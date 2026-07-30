<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonth;
use App\Models\AttendanceMonthDetail;
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

        // Fetch monthly attendance summary if available
        $attendanceMonth = AttendanceMonth::where('company_id', $request->company_id)
            ->where('branch_id', $request->branch_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $detail = null;
        if ($attendanceMonth) {
            $detail = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
                ->where('employee_id', $employee->id)
                ->first();
        }

        $summary = [
            'total_calendar_days' => $daysInMonth,
            'working_days' => $daysInMonth,
            'present' => $detail ? (float)$detail->present_days : 0,
            'absent' => $detail ? (float)$detail->absent_days : 0,
            'leave' => $detail ? (float)$detail->paid_leave : 0,
            'lwp' => $detail ? (float)$detail->lwp : 0,
            'half_day' => $detail ? (float)$detail->half_days : 0,
            'holiday' => $detail ? (float)$detail->holidays : 0,
            'overtime_hours' => $detail ? (float)$detail->overtime_hours : 0,
            'overtime_amount' => $detail ? (float)$detail->overtime_amount : 0,
            'payable_days' => $detail ? (float)$detail->payable_days : 0,
        ];

        $summary['total_working_hours_formatted'] = '-';
        $summary['overtime_hours_formatted'] = sprintf('%.1f Hours', $summary['overtime_hours']);

        $dailyLogs = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateObj = Carbon::createFromDate($year, $month, $day);
            $dailyLogs[] = [
                'date' => $dateObj->format('d-M-Y'),
                'day' => $dateObj->format('D'),
                'status' => $detail ? 'Monthly Recorded' : 'Not Marked',
                'check_in' => '-',
                'check_out' => '-',
                'working_hours' => '-',
                'remarks' => $detail ? ($detail->remarks ?? '-') : '-',
            ];
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $html = view('Admin.AttendanceReport.report', compact('employee', 'month', 'year', 'monthName', 'dailyLogs', 'summary'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
        ]);
    }
}
