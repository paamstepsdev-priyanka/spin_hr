<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonth;
use App\Models\AttendanceMonthDetail;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of saved monthly attendance batches (Yajra DataTables).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $batches = AttendanceMonth::with(['company', 'branch', 'creator'])
                ->withCount('details')
                ->when($request->filled('company_id'), function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
                ->when($request->filled('branch_id'), function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->where('month', $request->month);
                })
                ->when($request->filled('year'), function ($query) use ($request) {
                    return $query->where('year', $request->year);
                })
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($batches)
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
                ->addColumn('status', function ($row) {
                    return '<span class="badge bg-success px-2 py-1">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator ? e($row->creator->name) : '<span class="text-muted">System</span>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y h:i A') : '-';
                })
                ->addColumn('action', function ($row) {
                    $viewBtn = '<a href="' . route('attendance.show', $row->id) . '" class="btn btn-xs btn-outline-info py-0 px-1 me-1" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>';
                    $editBtn = '<a href="' . route('attendance.edit', $row->id) . '" class="btn btn-xs btn-outline-primary py-0 px-1 me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>';
                    $deleteBtn = '<button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 me-1 btn-delete" data-url="' . route('attendance.destroy', $row->id) . '" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>';

                    $payroll = Payroll::where('company_id', $row->company_id)
                        ->where('branch_id', $row->branch_id)
                        ->where('month', $row->month)
                        ->where('year', $row->year)
                        ->first();

                    if ($payroll) {
                        $payrollBtn = '<a href="' . route('payrolls.show', $payroll->id) . '" class="btn btn-xs btn-success py-0 px-1 text-white" title="View Generated Payroll">
                                        <i class="bi bi-currency-rupee"></i>
                                    </a>';
                    } else {
                        $payrollUrl = route('payrolls.create', [
                            'company_id' => $row->company_id,
                            'branch_id' => $row->branch_id,
                            'month' => $row->month,
                            'year' => $row->year,
                        ]);
                        $payrollBtn = '<a href="' . $payrollUrl . '" class="btn btn-xs btn-outline-warning py-0 px-1" title="Generate Payroll">
                                        <i class="bi bi-currency-rupee"></i>
                                    </a>';
                    }

                    return '<div class="d-flex justify-content-center align-items-center">' . $viewBtn . $editBtn . $deleteBtn . $payrollBtn . '</div>';
                })
                ->rawColumns(['company_name', 'branch_name', 'employees_count', 'status', 'created_by', 'created_at', 'action'])
                ->make(true);
        }

        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('Admin.Attendance.index', compact('companies', 'branches', 'months'));
    }

    /**
     * Show the form for marking monthly attendance.
     */
    public function create()
    {
        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        return view('Admin.Attendance.create', compact('companies', 'branches', 'months', 'currentMonth', 'currentYear'));
    }

    /**
     * Load employees for marking or editing monthly attendance.
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

        $companyId = $request->company_id;
        $branchId = $request->branch_id;
        $month = (int) $request->month;
        $year = (int) $request->year;

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        if ($year > $currentYear || ($year == $currentYear && $month > $currentMonth)) {
            return response()->json([
                'status' => false,
                'message' => 'Future month attendance cannot be created.',
            ], 422);
        }

        // Auto-calculate No. of Days in Month
        $totalDays = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Check if Monthly Attendance record already exists
        $attendanceMonth = AttendanceMonth::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // Fetch active employees of selected company & branch
        $employees = Employee::with('branch')
            ->withExists(['salaries as salary_exists' => function ($query) {
                $query->where('status', 'active');
            }])
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active employees found for the selected company and branch.',
            ], 422);
        }

        $existingDetails = collect();
        if ($attendanceMonth) {
            $existingDetails = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
                ->get()
                ->keyBy('employee_id');
        }

        $records = [];
        foreach ($employees as $emp) {
            $detail = $existingDetails->get($emp->id);
            
            $leaveTaken = $detail ? (float)$detail->leave_taken : 0;
            $netPresent = $detail ? (float)$detail->net_present : max(0, $totalDays - $leaveTaken);
            $leaveNotDeducted = $detail ? (float)$detail->leave_not_deducted : 0;
            $payableDays = $detail ? (float)$detail->payable_days : ($netPresent + $leaveNotDeducted);

            $records[] = [
                'employee_id' => $emp->id,
                'name' => $emp->name ?? 'N/A',
                'salary_exists' => (bool) $emp->salary_exists,
                'branch_name' => $emp->branch ? $emp->branch->name : 'N/A',
                'total_days' => $totalDays,
                'leave_taken' => ($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken,
                'net_present' => ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent,
                'leave_not_deducted' => ($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted,
                'payable_days' => ($payableDays == (int)$payableDays) ? (int)$payableDays : $payableDays,
            ];
        }

        $isEditMode = $attendanceMonth ? true : false;
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $html = view('Admin.Attendance.table', compact('records', 'companyId', 'branchId', 'month', 'year', 'monthName', 'totalDays', 'isEditMode', 'attendanceMonth'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'is_edit' => $isEditMode,
            'message' => $isEditMode ? 'Existing monthly attendance record loaded for editing.' : 'Active employees loaded for attendance marking.',
        ]);
    }

    /**
     * Store monthly attendance batch using manual property assignments.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
            'details' => 'required|array|min:1',
            'details.*.employee_id' => 'required|exists:employees,id',
            'details.*.leave_taken' => 'nullable|numeric|min:0',
            'details.*.leave_not_deducted' => 'nullable|numeric|min:0',
        ], [
            'company_id.required' => 'Please select company.',
            'branch_id.required' => 'Please select branch.',
            'month.required' => 'Please select month.',
            'year.required' => 'Please select year.',
            'details.required' => 'No employee attendance records submitted.',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        if ($year > $currentYear || ($year == $currentYear && $month > $currentMonth)) {
            return response()->json([
                'status' => false,
                'message' => 'Future month attendance cannot be created.',
            ], 422);
        }

        $totalDays = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Server-Side Validations
        foreach ($request->details as $index => $empData) {
            $leaveTaken = (float)($empData['leave_taken'] ?? 0);
            $leaveNotDeducted = (float)($empData['leave_not_deducted'] ?? 0);

            $employee = Employee::find($empData['employee_id']);
            $empName = $employee ? $employee->name : 'Employee #' . ($index + 1);

            if ($leaveTaken < 0) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Taken for {$empName} cannot be negative.",
                ], 422);
            }

            if ($leaveTaken > $totalDays) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Taken for {$empName} ({$leaveTaken} days) cannot be greater than No. of Days in Month ({$totalDays}).",
                ], 422);
            }

            if ($leaveNotDeducted < 0) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Not Deducted for {$empName} cannot be negative.",
                ], 422);
            }

            if ($leaveNotDeducted > $leaveTaken) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Not Deducted for {$empName} ({$leaveNotDeducted} days) cannot be greater than Leave Taken ({$leaveTaken} days).",
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            // Find existing AttendanceMonth or create new using manual assignment
            $attendanceMonth = AttendanceMonth::where('company_id', $request->company_id)
                ->where('branch_id', $request->branch_id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if (!$attendanceMonth) {
                $attendanceMonth = new AttendanceMonth();
                $attendanceMonth->company_id = $request->company_id;
                $attendanceMonth->branch_id = $request->branch_id;
                $attendanceMonth->month = $month;
                $attendanceMonth->year = $year;
                $attendanceMonth->status = 'Completed';
                $attendanceMonth->created_by = auth()->id();
                $attendanceMonth->save();
            } else {
                $attendanceMonth->updated_by = auth()->id();
                $attendanceMonth->save();
            }

            foreach ($request->details as $empData) {
                $employeeId = $empData['employee_id'];
                $leaveTaken = (float)($empData['leave_taken'] ?? 0);
                $leaveNotDeducted = (float)($empData['leave_not_deducted'] ?? 0);

                // Auto formulas:
                // Net Present = No. of Days in Month - Leave Taken
                $netPresent = max(0, $totalDays - $leaveTaken);
                // No. of Days Payable = Net Present + Leave Not Deducted
                $payableDays = $netPresent + $leaveNotDeducted;

                $detail = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$detail) {
                    $detail = new AttendanceMonthDetail();
                    $detail->attendance_month_id = $attendanceMonth->id;
                    $detail->employee_id = $employeeId;
                    $detail->created_by = auth()->id();
                } else {
                    $detail->updated_by = auth()->id();
                }

                $detail->total_days = $totalDays;
                $detail->leave_taken = ($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken;
                $detail->net_present = ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent;
                $detail->leave_not_deducted = ($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted;
                $detail->payable_days = ($payableDays == (int)$payableDays) ? (int)$payableDays : $payableDays;
                $detail->save();
            }

            DB::commit();

            session()->flash('success', 'Monthly attendance saved successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Monthly attendance saved successfully.',
                'redirect' => route('attendance.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while saving monthly attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display specified monthly attendance summary (View Mode).
     */
    public function show($id)
    {
        $attendanceMonth = AttendanceMonth::with([
            'company',
            'branch',
            'creator',
            'details.employee' => function ($q) {
                $q->withExists(['salaries as salary_exists' => function ($sq) {
                    $sq->where('status', 'active');
                }]);
            },
            'details.employee.branch'
        ])->findOrFail($id);

        $monthName = Carbon::createFromDate($attendanceMonth->year, $attendanceMonth->month, 1)->format('F');

        return view('Admin.Attendance.show', compact('attendanceMonth', 'monthName'));
    }

    /**
     * Show form to edit specified monthly attendance batch.
     */
    public function edit($id)
    {
        $attendanceMonth = AttendanceMonth::with([
            'company',
            'branch',
            'details.employee' => function ($q) {
                $q->withExists(['salaries as salary_exists' => function ($sq) {
                    $sq->where('status', 'active');
                }]);
            },
            'details.employee.branch'
        ])->findOrFail($id);

        $monthName = Carbon::createFromDate($attendanceMonth->year, $attendanceMonth->month, 1)->format('F');

        $records = [];
        foreach ($attendanceMonth->details as $detail) {
            $emp = $detail->employee;
            $leaveTaken = (float)$detail->leave_taken;
            $netPresent = (float)$detail->net_present;
            $leaveNotDeducted = (float)$detail->leave_not_deducted;
            $payableDays = (float)$detail->payable_days;

            $records[] = [
                'employee_id' => $detail->employee_id,
                'name' => $emp ? ($emp->name ?? 'N/A') : 'N/A',
                'salary_exists' => (bool) ($emp->salary_exists ?? false),
                'branch_name' => ($emp && $emp->branch) ? $emp->branch->name : 'N/A',
                'total_days' => $detail->total_days,
                'leave_taken' => ($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken,
                'net_present' => ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent,
                'leave_not_deducted' => ($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted,
                'payable_days' => ($payableDays == (int)$payableDays) ? (int)$payableDays : $payableDays,
            ];
        }

        return view('Admin.Attendance.edit', compact('attendanceMonth', 'monthName', 'records'));
    }

    /**
     * Update specified monthly attendance batch using manual property assignments.
     */
    public function update(Request $request, $id)
    {
        $attendanceMonth = AttendanceMonth::findOrFail($id);

        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.employee_id' => 'required|exists:employees,id',
            'details.*.leave_taken' => 'nullable|numeric|min:0',
            'details.*.leave_not_deducted' => 'nullable|numeric|min:0',
        ], [
            'details.required' => 'No employee attendance records submitted.',
        ]);

        $totalDays = Carbon::createFromDate($attendanceMonth->year, $attendanceMonth->month, 1)->daysInMonth;

        // Server-Side Validations
        foreach ($request->details as $index => $empData) {
            $leaveTaken = (float)($empData['leave_taken'] ?? 0);
            $leaveNotDeducted = (float)($empData['leave_not_deducted'] ?? 0);

            $employee = Employee::find($empData['employee_id']);
            $empName = $employee ? $employee->name : 'Employee #' . ($index + 1);

            if ($leaveTaken < 0) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Taken for {$empName} cannot be negative.",
                ], 422);
            }

            if ($leaveTaken > $totalDays) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Taken for {$empName} ({$leaveTaken} days) cannot be greater than No. of Days in Month ({$totalDays}).",
                ], 422);
            }

            if ($leaveNotDeducted < 0) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Not Deducted for {$empName} cannot be negative.",
                ], 422);
            }

            if ($leaveNotDeducted > $leaveTaken) {
                return response()->json([
                    'status' => false,
                    'message' => "Leave Not Deducted for {$empName} ({$leaveNotDeducted} days) cannot be greater than Leave Taken ({$leaveTaken} days).",
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $attendanceMonth->updated_by = auth()->id();
            $attendanceMonth->save();

            foreach ($request->details as $empData) {
                $employeeId = $empData['employee_id'];
                $leaveTaken = (float)($empData['leave_taken'] ?? 0);
                $leaveNotDeducted = (float)($empData['leave_not_deducted'] ?? 0);

                // Auto formulas:
                $netPresent = max(0, $totalDays - $leaveTaken);
                $payableDays = $netPresent + $leaveNotDeducted;

                $detail = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$detail) {
                    $detail = new AttendanceMonthDetail();
                    $detail->attendance_month_id = $attendanceMonth->id;
                    $detail->employee_id = $employeeId;
                    $detail->created_by = auth()->id();
                } else {
                    $detail->updated_by = auth()->id();
                }

                $detail->total_days = $totalDays;
                $detail->leave_taken = ($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken;
                $detail->net_present = ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent;
                $detail->leave_not_deducted = ($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted;
                $detail->payable_days = ($payableDays == (int)$payableDays) ? (int)$payableDays : $payableDays;
                $detail->save();
            }

            DB::commit();

            session()->flash('success', 'Monthly attendance updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Monthly attendance updated successfully.',
                'redirect' => route('attendance.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating monthly attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified monthly attendance batch from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $attendanceMonth = AttendanceMonth::findOrFail($id);
            $attendanceMonth->delete(); // Soft delete master and cascade soft delete details

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Monthly attendance record deleted successfully.',
                ]);
            }

            return redirect()->route('attendance.index')->with('success', 'Monthly attendance record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete monthly attendance record: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete monthly attendance record.');
        }
    }
}
