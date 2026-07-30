<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceBatch;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    /**
     * Display attendance report filter page.
     */
    public function index()
    {
        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');

        return view('Admin.AttendanceReport.index', compact('companies', 'currentMonth', 'currentYear'));
    }

    /**
     * Get branches for a specific company (AJAX).
     */
    public function getBranches($companyId)
    {
        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($branches);
    }

    /**
     * Get active employees for a specific branch (AJAX).
     */
    public function getEmployees($branchId)
    {
        $employees = Employee::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('employee_code', 'asc')
            ->get(['id', 'employee_code', 'name']);

        return response()->json($employees);
    }

    /**
     * Generate monthly attendance report.
     */
    public function report(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
        ], [
            'company_id.required' => 'Company is required.',
            'branch_id.required' => 'Branch is required.',
            'employee_id.required' => 'Employee is required.',
            'month.required' => 'Month is required.',
            'year.required' => 'Year is required.',
        ]);

        $employee = Employee::with(['company', 'branch', 'department'])->findOrFail($request->employee_id);
        $month = (int) $request->month;
        $year = (int) $request->year;

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, $daysInMonth)->format('Y-m-d');

        // Fetch attendance records joined with attendance_batches for this employee & date range
        $attendances = Attendance::select('attendances.*', 'attendance_batches.attendance_date')
            ->join('attendance_batches', 'attendances.attendance_batch_id', '=', 'attendance_batches.id')
            ->where('attendances.employee_id', $employee->id)
            ->whereBetween('attendance_batches.attendance_date', [$startDate, $endDate])
            ->whereNull('attendance_batches.deleted_at')
            ->whereNull('attendances.deleted_at')
            ->get()
            ->keyBy('attendance_date');

        $dailyLogs = [];
        $summary = [
            'total_calendar_days' => $daysInMonth,
            'working_days' => 0,
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'half_day' => 0,
            'holiday' => 0,
            'not_marked' => 0,
            'total_working_hours' => 0,
            'overtime_hours' => 0,
            'payable_days' => 0.0,
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateObj = Carbon::createFromDate($year, $month, $day);
            $dateStr = $dateObj->format('Y-m-d');
            $formattedDate = $dateObj->format('d-M-Y');
            $dayName = $dateObj->format('D');
            $isSunday = $dateObj->isSunday();

            $att = $attendances->get($dateStr);

            $status = 'Not Marked';
            $checkIn = '-';
            $checkOut = '-';
            $workingHoursStr = '-';
            $remarks = '-';
            $workingHoursNum = 0.0;

            if ($att) {
                $status = $att->attendance_status;
                $remarks = !empty($att->remarks) ? $att->remarks : '-';

                if ($att->check_in) {
                    $checkIn = date('h:i A', strtotime($att->check_in));
                }
                if ($att->check_out) {
                    $checkOut = date('h:i A', strtotime($att->check_out));
                }

                if ($att->check_in && $att->check_out) {
                    $t1 = strtotime($att->check_in);
                    $t2 = strtotime($att->check_out);
                    if ($t2 > $t1) {
                        $diffSeconds = $t2 - $t1;
                        $hours = floor($diffSeconds / 3600);
                        $mins = floor(($diffSeconds % 3600) / 60);
                        $workingHoursNum = round($diffSeconds / 3600, 2);
                        $workingHoursStr = sprintf('%02dh %02dm', $hours, $mins);
                    }
                }
            }

            // Summary counts
            if ($status === 'Present') {
                $summary['present']++;
                $summary['working_days']++;
            } elseif ($status === 'Absent') {
                $summary['absent']++;
                $summary['working_days']++;
            } elseif ($status === 'Leave') {
                $summary['leave']++;
                $summary['working_days']++;
            } elseif ($status === 'Half Day') {
                $summary['half_day']++;
                $summary['working_days']++;
            } elseif ($status === 'Holiday') {
                $summary['holiday']++;
            } else {
                $summary['not_marked']++;
                if (!$isSunday) {
                    $summary['working_days']++;
                } else {
                    $summary['holiday']++; // Sunday defaults to holiday if not marked
                }
            }

            $summary['total_working_hours'] += $workingHoursNum;

            $dailyLogs[] = [
                'date' => $formattedDate,
                'day' => $dayName,
                'status' => $status,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'working_hours' => $workingHoursStr,
                'remarks' => $remarks,
            ];
        }

        // Summary calculations
        $summary['total_working_hours_formatted'] = sprintf('%.1f Hours', $summary['total_working_hours']);
        $summary['overtime_hours_formatted'] = '0 Hours';

        // Formula: Present + Leave + Holiday + (Half Day * 0.5)
        $summary['payable_days'] = $summary['present'] + $summary['leave'] + $summary['holiday'] + ($summary['half_day'] * 0.5);

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $html = view('Admin.AttendanceReport.report', compact('employee', 'month', 'year', 'monthName', 'dailyLogs', 'summary'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
        ]);
    }
}
