<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments (Supports AJAX DataTables & standard view).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::forCurrentCompany()->orderBy('id', 'desc');

            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('edit', function ($row) {
                    return '<a href="' . route('departments.edit', $row->id) . '" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>';
                })
                ->editColumn('status', function ($row) {
                    $badgeClass = strtolower($row->status) === 'active' ? 'bg-warning text-dark' : 'bg-danger';
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y H:i') : 'N/A';
                })
                ->rawColumns(['edit', 'status'])
                ->make(true);
        }

        return view('Admin.Department.index');
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        return view('Admin.Department.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        Department::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        session()->flash('success', 'Department created successfully.');
        return response()->json([
            'status' => 'success',
            'message' => 'Department created successfully.',
            'redirect' => route('departments.index')
        ]);
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        return view('Admin.Department.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name,' . $department->id,
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $department->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        session()->flash('success', 'Department updated successfully.');
        return response()->json([
            'status' => 'success',
            'message' => 'Department updated successfully.',
            'redirect' => route('departments.index')
        ]);
    }
}
