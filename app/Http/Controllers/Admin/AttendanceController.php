<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonth;
use App\Models\AttendanceMonthDetail;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\AttendanceLockService;
use App\Services\CompanyScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of saved monthly attendance batches (Yajra DataTables).
     */
    /**
     * Display unified monthly attendance management (Payroll Driven).
     */
    public function index(Request $request)
    {
        $companyId = CompanyScope::id();
        $company = $companyId ? Company::find($companyId) : Company::where('status', 'active')->first();
        if ($company && !$companyId) {
            $companyId = $company->id;
        }

        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $currentMonth = (int) $request->query('month', date('n'));
        $currentYear = (int) $request->query('year', date('Y'));
        $selectedBranchId = $request->query('branch_id', null);
        $monthName = $months[$currentMonth] ?? 'January';

        return view('Admin.Attendance.index', compact('company', 'companyId', 'branches', 'months', 'currentMonth', 'currentYear', 'monthName', 'selectedBranchId'));
    }

    /**
     * Show the form for marking monthly attendance.
     */
    public function create(Request $request)
    {
        $companyId = CompanyScope::id();
        $company = $companyId ? Company::find($companyId) : Company::where('status', 'active')->first();
        if ($company && !$companyId) {
            $companyId = $company->id;
        }

        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $currentMonth = (int) $request->query('month', date('n'));
        $currentYear = (int) $request->query('year', date('Y'));
        $selectedBranchId = $request->query('branch_id', null);
        $monthName = $months[$currentMonth] ?? 'January';

        return view('Admin.Attendance.create', compact('company', 'companyId', 'branches', 'months', 'currentMonth', 'currentYear', 'monthName', 'selectedBranchId'));
    }

    /**
     * Load employees for marking or editing monthly attendance.
     */
    public function loadEmployees(Request $request)
    {
        $scopedCompanyId = CompanyScope::id();
        if ($scopedCompanyId) {
            $companyId = $scopedCompanyId;
            if ($request->filled('company_id') && (int)$request->company_id !== (int)$companyId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized cross-company access.'
                ], 403);
            }
            $request->merge(['company_id' => $companyId]);
        }

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

        // Verify branch belongs to current company
        $branchValid = Branch::where('id', $branchId)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->exists();

        if (!$branchValid) {
            return response()->json([
                'status' => false,
                'message' => 'Selected branch does not belong to the active company.',
            ], 403);
        }

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
            $hasDetail = $detail !== null;
            
            $leaveTaken = ($hasDetail && $detail->leave_taken !== null) ? (float)$detail->leave_taken : null;
            $calcLeaveTaken = $leaveTaken ?? 0;
            $netPresent = $hasDetail ? (float)$detail->net_present : max(0, $totalDays - $calcLeaveTaken);
            $leaveNotDeducted = ($hasDetail && $detail->leave_not_deducted !== null) ? (float)$detail->leave_not_deducted : null;
            $calcLeaveNotDeducted = $leaveNotDeducted ?? 0;
            $payableDays = $hasDetail ? (float)$detail->payable_days : ($netPresent + $calcLeaveNotDeducted);

            $records[] = [
                'employee_id' => $emp->id,
                'name' => $emp->name ?? 'N/A',
                'salary_exists' => (bool) $emp->salary_exists,
                'branch_name' => $emp->branch ? $emp->branch->name : 'N/A',
                'total_days' => $totalDays,
                'leave_taken' => $leaveTaken !== null ? (($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken) : null,
                'net_present' => ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent,
                'leave_not_deducted' => $leaveNotDeducted !== null ? (($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted) : null,
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
        $scopedCompanyId = CompanyScope::id();
        if ($scopedCompanyId) {
            if ($request->filled('company_id') && (int)$request->company_id !== (int)$scopedCompanyId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized cross-company access.'
                ], 403);
            }
            $request->merge(['company_id' => $scopedCompanyId]);
        }

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

        $companyId = (int) $request->company_id;
        $branchId = (int) $request->branch_id;

        // Verify branch belongs to current company
        $branchValid = Branch::where('id', $branchId)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->exists();

        if (!$branchValid) {
            return response()->json([
                'status' => false,
                'message' => 'Selected branch does not belong to the active company.',
            ], 403);
        }

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

        // Check if all employees have active salary configurations
        $missingSalaryEmployees = [];
        foreach ($request->details as $index => $empData) {
            $empId = $empData['employee_id'];
            $hasSalary = Employee::where('id', $empId)
                ->whereHas('salaries', function ($query) {
                    $query->where('status', 'active');
                })
                ->exists();

            if (!$hasSalary) {
                $employee = Employee::find($empId);
                $missingSalaryEmployees[] = $employee ? $employee->name : 'Employee #' . ($index + 1);
            }
        }

        if (!empty($missingSalaryEmployees)) {
            return response()->json([
                'status' => false,
                'message' => 'Attendance cannot be saved. Salary is not set for the following employee(s): ' . implode(', ', $missingSalaryEmployees) . '. Please click "Set Salary" to configure salary in a new tab first.',
            ], 422);
        }

        $isAllComplete = true;

        // Server-Side Validations
        foreach ($request->details as $index => $empData) {
            $rawLeaveTaken = $empData['leave_taken'] ?? null;
            $rawLeaveNotDeducted = $empData['leave_not_deducted'] ?? null;

            $hasLeaveTaken = ($rawLeaveTaken !== null && trim((string)$rawLeaveTaken) !== '');
            $hasLeaveNotDeducted = ($rawLeaveNotDeducted !== null && trim((string)$rawLeaveNotDeducted) !== '');

            $employee = Employee::find($empData['employee_id']);
            $empName = $employee ? $employee->name : 'Employee #' . ($index + 1);

            if ($hasLeaveTaken && !$hasLeaveNotDeducted) {
                return response()->json([
                    'status' => false,
                    'message' => "Please enter 'Leave Not Deducted' for {$empName} as Leave Taken is provided.",
                ], 422);
            }

            if (!$hasLeaveTaken || !$hasLeaveNotDeducted) {
                $isAllComplete = false;
            }

            if ($hasLeaveTaken) {
                $leaveTaken = (float)$rawLeaveTaken;
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
            }

            if ($hasLeaveNotDeducted) {
                $leaveNotDeducted = (float)$rawLeaveNotDeducted;
                if ($leaveNotDeducted < 0) {
                    return response()->json([
                        'status' => false,
                        'message' => "Leave Not Deducted for {$empName} cannot be negative.",
                    ], 422);
                }

                if ($hasLeaveTaken) {
                    $leaveTaken = (float)$rawLeaveTaken;
                    if ($leaveNotDeducted > $leaveTaken) {
                        return response()->json([
                            'status' => false,
                            'message' => "Leave Not Deducted for {$empName} ({$leaveNotDeducted} days) cannot be greater than Leave Taken ({$leaveTaken} days).",
                        ], 422);
                    }
                }
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
                $attendanceMonth->created_by = auth()->id();
            } else {
                $attendanceMonth->updated_by = auth()->id();
            }

            $attendanceMonth->status = $isAllComplete ? 'Completed' : 'Draft';
            $attendanceMonth->save();

            foreach ($request->details as $empData) {
                $employeeId = $empData['employee_id'];
                $rawLeaveTaken = $empData['leave_taken'] ?? null;
                $rawLeaveNotDeducted = $empData['leave_not_deducted'] ?? null;

                $leaveTaken = ($rawLeaveTaken !== null && trim($rawLeaveTaken) !== '') ? (float)$rawLeaveTaken : null;
                $leaveNotDeducted = ($rawLeaveNotDeducted !== null && trim($rawLeaveNotDeducted) !== '') ? (float)$rawLeaveNotDeducted : null;

                $calcLeaveTaken = $leaveTaken ?? 0;
                $calcLeaveNotDeducted = $leaveNotDeducted ?? 0;

                // Auto formulas:
                // Net Present = No. of Days in Month - Leave Taken
                $netPresent = max(0, $totalDays - $calcLeaveTaken);
                // No. of Days Payable = Net Present + Leave Not Deducted
                $payableDays = $netPresent + $calcLeaveNotDeducted;

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
                $detail->leave_taken = ($leaveTaken !== null) ? (($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken) : null;
                $detail->net_present = ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent;
                $detail->leave_not_deducted = ($leaveNotDeducted !== null) ? (($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted) : null;
                $detail->payable_days = ($payableDays == (int)$payableDays) ? (int)$payableDays : $payableDays;
                $detail->save();
            }

            DB::commit();

            session()->flash('success', 'Monthly attendance saved successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Monthly attendance saved successfully.',
                'redirect' => route('payroll-processing.index')
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
    public function show(Request $request, $id = null)
    {
        $companyId = CompanyScope::id();
        $company = $companyId ? Company::find($companyId) : Company::where('status', 'active')->first();
        if ($company && !$companyId) {
            $companyId = $company->id;
        }

        $currentMonth = (int) $request->query('month', date('n'));
        $currentYear = (int) $request->query('year', date('Y'));
        $selectedBranchId = $request->query('branch_id', null);

        if ($id && is_numeric($id)) {
            $attendanceMonth = AttendanceMonth::forCurrentCompany()->find($id);
            if ($attendanceMonth) {
                $currentMonth = (int) $attendanceMonth->month;
                $currentYear = (int) $attendanceMonth->year;
                $selectedBranchId = (int) $attendanceMonth->branch_id;
            }
        }

        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $monthName = $months[$currentMonth] ?? 'January';

        return view('Admin.Attendance.show', compact('company', 'companyId', 'branches', 'months', 'currentMonth', 'currentYear', 'monthName', 'selectedBranchId'));
    }

    /**
     * Load attendance summary for specified branch, month, and year (Read-Only).
     */
    public function loadAttendanceSummary(Request $request)
    {
        $scopedCompanyId = CompanyScope::id();
        if ($scopedCompanyId) {
            $companyId = $scopedCompanyId;
            if ($request->filled('company_id') && (int)$request->company_id !== (int)$companyId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized cross-company access.'
                ], 403);
            }
            $request->merge(['company_id' => $companyId]);
        }

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
        ]);

        $companyId = (int) $request->company_id;
        $branchId = (int) $request->branch_id;
        $month = (int) $request->month;
        $year = (int) $request->year;

        // Verify branch belongs to current company
        $branchValid = Branch::where('id', $branchId)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->exists();

        if (!$branchValid) {
            return response()->json([
                'status' => false,
                'message' => 'Selected branch does not belong to the active company.',
            ], 403);
        }

        $totalDays = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $attendanceMonth = AttendanceMonth::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

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
                'message' => 'No active employees found for the selected branch.',
            ], 422);
        }

        $existingDetails = collect();
        if ($attendanceMonth) {
            $existingDetails = AttendanceMonthDetail::where('attendance_month_id', $attendanceMonth->id)
                ->get()
                ->keyBy('employee_id');
        }

        $records = [];
        $completedCount = 0;
        $pendingCount = 0;

        foreach ($employees as $emp) {
            $detail = $existingDetails->get($emp->id);
            $hasDetail = $detail !== null;

            $leaveTaken = ($hasDetail && $detail->leave_taken !== null) ? (float)$detail->leave_taken : null;
            $calcLeaveTaken = $leaveTaken ?? 0;
            $netPresent = $hasDetail ? (float)$detail->net_present : max(0, $totalDays - $calcLeaveTaken);
            $leaveNotDeducted = ($hasDetail && $detail->leave_not_deducted !== null) ? (float)$detail->leave_not_deducted : null;
            $calcLeaveNotDeducted = $leaveNotDeducted ?? 0;
            $payableDays = $hasDetail ? (float)$detail->payable_days : ($netPresent + $calcLeaveNotDeducted);

            if ($leaveTaken === null || $leaveNotDeducted === null) {
                $pendingCount++;
            } else {
                $completedCount++;
            }

            $records[] = [
                'employee_id' => $emp->id,
                'name' => $emp->name ?? 'N/A',
                'salary_exists' => (bool) $emp->salary_exists,
                'branch_name' => $emp->branch ? $emp->branch->name : 'N/A',
                'total_days' => $totalDays,
                'leave_taken' => $leaveTaken !== null ? (($leaveTaken == (int)$leaveTaken) ? (int)$leaveTaken : $leaveTaken) : null,
                'net_present' => ($netPresent == (int)$netPresent) ? (int)$netPresent : $netPresent,
                'leave_not_deducted' => $leaveNotDeducted !== null ? (($leaveNotDeducted == (int)$leaveNotDeducted) ? (int)$leaveNotDeducted : $leaveNotDeducted) : null,
                'payable_days' => ($payableDays == (int)$payableDays) ? (int)$payableDays : $payableDays,
            ];
        }

        $html = view('Admin.Attendance.summary_table', compact('records', 'companyId', 'branchId', 'month', 'year', 'totalDays', 'completedCount', 'pendingCount'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Attendance summary loaded successfully.',
        ]);
    }

    /**
     * Show form to edit specified monthly attendance batch.
     */
    public function edit($id)
    {
        $attendanceMonth = AttendanceMonth::forCurrentCompany()->with([
            'company',
            'branch'
        ])->findOrFail($id);

        if (AttendanceLockService::isLocked($attendanceMonth->company_id, (int)$attendanceMonth->year, (int)$attendanceMonth->month)) {
            abort(403, 'Attendance has been locked because all company branches have completed attendance for this month.');
        }

        $companyId = $attendanceMonth->company_id;
        $company = $attendanceMonth->company;
        $currentMonth = (int) $attendanceMonth->month;
        $currentYear = (int) $attendanceMonth->year;
        $selectedBranchId = (int) $attendanceMonth->branch_id;

        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        $monthName = $months[$currentMonth] ?? 'January';

        return view('Admin.Attendance.edit', compact('attendanceMonth', 'company', 'companyId', 'branches', 'months', 'currentMonth', 'currentYear', 'monthName', 'selectedBranchId'));
    }

    /**
     * Update specified monthly attendance batch using manual property assignments.
     */
    public function update(Request $request, $id)
    {
        return $this->store($request);
    }

    /**
     * Remove the specified monthly attendance batch from storage.
     */
    public function destroy($id)
    {
        $attendanceMonth = AttendanceMonth::forCurrentCompany()->findOrFail($id);

        if (AttendanceLockService::isLocked($attendanceMonth->company_id, (int)$attendanceMonth->year, (int)$attendanceMonth->month)) {
            $lockMsg = 'Attendance has been locked because all company branches have completed attendance for this month.';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $lockMsg,
                ], 403);
            }
            abort(403, $lockMsg);
        }

        DB::beginTransaction();

        try {
            $attendanceMonth->delete(); // Soft delete master and cascade soft delete details

            DB::commit();

            Log::info("Attendance Deleted: Company ID {$attendanceMonth->company_id}, Branch ID {$attendanceMonth->branch_id}, Month {$attendanceMonth->month}, Year {$attendanceMonth->year} by User " . auth()->id());

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
