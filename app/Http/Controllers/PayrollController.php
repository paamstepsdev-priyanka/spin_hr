<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMonth;
use App\Models\AttendanceMonthDetail;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends Controller
{
    /**
     * Display a listing of generated payroll batches (Yajra DataTables).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Payroll::with(['company', 'branch', 'creator'])
                ->withCount('details')
                ->when($request->filled('company_id'), function ($q) use ($request) {
                    return $q->where('company_id', $request->company_id);
                })
                ->when($request->filled('branch_id'), function ($q) use ($request) {
                    return $q->where('branch_id', $request->branch_id);
                })
                ->when($request->filled('month'), function ($q) use ($request) {
                    return $q->where('month', $request->month);
                })
                ->when($request->filled('year'), function ($q) use ($request) {
                    return $q->where('year', $request->year);
                })
                ->when($request->filled('status'), function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->company ? e($row->company->name) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? e($row->branch->name) : '<span class="text-muted">N/A</span>';
                })
                ->editColumn('month', function ($row) {
                    return Carbon::createFromDate($row->year, $row->month, 1)->format('F');
                })
                ->editColumn('year', function ($row) {
                    return $row->year;
                })
                ->addColumn('employees_count', function ($row) {
                    return '<span class="badge bg-secondary px-2 py-1">' . $row->details_count . '</span>';
                })
                ->addColumn('total_net_salary', function ($row) {
                    return '<strong class="text-success">₹ ' . number_format($row->total_net_salary, 2) . '</strong>';
                })
                ->addColumn('status', function ($row) {
                    $badgeClass = match ($row->status) {
                        'Draft' => 'bg-secondary',
                        'Generated' => 'bg-success',
                        'Locked' => 'bg-warning text-dark',
                        'Paid' => 'bg-info text-dark',
                        default => 'bg-primary'
                    };
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . e($row->status) . '</span>';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator ? e($row->creator->name) : '<span class="text-muted">System</span>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y h:i A') : '-';
                })
                ->addColumn('action', function ($row) {
                    $viewBtn = '<a href="' . route('payrolls.show', $row->id) . '" class="btn btn-xs btn-outline-info py-0 px-1 me-1" title="View Payroll">
                                    <i class="bi bi-eye"></i>
                                </a>';

                    if (in_array($row->status, ['Draft', 'Generated'])) {
                        $deleteBtn = '<button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete" data-url="' . route('payrolls.destroy', $row->id) . '" title="Delete Payroll">
                                        <i class="bi bi-trash"></i>
                                    </button>';
                    } else {
                        $deleteBtn = '<button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 me-1" disabled title="Locked/Paid payrolls cannot be deleted">
                                        <i class="bi bi-trash"></i>
                                    </button>';
                    }

                    return '<div class="d-flex justify-content-center align-items-center">' . $viewBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['company_name', 'branch_name', 'employees_count', 'total_net_salary', 'status', 'created_by', 'created_at', 'action'])
                ->make(true);
        }

        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        $statuses = ['Draft', 'Generated', 'Locked', 'Paid'];

        return view('Admin.Payroll.index', compact('companies', 'branches', 'months', 'statuses'));
    }

    /**
     * Show form to generate monthly payroll.
     */
    public function create(Request $request)
    {
        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $currentMonth = (int) $request->query('month', date('n'));
        $currentYear = (int) $request->query('year', date('Y'));
        $selectedCompanyId = $request->query('company_id');
        $selectedBranchId = $request->query('branch_id');

        return view('Admin.Payroll.create', compact('companies', 'branches', 'months', 'currentMonth', 'currentYear', 'selectedCompanyId', 'selectedBranchId'));
    }

    /**
     * Load employees, active salary, and attendance data for payroll preview (AJAX).
     */
    public function loadEmployees(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
        ], [
            'company_id.required' => 'Please select company.',
            'branch_id.required' => 'Please select branch.',
            'month.required' => 'Please select month.',
            'year.required' => 'Please select year.',
        ]);

        $companyId = (int) $request->company_id;
        $branchId = (int) $request->branch_id;
        $month = (int) $request->month;
        $year = (int) $request->year;

        // Check 1: Check if Payroll already generated for this Company, Branch, Month, Year
        $existingPayroll = Payroll::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingPayroll) {
            return response()->json([
                'status' => false,
                'message' => 'Payroll already generated for selected Company, Branch, Month and Year.',
            ], 422);
        }

        // Check 2: Fetch Monthly Attendance record
        $attendanceMonth = AttendanceMonth::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (!$attendanceMonth) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance Not Available for selected Company, Branch, Month and Year.',
            ], 422);
        }

        // Fetch attendance details indexed by employee_id
        $attendanceDetails = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
            ->get()
            ->keyBy('employee_id');

        if ($attendanceDetails->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance Not Available (No detailed attendance records found).',
            ], 422);
        }

        $firstDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $lastDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        // Fetch active employees of selected company & branch with their valid active salary for the payroll month
        $employees = Employee::with(['department', 'branch'])
            ->with(['salaries' => function ($q) use ($firstDate, $lastDate) {
                $q->where('status', 'active')
                  ->where('effective_from', '<=', $lastDate)
                  ->where(function ($sub) use ($firstDate) {
                      $sub->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', $firstDate);
                  })
                  ->orderBy('effective_from', 'desc')
                  ->orderBy('id', 'desc');
            }])
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('employee_code', 'asc')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active employees found for the selected company and branch.',
            ], 422);
        }

        $records = [];
        $hasErrors = false;

        foreach ($employees as $emp) {
            $salaryConfig = $emp->salaries->first();
            $attDetail = $attendanceDetails->get($emp->id);

            $hasSalary = !is_null($salaryConfig);
            $hasAttendance = !is_null($attDetail);

            if (!$hasSalary || !$hasAttendance) {
                $hasErrors = true;
            }

            // Calculation pipeline (Server side)
            $basicSalary = $hasSalary ? (float) $salaryConfig->basic_salary : 0;
            $hra = $hasSalary ? (float) $salaryConfig->hra : 0;
            $conveyanceAllowance = $hasSalary ? (float) $salaryConfig->conveyance_allowance : 0;
            $medicalAllowance = $hasSalary ? (float) $salaryConfig->medical_allowance : 0;
            $specialAllowance = $hasSalary ? (float) $salaryConfig->special_allowance : 0;
            $otherAllowance = $hasSalary ? (float) $salaryConfig->other_allowance : 0;
            $variableAllowance = $hasSalary ? (float) $salaryConfig->variable_allowance : 0;
            $grossSalary = $hasSalary ? (float) $salaryConfig->gross_salary : 0;

            $totalDays = $hasAttendance ? (float) $attDetail->total_days : 0;
            $leaveTaken = $hasAttendance ? (float) $attDetail->leave_taken : 0;
            $netPresent = $hasAttendance ? (float) $attDetail->net_present : 0;
            $leaveNotDeducted = $hasAttendance ? (float) $attDetail->leave_not_deducted : 0;
            $payableDays = $hasAttendance ? (float) $attDetail->payable_days : 0;

            $perDaySalary = ($hasSalary && $totalDays > 0) ? round($grossSalary / $totalDays, 2) : 0;
            $earnedSalary = round($perDaySalary * $payableDays, 2);

            $employeePf = $hasSalary ? (float) $salaryConfig->employee_pf : 0;
            $esi = $hasSalary ? (float) $salaryConfig->esi : 0;
            $professionalTax = $hasSalary ? (float) $salaryConfig->professional_tax : 0;
            $tds = $hasSalary ? (float) $salaryConfig->tds : 0;
            $otherDeduction = $hasSalary ? (float) $salaryConfig->other_deduction : 0;
            $totalDeduction = round($employeePf + $esi + $professionalTax + $tds + $otherDeduction, 2);

            $netSalary = round($earnedSalary - $totalDeduction, 2);

            $records[] = [
                'employee_id' => $emp->id,
                'employee_code' => $emp->employee_code ?? 'N/A',
                'name' => $emp->name ?? 'N/A',
                'department_name' => $emp->department ? $emp->department->name : 'N/A',
                'has_salary' => $hasSalary,
                'has_attendance' => $hasAttendance,
                'basic_salary' => $basicSalary,
                'gross_salary' => $grossSalary,
                'total_days' => $totalDays,
                'leave_taken' => $leaveTaken,
                'net_present' => $netPresent,
                'leave_not_deducted' => $leaveNotDeducted,
                'payable_days' => $payableDays,
                'per_day_salary' => $perDaySalary,
                'earned_salary' => $earnedSalary,
                'total_deduction' => $totalDeduction,
                'net_salary' => $netSalary,
            ];
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $html = view('Admin.Payroll.table', compact(
            'records',
            'hasErrors',
            'companyId',
            'branchId',
            'month',
            'year',
            'monthName'
        ))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'has_errors' => $hasErrors,
            'message' => $hasErrors ? 'Cannot generate payroll because one or more employees have missing or expired salary configuration.' : 'Employees loaded for payroll generation.',
        ]);
    }

    /**
     * Store payroll master and employee snapshot detail records using manual property assignments.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
        ], [
            'company_id.required' => 'Please select company.',
            'branch_id.required' => 'Please select branch.',
            'month.required' => 'Please select month.',
            'year.required' => 'Please select year.',
        ]);

        $companyId = (int) $request->company_id;
        $branchId = (int) $request->branch_id;
        $month = (int) $request->month;
        $year = (int) $request->year;

        // Check duplicate
        $existingPayroll = Payroll::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingPayroll) {
            return response()->json([
                'status' => false,
                'message' => 'Payroll already generated for selected Company, Branch, Month and Year.',
            ], 422);
        }

        // Fetch Attendance
        $attendanceMonth = AttendanceMonth::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (!$attendanceMonth) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance Not Available for selected Company, Branch, Month and Year.',
            ], 422);
        }

        $attendanceDetails = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
            ->get()
            ->keyBy('employee_id');

        $firstDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $lastDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        // Fetch Active Employees with valid active salary for the payroll month
        $employees = Employee::with(['salaries' => function ($q) use ($firstDate, $lastDate) {
            $q->where('status', 'active')
              ->where('effective_from', '<=', $lastDate)
              ->where(function ($sub) use ($firstDate) {
                  $sub->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $firstDate);
              })
              ->orderBy('effective_from', 'desc')
              ->orderBy('id', 'desc');
        }])
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active employees found for payroll generation.',
            ], 422);
        }

        // Strict Server-Side Validation: Ensure no employee missing salary or attendance
        foreach ($employees as $emp) {
            if (!$emp->salaries->first()) {
                return response()->json([
                    'status' => false,
                    'message' => "Salary Not Configured for employee {$emp->name} ({$emp->employee_code}). Cannot generate payroll.",
                ], 422);
            }

            if (!$attendanceDetails->has($emp->id)) {
                return response()->json([
                    'status' => false,
                    'message' => "Attendance Not Available for employee {$emp->name} ({$emp->employee_code}). Cannot generate payroll.",
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            // Create Payroll Master using Manual Model Property Assignment
            $payroll = new Payroll();
            $payroll->company_id = $companyId;
            $payroll->branch_id = $branchId;
            $payroll->month = $month;
            $payroll->year = $year;
            $payroll->total_gross_salary = 0;
            $payroll->total_deduction = 0;
            $payroll->total_net_salary = 0;
            $payroll->status = 'Generated';
            $payroll->created_by = auth()->id();
            $payroll->save();

            $batchGross = 0;
            $batchDeduction = 0;
            $batchNet = 0;

            foreach ($employees as $emp) {
                $salaryConfig = $emp->salaries->first();
                $attDetail = $attendanceDetails->get($emp->id);

                $basicSalary = (float) $salaryConfig->basic_salary;
                $hra = (float) $salaryConfig->hra;
                $conveyanceAllowance = (float) $salaryConfig->conveyance_allowance;
                $medicalAllowance = (float) $salaryConfig->medical_allowance;
                $specialAllowance = (float) $salaryConfig->special_allowance;
                $otherAllowance = (float) $salaryConfig->other_allowance;
                $variableAllowance = (float) $salaryConfig->variable_allowance;
                $grossSalary = (float) $salaryConfig->gross_salary;

                $totalDays = (float) $attDetail->total_days;
                $leaveTaken = (float) $attDetail->leave_taken;
                $netPresent = (float) $attDetail->net_present;
                $leaveNotDeducted = (float) $attDetail->leave_not_deducted;
                $payableDays = (float) $attDetail->payable_days;

                $perDaySalary = ($totalDays > 0) ? round($grossSalary / $totalDays, 2) : 0;
                $earnedSalary = round($perDaySalary * $payableDays, 2);

                $employeePf = (float) $salaryConfig->employee_pf;
                $esi = (float) $salaryConfig->esi;
                $professionalTax = (float) $salaryConfig->professional_tax;
                $tds = (float) $salaryConfig->tds;
                $otherDeduction = (float) $salaryConfig->other_deduction;
                $totalDeduction = round($employeePf + $esi + $professionalTax + $tds + $otherDeduction, 2);

                $netSalary = round($earnedSalary - $totalDeduction, 2);

                // Create Payroll Detail using Manual Model Property Assignment
                $detail = new PayrollDetail();
                $detail->payroll_id = $payroll->id;
                $detail->employee_id = $emp->id;

                $detail->basic_salary = $basicSalary;
                $detail->hra = $hra;
                $detail->conveyance_allowance = $conveyanceAllowance;
                $detail->medical_allowance = $medicalAllowance;
                $detail->special_allowance = $specialAllowance;
                $detail->other_allowance = $otherAllowance;
                $detail->variable_allowance = $variableAllowance;
                $detail->gross_salary = $grossSalary;

                $detail->total_days = $totalDays;
                $detail->leave_taken = $leaveTaken;
                $detail->net_present = $netPresent;
                $detail->leave_not_deducted = $leaveNotDeducted;
                $detail->payable_days = $payableDays;

                $detail->per_day_salary = $perDaySalary;
                $detail->earned_salary = $earnedSalary;

                $detail->employee_pf = $employeePf;
                $detail->esi = $esi;
                $detail->professional_tax = $professionalTax;
                $detail->tds = $tds;
                $detail->other_deduction = $otherDeduction;
                $detail->total_deduction = $totalDeduction;

                $detail->net_salary = $netSalary;
                $detail->status = 'Generated';
                $detail->created_by = auth()->id();
                $detail->save();

                $batchGross += $grossSalary;
                $batchDeduction += $totalDeduction;
                $batchNet += $netSalary;
            }

            // Update master totals manually
            $payroll->total_gross_salary = round($batchGross, 2);
            $payroll->total_deduction = round($batchDeduction, 2);
            $payroll->total_net_salary = round($batchNet, 2);
            $payroll->save();

            DB::commit();

            session()->flash('success', 'Payroll generated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Payroll generated successfully.',
                'redirect' => route('payrolls.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate payroll: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View detailed payroll batch with employee snapshot records.
     */
    public function show($id)
    {
        $payroll = Payroll::with([
            'company',
            'branch',
            'creator',
            'details.employee.department'
        ])->findOrFail($id);

        $monthName = Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F');

        return view('Admin.Payroll.show', compact('payroll', 'monthName'));
    }

    /**
     * Delete specified payroll batch (Subject to status rules).
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);

        if (in_array($payroll->status, ['Locked', 'Paid'])) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Locked or Paid payroll records cannot be deleted.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Locked or Paid payroll records cannot be deleted.');
        }

        DB::beginTransaction();

        try {
            // Soft delete details and master
            $payroll->details()->delete();
            $payroll->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payroll batch deleted successfully.',
                ]);
            }

            return redirect()->route('payrolls.index')->with('success', 'Payroll batch deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete payroll batch: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete payroll batch.');
        }
    }

    /**
     * Display Salary Slip for an employee (HTML / Print view).
     */
    public function salarySlip($detailId)
    {
        $detail = PayrollDetail::with([
            'payroll.company',
            'payroll.branch',
            'employee.department'
        ])->findOrFail($detailId);

        $monthName = Carbon::createFromDate($detail->payroll->year, $detail->payroll->month, 1)->format('F');

        return view('Admin.Payroll.salary_slip', compact('detail', 'monthName'));
    }

    /**
     * Download / Printable A4 Portrait PDF view for Salary Slip.
     */
    public function salarySlipPdf($detailId)
    {
        $detail = PayrollDetail::with([
            'payroll.company',
            'payroll.branch',
            'employee.department'
        ])->findOrFail($detailId);

        $monthName = Carbon::createFromDate($detail->payroll->year, $detail->payroll->month, 1)->format('F');

        return view('Admin.Payroll.salary_slip_pdf', compact('detail', 'monthName'));
    }
}
