<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceBatch;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance history batches (Yajra DataTables).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $batches = AttendanceBatch::with(['company', 'branch'])
                ->withCount('attendances')
                ->when($request->filled('company_id'), function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
                ->when($request->filled('branch_id'), function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->orderBy('attendance_date', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($batches)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->company ? e($row->company->name) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? e($row->branch->name) : '<span class="text-muted">N/A</span>';
                })
                ->editColumn('attendance_date', function ($row) {
                    return date('d-m-Y', strtotime($row->attendance_date));
                })
                ->addColumn('employees_count', function ($row) {
                    return '<span class="badge bg-secondary px-2 py-1">' . $row->attendances_count . '</span>';
                })
                ->addColumn('status', function ($row) {
                    return '<span class="badge bg-success px-2 py-1">Completed</span>';
                })
                ->addColumn('edit', function ($row) {
                    return '<a href="' . route('attendance.edit', $row->id) . '" class="btn btn-xs btn-primary py-0 px-1 me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>';
                })
                ->addColumn('delete', function ($row) {
                    return '<button type="button" class="btn btn-xs btn-danger text-white py-0 px-1 btn-delete" data-url="' . route('attendance.destroy', $row->id) . '" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>';
                })
                ->rawColumns(['company_name', 'branch_name', 'employees_count', 'status', 'edit', 'delete'])
                ->make(true);
        }

        return view('Admin.Attendance.index');
    }

    /**
     * Show the form for creating / marking attendance.
     */
    public function create()
    {
        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();

        return view('Admin.Attendance.create', compact('companies', 'branches'));
    }

    /**
     * Load employees for marking or editing attendance.
     */
    public function loadEmployees(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'attendance_date' => 'required|date',
        ], [
            'company_id.required' => 'Please select company.',
            'branch_id.required' => 'Please select branch.',
            'attendance_date.required' => 'Please select attendance date.',
        ]);

        $companyId = $request->company_id;
        $branchId = $request->branch_id;
        $attendanceDate = $request->attendance_date;

        // Check if batch already exists for this Company, Branch, and Date
        $batch = AttendanceBatch::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('attendance_date', $attendanceDate)
            ->first();

        // Load active employees for the selected Company & Branch
        $employees = Employee::with('department')
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('employee_code', 'asc')
            ->get();

        // If batch exists, map existing attendance records
        $existingAttendances = collect();
        if ($batch) {
            $existingAttendances = Attendance::where('attendance_batch_id', $batch->id)
                ->get()
                ->keyBy('employee_id');
        }

        $records = [];
        foreach ($employees as $emp) {
            $att = $existingAttendances->get($emp->id);
            $records[] = [
                'employee_id' => $emp->id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->name,
                'department_name' => $emp->department ? $emp->department->name : 'N/A',
                'attendance_status' => $att ? $att->attendance_status : 'Present',
                'check_in' => $att ? ($att->check_in ? date('H:i', strtotime($att->check_in)) : '09:00') : '09:00',
                'check_out' => $att ? ($att->check_out ? date('H:i', strtotime($att->check_out)) : '18:00') : '18:00',
                'remarks' => $att ? $att->remarks : '',
            ];
        }

        $isEditMode = $batch ? true : false;

        $html = view('Admin.Attendance.table', compact('records', 'companyId', 'branchId', 'attendanceDate', 'isEditMode', 'batch'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'is_edit' => $isEditMode,
            'message' => $isEditMode ? 'Existing attendance record loaded.' : 'Active employees loaded for attendance marking.',
        ]);
    }

    /**
     * Store or update attendance for employees in a batch.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.attendance_status' => 'required|in:Present,Absent,Half Day,Leave,Holiday',
        ], [
            'company_id.required' => 'Please select company.',
            'branch_id.required' => 'Please select branch.',
            'attendance_date.required' => 'Please select attendance date.',
            'attendances.required' => 'No employee attendance records provided.',
        ]);

        DB::beginTransaction();

        try {
            // Check for existing batch or create new one using manual model assignments
            $batch = AttendanceBatch::where('company_id', $request->company_id)
                ->where('branch_id', $request->branch_id)
                ->where('attendance_date', $request->attendance_date)
                ->first();

            if (!$batch) {
                $batch = new AttendanceBatch();
                $batch->company_id = $request->company_id;
                $batch->branch_id = $request->branch_id;
                $batch->attendance_date = $request->attendance_date;
                $batch->created_by = auth()->id();
                $batch->save();
            } else {
                $batch->updated_by = auth()->id();
                $batch->save();
            }

            foreach ($request->attendances as $empData) {
                $employeeId = $empData['employee_id'];
                $status = $empData['attendance_status'];
                $checkIn = !empty($empData['check_in']) ? $empData['check_in'] : null;
                $checkOut = !empty($empData['check_out']) ? $empData['check_out'] : null;
                $remarks = !empty($empData['remarks']) ? $empData['remarks'] : null;

                $attendance = Attendance::where('attendance_batch_id', $batch->id)
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$attendance) {
                    $attendance = new Attendance();
                    $attendance->attendance_batch_id = $batch->id;
                    $attendance->employee_id = $employeeId;
                    $attendance->created_by = auth()->id();
                } else {
                    $attendance->updated_by = auth()->id();
                }

                $attendance->attendance_status = $status;
                $attendance->check_in = $checkIn;
                $attendance->check_out = $checkOut;
                $attendance->remarks = $remarks;
                $attendance->save();
            }

            DB::commit();

            session()->flash('success', 'Attendance saved successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Attendance saved successfully.',
                'redirect' => route('attendance.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while saving attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show form to edit a specific attendance batch.
     */
    public function edit($id)
    {
        $batch = AttendanceBatch::with(['company', 'branch', 'attendances.employee.department'])->findOrFail($id);

        $records = [];
        foreach ($batch->attendances as $att) {
            $records[] = [
                'employee_id' => $att->employee_id,
                'employee_code' => $att->employee->employee_code ?? 'N/A',
                'name' => $att->employee->name ?? 'N/A',
                'department_name' => ($att->employee && $att->employee->department) ? $att->employee->department->name : 'N/A',
                'attendance_status' => $att->attendance_status,
                'check_in' => $att->check_in ? date('H:i', strtotime($att->check_in)) : '09:00',
                'check_out' => $att->check_out ? date('H:i', strtotime($att->check_out)) : '18:00',
                'remarks' => $att->remarks ?? '',
            ];
        }

        return view('Admin.Attendance.edit', compact('batch', 'records'));
    }

    /**
     * Update specified attendance batch.
     */
    public function update(Request $request, $id)
    {
        $batch = AttendanceBatch::findOrFail($id);

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.attendance_status' => 'required|in:Present,Absent,Half Day,Leave,Holiday',
        ], [
            'attendances.required' => 'No employee attendance records provided.',
        ]);

        DB::beginTransaction();

        try {
            $batch->updated_by = auth()->id();
            $batch->save();

            foreach ($request->attendances as $empData) {
                $employeeId = $empData['employee_id'];
                $status = $empData['attendance_status'];
                $checkIn = !empty($empData['check_in']) ? $empData['check_in'] : null;
                $checkOut = !empty($empData['check_out']) ? $empData['check_out'] : null;
                $remarks = !empty($empData['remarks']) ? $empData['remarks'] : null;

                $attendance = Attendance::where('attendance_batch_id', $batch->id)
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$attendance) {
                    $attendance = new Attendance();
                    $attendance->attendance_batch_id = $batch->id;
                    $attendance->employee_id = $employeeId;
                    $attendance->created_by = auth()->id();
                } else {
                    $attendance->updated_by = auth()->id();
                }

                $attendance->attendance_status = $status;
                $attendance->check_in = $checkIn;
                $attendance->check_out = $checkOut;
                $attendance->remarks = $remarks;
                $attendance->save();
            }

            DB::commit();

            session()->flash('success', 'Attendance batch updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Attendance batch updated successfully.',
                'redirect' => route('attendance.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified attendance batch from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $batch = AttendanceBatch::findOrFail($id);
            $batch->delete(); // Soft deletes batch and cascade deletes attendances

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Attendance batch deleted successfully.',
                ]);
            }

            return redirect()->route('attendance.index')->with('success', 'Attendance batch deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete attendance batch: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete attendance batch.');
        }
    }
}
