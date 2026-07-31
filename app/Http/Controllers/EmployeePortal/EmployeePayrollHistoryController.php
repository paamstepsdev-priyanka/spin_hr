<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use App\Models\PayrollDetail;
use Illuminate\Http\Request;

class EmployeePayrollHistoryController extends Controller
{
    /**
     * Display logged-in employee payroll history.
     */
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'Unauthorized access.');
        }

        $payrolls = PayrollDetail::where('employee_id', $employee->id)
            ->with(['payroll.company', 'payroll.branch'])
            ->latest('id')
            ->paginate(10);

        return view('Employee.payroll_history', compact('employee', 'payrolls'));
    }
}
