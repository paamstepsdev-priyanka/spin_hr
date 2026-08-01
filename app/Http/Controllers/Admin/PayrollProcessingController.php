<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonth;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Services\CompanyScope;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollProcessingController extends Controller
{
    /**
     * Display the unified Payroll Processing Dashboard.
     */
    public function index(Request $request)
    {
        $selectedCompanyId = CompanyScope::id();
        $currentCompany = CompanyScope::currentCompany();

        // Calculate Financial Year (April - March)
        $todayYear = (int) date('Y');
        $todayMonth = (int) date('n');
        
        $defaultStartYear = ($todayMonth >= 4) ? $todayYear : ($todayYear - 1);
        $startYear = (int) $request->query('fy', $defaultStartYear);

        $fyLabel = "FY {$startYear}-" . substr((string)($startYear + 1), 2);
        $prevFyYear = $startYear - 1;
        $nextFyYear = $startYear + 1;

        // Build list of available Financial Years for dropdown
        $availableFys = [];
        for ($y = $defaultStartYear - 2; $y <= $defaultStartYear + 2; $y++) {
            $availableFys[$y] = "FY {$y}-" . substr((string)($y + 1), 2);
        }

        // Build 12 months array (April to March)
        $monthsGrid = [];
        $monthsOrder = [
            4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July',
            8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November',
            12 => 'December', 1 => 'January', 2 => 'February', 3 => 'March'
        ];

        foreach ($monthsOrder as $mNum => $mName) {
            $mYear = ($mNum >= 4) ? $startYear : ($startYear + 1);
            $monthsGrid[] = [
                'month' => $mNum,
                'year' => $mYear,
                'month_name' => $mName,
                'short_name' => strtoupper(substr($mName, 0, 3)),
                'key' => "{$mYear}_{$mNum}",
            ];
        }

        // Eager load Attendance and Payroll data for all 12 months (Zero N+1)
        $attendanceBatches = AttendanceMonth::forCurrentCompany()
            ->with(['creator'])
            ->where(function ($q) use ($startYear) {
                $q->where(function ($sub) use ($startYear) {
                    $sub->where('year', $startYear)->where('month', '>=', 4);
                })->orWhere(function ($sub) use ($startYear) {
                    $sub->where('year', $startYear + 1)->where('month', '<=', 3);
                });
            })
            ->get()
            ->keyBy(function ($item) {
                return "{$item->year}_{$item->month}";
            });

        $payrollBatches = Payroll::forCurrentCompany()
            ->with(['creator', 'details'])
            ->where(function ($q) use ($startYear) {
                $q->where(function ($sub) use ($startYear) {
                    $sub->where('year', $startYear)->where('month', '>=', 4);
                })->orWhere(function ($sub) use ($startYear) {
                    $sub->where('year', $startYear + 1)->where('month', '<=', 3);
                });
            })
            ->get()
            ->keyBy(function ($item) {
                return "{$item->year}_{$item->month}";
            });

        // Compute status for each of the 12 months based on sequential workflow logic
        $processedMonths = [];
        $attCompletedCount = 0;
        $payrollProcessedCount = 0;
        $inProgressCount = 0;
        $payslipGeneratedCount = 0;

        foreach ($monthsGrid as $mInfo) {
            $mKey = $mInfo['key'];
            $y = $mInfo['year'];
            $m = $mInfo['month'];

            $isCurrent = ($y === $todayYear && $m === $todayMonth);
            $isPast = ($y < $todayYear || ($y === $todayYear && $m < $todayMonth));
            $isFuture = ($y > $todayYear || ($y === $todayYear && $m > $todayMonth));

            $attRec = $attendanceBatches->get($mKey);
            $payRec = $payrollBatches->get($mKey);

            $hasAttendance = ($attRec !== null);
            $hasPayroll = ($payRec !== null);
            $hasPayslip = ($payRec !== null && $payRec->details->count() > 0);
            $isComplete = ($hasAttendance && $hasPayroll && $hasPayslip);

            // KPI Counts
            if ($hasAttendance) {
                $attCompletedCount++;
            }
            if ($hasPayroll) {
                $payrollProcessedCount++;
            }
            if ($hasPayslip) {
                $payslipGeneratedCount++;
            }

            // In Progress: current month or past active month where workflow is incomplete
            if (($isCurrent || $isPast) && !$isComplete && !$isFuture) {
                $inProgressCount++;
            }

            // Text statuses
            $attStatus = $hasAttendance ? 'Completed' : 'Pending';
            $payStatus = $hasPayroll ? 'Processed' : 'Pending';
            $payslipStatus = $hasPayslip ? 'Generated' : 'Pending';

            // Action flags
            $canGenerateAttendance = !$hasAttendance;
            $canGeneratePayroll = ($hasAttendance && !$hasPayroll);
            $canGeneratePayslip = ($hasPayroll && !$hasPayslip);
            $canViewDetails = $isComplete;

            // Lock opening date for future months
            $openDateFormatted = Carbon::createFromDate($y, $m, 1)->format('j F Y');

            $processedMonths[] = [
                'month' => $m,
                'year' => $y,
                'month_name' => $mInfo['month_name'],
                'short_name' => $mInfo['short_name'],
                'is_current' => $isCurrent,
                'is_past' => $isPast,
                'is_future' => $isFuture,
                'has_attendance' => $hasAttendance,
                'has_payroll' => $hasPayroll,
                'has_payslip' => $hasPayslip,
                'is_complete' => $isComplete,
                'att_status' => $attStatus,
                'pay_status' => $payStatus,
                'payslip_status' => $payslipStatus,
                'can_generate_attendance' => $canGenerateAttendance,
                'can_generate_payroll' => $canGeneratePayroll,
                'can_generate_payslip' => $canGeneratePayslip,
                'can_view_details' => $canViewDetails,
                'att_record' => $attRec,
                'pay_record' => $payRec,
                'open_date' => $openDateFormatted,
            ];
        }

        return view('Admin.PayrollProcessing.index', compact(
            'fyLabel',
            'startYear',
            'prevFyYear',
            'nextFyYear',
            'availableFys',
            'processedMonths',
            'attCompletedCount',
            'payrollProcessedCount',
            'inProgressCount',
            'payslipGeneratedCount',
            'currentCompany'
        ));
    }

    /**
     * Display detailed Payroll Processing view for a specific year and month.
     */
    public function show($year, $month)
    {
        $year = (int) $year;
        $month = (int) $month;

        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2099) {
            return redirect()->route('payroll-processing.index')->with('error', 'Invalid month or year requested.');
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        // Eager load Attendance Month master & details
        $attendanceMonth = AttendanceMonth::forCurrentCompany()
            ->with(['company', 'branch', 'creator', 'details.employee'])
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        // Eager load Payroll master & details
        $payroll = Payroll::forCurrentCompany()
            ->with(['company', 'branch', 'creator', 'details.employee.department'])
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        // Active employees of current company
        $employees = Employee::forCurrentCompany()
            ->with(['department', 'branch'])
            ->where('status', 'active')
            ->orderBy('employee_code', 'asc')
            ->get();

        // Attendance Summary Aggregates
        $attDetails = $attendanceMonth ? $attendanceMonth->details : collect();
        $attSummary = [
            'status' => $attendanceMonth ? 'Completed' : 'Pending',
            'generated_date' => $attendanceMonth && $attendanceMonth->created_at ? $attendanceMonth->created_at->format('d/m/Y h:i A') : 'N/A',
            'generated_by' => $attendanceMonth && $attendanceMonth->creator ? $attendanceMonth->creator->name : 'N/A',
            'total_employees' => $attDetails->count(),
            'present_days' => $attDetails->sum('net_present'),
            'absent_days' => $attDetails->sum(function ($d) {
                return max(0, (float)$d->total_days - (float)$d->payable_days);
            }),
            'leave_days' => $attDetails->sum('leave_taken'),
            'payable_days' => $attDetails->sum('payable_days'),
        ];

        // Payroll Summary Aggregates
        $payDetails = $payroll ? $payroll->details->keyBy('employee_id') : collect();
        $paySummary = [
            'status' => $payroll ? $payroll->status : 'Pending',
            'generated_date' => $payroll && $payroll->created_at ? $payroll->created_at->format('d/m/Y h:i A') : 'N/A',
            'generated_by' => $payroll && $payroll->creator ? $payroll->creator->name : 'N/A',
            'employees_processed' => $payDetails->count(),
            'gross_salary' => $payroll ? (float)$payroll->total_gross_salary : 0.0,
            'total_deduction' => $payroll ? (float)$payroll->total_deduction : 0.0,
            'net_salary' => $payroll ? (float)$payroll->total_net_salary : 0.0,
        ];

        // Salary Slip Summary Aggregates
        $totalEmployeesCount = $employees->count();
        $slipsGeneratedCount = $payDetails->count();
        $slipsPendingCount = max(0, $totalEmployeesCount - $slipsGeneratedCount);

        $slipSummary = [
            'generated' => $slipsGeneratedCount,
            'pending' => $slipsPendingCount,
            'total' => $totalEmployeesCount,
        ];

        // Employee Table Breakdown
        $attDetailsKeyed = $attDetails->keyBy('employee_id');
        $employeeRows = [];

        foreach ($employees as $emp) {
            $attItem = $attDetailsKeyed->get($emp->id);
            $payItem = $payDetails->get($emp->id);

            $employeeRows[] = [
                'employee_id' => $emp->id,
                'employee_code' => $emp->employee_code ?? 'N/A',
                'name' => $emp->name ?? 'N/A',
                'department_name' => $emp->department ? $emp->department->name : 'N/A',
                'attendance_status' => $attItem ? 'Completed' : 'Pending',
                'payable_days' => $attItem ? $attItem->payable_days : '-',
                'payroll_status' => $payItem ? ($payItem->status ?? 'Generated') : 'Pending',
                'salary_slip_status' => $payItem ? 'Generated' : 'Pending',
                'payroll_detail_id' => $payItem ? $payItem->id : null,
            ];
        }

        return view('Admin.PayrollProcessing.show', compact(
            'year',
            'month',
            'monthName',
            'attendanceMonth',
            'payroll',
            'attSummary',
            'paySummary',
            'slipSummary',
            'employeeRows'
        ));
    }
}
