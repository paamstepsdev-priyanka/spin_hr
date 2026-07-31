<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use App\Models\PayrollDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeePayslipController extends Controller
{
    /**
     * Display logged-in employee payslips list.
     */
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'Unauthorized access.');
        }

        $payslips = PayrollDetail::where('employee_id', $employee->id)
            ->with(['payroll.company', 'payroll.branch'])
            ->latest('id')
            ->paginate(10);

        return view('Employee.payslips.index', compact('employee', 'payslips'));
    }

    /**
     * Display detailed A4 Salary Slip view for an employee.
     */
    public function show($detailId)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'Unauthorized access.');
        }

        $detail = PayrollDetail::with([
            'payroll.company',
            'payroll.branch',
            'employee.department'
        ])->findOrFail($detailId);

        // Security check: Never trust URL parameter, strictly validate employee ownership
        if ((int)$detail->employee_id !== (int)$employee->id) {
            abort(403, 'Forbidden: You are not authorized to view another employee\'s salary slip.');
        }

        $monthName = Carbon::createFromDate($detail->payroll->year, $detail->payroll->month, 1)->format('F');

        return view('Employee.payslips.show', compact('detail', 'monthName'));
    }

    /**
     * Download / Printable A4 Portrait PDF view for Salary Slip.
     */
    public function downloadPdf($detailId)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'Unauthorized access.');
        }

        $detail = PayrollDetail::with([
            'payroll.company',
            'payroll.branch',
            'employee.department'
        ])->findOrFail($detailId);

        // Security check: Validate employee ownership
        if ((int)$detail->employee_id !== (int)$employee->id) {
            abort(403, 'Forbidden: You are not authorized to download another employee\'s salary slip.');
        }

        $monthName = Carbon::createFromDate($detail->payroll->year, $detail->payroll->month, 1)->format('F');

        return view('Employee.payslips.pdf', compact('detail', 'monthName'));
    }
}
