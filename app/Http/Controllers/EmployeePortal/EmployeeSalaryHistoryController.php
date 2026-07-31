<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;

class EmployeeSalaryHistoryController extends Controller
{
    /**
     * Display logged-in employee salary history.
     */
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'Unauthorized access.');
        }

        $salaries = EmployeeSalary::where('employee_id', $employee->id)
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('Employee.salary_history', compact('employee', 'salaries'));
    }
}
