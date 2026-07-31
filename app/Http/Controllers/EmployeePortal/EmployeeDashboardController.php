<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonthDetail;
use App\Models\EmployeeSalary;
use App\Models\PayrollDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    /**
     * Render the Employee Self-Service (ESS) Dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee()
            ->with(['company', 'branch', 'department'])
            ->firstOrFail();

        $employeeId = $employee->id;
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        $currentDate = date('d M Y');
        $currentMonthName = date('F Y');

        // Current Month Attendance Detail
        $currentAttendance = AttendanceMonthDetail::where('employee_id', $employeeId)
            ->whereHas('attendanceMonth', function ($q) use ($currentMonth, $currentYear) {
                $q->where('month', $currentMonth)->where('year', $currentYear);
            })
            ->with('attendanceMonth')
            ->first();

        // Current Active Salary Configuration
        $firstDate = Carbon::createFromDate($currentYear, $currentMonth, 1)->startOfMonth()->toDateString();
        $lastDate = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->toDateString();

        $currentSalary = EmployeeSalary::where('employee_id', $employeeId)
            ->where('status', 'active')
            ->where('effective_from', '<=', $lastDate)
            ->where(function ($sub) use ($firstDate) {
                $sub->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $firstDate);
            })
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // Fallback to latest active salary if not matched to current month exact dates
        if (!$currentSalary) {
            $currentSalary = EmployeeSalary::where('employee_id', $employeeId)
                ->where('status', 'active')
                ->latest('id')
                ->first();
        }

        // Latest Generated Payroll Detail
        $latestPayroll = PayrollDetail::where('employee_id', $employeeId)
            ->with('payroll')
            ->latest('id')
            ->first();

        return view('Employee.dashboard', compact(
            'employee',
            'currentDate',
            'currentMonthName',
            'currentAttendance',
            'currentSalary',
            'latestPayroll'
        ));
    }
}
