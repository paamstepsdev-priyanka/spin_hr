<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments (Supports AJAX DataTables & standard view).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::orderBy('id', 'desc');

            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('departments.edit', $row->id);
                    $deleteUrl = route('departments.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . $editUrl . '" class="btn btn-xs btn-outline-primary py-0 px-2" title="Edit">Edit</a>
                                <form action="' . $deleteUrl . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this department?\');">
                                    ' . $csrf . '
                                    ' . $method . '
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2" title="Delete">Del</button>
                                </form>
                            </div>';
                })
                ->editColumn('status', function ($row) {
                    $badgeClass = strtolower($row->status) === 'active' ? 'bg-warning text-dark' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y H:i') : 'N/A';
                })
                ->rawColumns(['action', 'status'])
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
        $request->validate([
            'name' => 'required|max:255|unique:departments,name',
            'status' => 'required',
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'Department name already exists.',
            'status.required' => 'Select Status.',
        ]);

        $department = new Department();
        $department->name = $request->name;
        $department->status = $request->status;
        $department->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Department created successfully.',
                'redirect' => route('departments.index')
            ]);
        }

        return redirect()->route('departments.index')
                         ->with('success', 'Department created successfully.');
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
        $request->validate([
            'name' => 'required|max:255|unique:departments,name,' . $department->id,
            'status' => 'required',
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'Department name already exists.',
            'status.required' => 'Select Status.',
        ]);

        $department->name = $request->name;
        $department->status = $request->status;
        $department->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Department updated successfully.',
                'redirect' => route('departments.index')
            ]);
        }

        return redirect()->route('departments.index')
                         ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Department deleted successfully.',
                'redirect' => route('departments.index')
            ]);
        }

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
