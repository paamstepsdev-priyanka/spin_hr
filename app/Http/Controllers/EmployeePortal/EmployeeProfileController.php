<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonthDetail;
use App\Models\EmployeeSalary;
use App\Models\PayrollDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{
    /**
     * Display logged-in employee profile.
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee()
            ->with(['company', 'branch', 'department'])
            ->firstOrFail();

        // Calculate Experience
        $experience = 'N/A';
        if ($employee->joining_date) {
            $joiningDate = Carbon::parse($employee->joining_date);
            $now = Carbon::now();
            $diff = $joiningDate->diff($now);

            $parts = [];
            if ($diff->y > 0) {
                $parts[] = $diff->y . ' ' . ($diff->y === 1 ? 'Year' : 'Years');
            }
            if ($diff->m > 0) {
                $parts[] = $diff->m . ' ' . ($diff->m === 1 ? 'Month' : 'Months');
            }
            if (empty($parts)) {
                $parts[] = 'Less than a month';
            }
            $experience = implode(' ', $parts);
        }

        // Calculate Profile Completion %
        $completionFields = [
            'name', 'email', 'mobile', 'dob', 'gender', 'marital_status',
            'address_line1', 'city', 'state', 'zip_code', 'pan_no', 'aadhar_no',
            'bank_name', 'account_no', 'ifsc_code', 'contact_person_name',
            'primary_phone', 'photo'
        ];
        $filledCount = 0;
        foreach ($completionFields as $field) {
            if (!empty($employee->{$field})) {
                $filledCount++;
            }
        }
        $completionPercentage = round(($filledCount / count($completionFields)) * 100);
        $profileStatus = $completionPercentage >= 80 ? 'Complete' : 'Incomplete';

        return view('Employee.profile', compact(
            'employee',
            'experience',
            'completionPercentage',
            'profileStatus'
        ));
    }

    /**
     * Download / Printable Profile PDF view.
     */
    public function downloadPdf(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee()
            ->with(['company', 'branch', 'department'])
            ->firstOrFail();

        $employeeId = $employee->id;
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        // Current Active Salary
        $currentSalary = EmployeeSalary::where('employee_id', $employeeId)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        // Latest Attendance
        $latestAttendance = AttendanceMonthDetail::where('employee_id', $employeeId)
            ->with('attendanceMonth')
            ->latest('id')
            ->first();

        // Latest Payroll
        $latestPayroll = PayrollDetail::where('employee_id', $employeeId)
            ->with('payroll')
            ->latest('id')
            ->first();

        // Experience
        $experience = 'N/A';
        if ($employee->joining_date) {
            $joiningDate = Carbon::parse($employee->joining_date);
            $diff = $joiningDate->diff(Carbon::now());
            $experience = ($diff->y > 0 ? $diff->y . ' Yrs ' : '') . ($diff->m > 0 ? $diff->m . ' Mos' : '0 Mos');
        }

        return view('Employee.profile_pdf', compact(
            'employee',
            'experience',
            'currentSalary',
            'latestAttendance',
            'latestPayroll'
        ));
    }
}
