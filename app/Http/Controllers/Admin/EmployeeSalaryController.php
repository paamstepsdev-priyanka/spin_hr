<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Services\CompanyScope;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class EmployeeSalaryController extends Controller
{
    /**
     * Authorize that the target employee belongs to active company scope.
     */
    protected function authorizeEmployeeCompany(Employee $employee): void
    {
        if (CompanyScope::id() !== null && (int) $employee->company_id !== CompanyScope::id()) {
            abort(403, 'Unauthorized company access.');
        }
    }

    /**
     * Display a listing of the salary records for the specified employee.
     */
    public function index(Request $request, Employee $employee)
    {
        $this->authorizeEmployeeCompany($employee);

        if ($request->ajax()) {
            $salaries = $employee->salaries()->orderBy('effective_from', 'desc')->orderBy('id', 'desc');

            return DataTables::of($salaries)
                ->addIndexColumn()
                ->editColumn('basic_salary', function ($row) {
                    return '₹ ' . number_format($row->basic_salary, 2);
                })
                ->editColumn('gross_salary', function ($row) {
                    return '<strong class="text-success">₹ ' . number_format($row->gross_salary, 2) . '</strong>';
                })
                ->editColumn('total_deduction', function ($row) {
                    return '<span class="text-danger">₹ ' . number_format($row->total_deduction, 2) . '</span>';
                })
                ->editColumn('net_salary', function ($row) {
                    return '<strong class="text-primary">₹ ' . number_format($row->net_salary, 2) . '</strong>';
                })
                ->editColumn('effective_from', function ($row) {
                    return $row->effective_from ? date('d/m/Y', strtotime($row->effective_from)) : '-';
                })
                ->editColumn('effective_to', function ($row) {
                    return $row->effective_to ? date('d/m/Y', strtotime($row->effective_to)) : '<span class="badge bg-light text-dark border">Present</span>';
                })

                ->editColumn('status', function ($row) {
                    $badgeClass = strtolower($row->status) === 'active' ? 'bg-warning text-dark' : 'bg-danger';
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('edit', function ($row) use ($employee) {
                    return '<a href="' . route('employees.salaries.edit', [$employee->id, $row->id]) . '" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>';
                })
                ->addColumn('delete', function ($row) use ($employee) {
                    return '<button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="' . route('employees.salaries.destroy', [$employee->id, $row->id]) . '" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>';
                })

                ->rawColumns(['gross_salary', 'total_deduction', 'net_salary', 'effective_to', 'status', 'edit', 'delete'])
                ->make(true);
        }

        return view('Admin.Employee.Salary.index', compact('employee'));
    }

    /**
     * Show the form for creating a new salary record.
     */
    public function create(Employee $employee): View
    {
        $this->authorizeEmployeeCompany($employee);
        return view('Admin.Employee.Salary.create', compact('employee'));
    }

    /**
     * Store a newly created salary record in storage.
     */
    public function store(Request $request, Employee $employee)
    {
        $this->authorizeEmployeeCompany($employee);

        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'variable_allowance' => 'nullable|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'employee_pf' => 'nullable|numeric|min:0',
            'esi' => 'nullable|numeric|min:0',
            'professional_tax' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'status' => 'required',
        ], [
            'basic_salary.required' => 'Basic salary is required.',
            'basic_salary.numeric' => 'Basic salary must be a number.',
            'variable_allowance.numeric' => 'Variable allowance must be a number.',
            'hra.numeric' => 'HRA must be a number.',
            'effective_from.required' => 'Effective from date is required.',
            'effective_from.date' => 'Enter a valid effective from date.',
            'effective_to.after_or_equal' => 'Effective to date must be equal to or after effective from date.',
            'status.required' => 'Select status.',
        ]);

        $salary = new EmployeeSalary();

        $salary->employee_id = $employee->id;
        $salary->fill($request->all());

        // Controller Recalculation
        $grossSalary = (float)$salary->basic_salary +
            (float)$salary->variable_allowance +
            (float)$salary->hra +
            (float)$salary->conveyance_allowance +
            (float)$salary->medical_allowance +
            (float)$salary->special_allowance +
            (float)$salary->other_allowance;

        $totalDeduction = (float)$salary->employee_pf +
            (float)$salary->esi +
            (float)$salary->professional_tax +
            (float)$salary->tds +
            (float)$salary->other_deduction;

        $netSalary = $grossSalary - $totalDeduction;

        $salary->gross_salary = $grossSalary;
        $salary->total_deduction = $totalDeduction;
        $salary->net_salary = $netSalary;

        $salary->save();

        session()->flash('success', 'Salary details saved successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Salary details saved successfully.',
            'redirect' => route('employees.salaries.index', $employee->id)
        ]);
    }

    /**
     * Show the form for editing the specified salary record.
     */
    public function edit(Employee $employee, EmployeeSalary $salary): View
    {
        $this->authorizeEmployeeCompany($employee);
        return view('Admin.Employee.Salary.edit', compact('employee', 'salary'));
    }

    /**
     * Update the specified salary record in storage.
     */
    public function update(Request $request, Employee $employee, EmployeeSalary $salary)
    {
        $this->authorizeEmployeeCompany($employee);

        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'variable_allowance' => 'nullable|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'employee_pf' => 'nullable|numeric|min:0',
            'esi' => 'nullable|numeric|min:0',
            'professional_tax' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'status' => 'required',
        ], [
            'basic_salary.required' => 'Basic salary is required.',
            'basic_salary.numeric' => 'Basic salary must be a number.',
            'variable_allowance.numeric' => 'Variable allowance must be a number.',
            'hra.numeric' => 'HRA must be a number.',
            'effective_from.required' => 'Effective from date is required.',
            'effective_from.date' => 'Enter a valid effective from date.',
            'effective_to.after_or_equal' => 'Effective to date must be equal to or after effective from date.',
            'status.required' => 'Select status.',
        ]);

        $salary->update($request->all());

        // Controller Recalculation
        $grossSalary = (float)$salary->basic_salary +
            (float)$salary->variable_allowance +
            (float)$salary->hra +
            (float)$salary->conveyance_allowance +
            (float)$salary->medical_allowance +
            (float)$salary->special_allowance +
            (float)$salary->other_allowance;

        $totalDeduction = (float)$salary->employee_pf +
            (float)$salary->esi +
            (float)$salary->professional_tax +
            (float)$salary->tds +
            (float)$salary->other_deduction;

        $netSalary = $grossSalary - $totalDeduction;

        $salary->gross_salary = $grossSalary;
        $salary->total_deduction = $totalDeduction;
        $salary->net_salary = $netSalary;

        $salary->save();

        session()->flash('success', 'Salary details updated successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Salary details updated successfully.',
            'redirect' => route('employees.salaries.index', $employee->id)
        ]);
    }

    /**
     * Remove the specified salary record from storage.
     */
    public function destroy(Employee $employee, EmployeeSalary $salary)
    {
        $this->authorizeEmployeeCompany($employee);

        $salary->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Salary record deleted successfully.'
            ]);
        }

        return redirect()->route('employees.salaries.index', $employee->id)->with('success', 'Salary record deleted successfully.');
    }
}
