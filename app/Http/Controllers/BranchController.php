<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches for the specified company.
     */
    public function index(Request $request, Company $company)
    {
        if ($request->ajax()) {
            $branches = $company->branches()->orderBy('id', 'desc');

            return DataTables::of($branches)
                ->addIndexColumn()
                ->addColumn('edit', function ($row) use ($company) {
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . route('admin.company.branches.edit', [$company->id, $row->id]) . '" class="btn btn-xs btn-outline-primary py-0 px-2" title="Edit">
                                    Edit
                                </a>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 btn-delete-branch" data-url="' . route('admin.company.branches.destroy', [$company->id, $row->id]) . '" title="Delete">
                                    Delete
                                </button>
                            </div>';
                })
                ->editColumn('status', function ($row) {
                    $badgeClass = strtolower($row->status) === 'active' ? 'bg-warning text-dark' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('email', function ($row) {
                    return $row->email ? '<a href="mailto:' . e($row->email) . '" class="text-decoration-none text-body">' . e($row->email) . '</a>' : '<span class="text-muted">N/A</span>';
                })
                ->rawColumns(['edit', 'status', 'email'])
                ->make(true);
        }

        return view('Admin.Company.Branch.index', compact('company'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create(Company $company): View
    {
        return view('Admin.Company.Branch.create', compact('company'));
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:branches,email',
            'contact_no' => 'required|digits:10',
            'address_line1' => 'required',
            'address_line2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'status' => 'required',
        ], [
            'name.required' => 'Branch name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email.',
            'email.unique' => 'Email already exists.',
            'contact_no.required' => 'Contact number is required.',
            'contact_no.digits' => 'Contact number must be 10 digits.',
            'address_line1.required' => 'Address Line 1 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'status.required' => 'Select Status.',
        ]);

        $branch = new Branch();
        $branch->company_id = $company->id;
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->contact_no = $request->contact_no;
        $branch->address_line1 = $request->address_line1;
        $branch->address_line2 = $request->address_line2;
        $branch->city = $request->city;
        $branch->state = $request->state;
        $branch->zip_code = $request->zip_code;
        $branch->status = $request->status;
        $branch->created_by = auth()->id();
        $branch->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Branch created successfully.',
                'redirect' => route('admin.company.branches.index', $company->id)
            ]);
        }

        return redirect()->route('admin.company.branches.index', $company->id)
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Company $company, Branch $branch): View
    {
        return view('Admin.Company.Branch.edit', compact('company', 'branch'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Company $company, Branch $branch)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:branches,email,' . $branch->id,
            'contact_no' => 'required|digits:10',
            'address_line1' => 'required',
            'address_line2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'status' => 'required',
        ], [
            'name.required' => 'Branch name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email.',
            'email.unique' => 'Email already exists.',
            'contact_no.required' => 'Contact number is required.',
            'contact_no.digits' => 'Contact number must be 10 digits.',
            'address_line1.required' => 'Address Line 1 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'status.required' => 'Select Status.',
        ]);

        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->contact_no = $request->contact_no;
        $branch->address_line1 = $request->address_line1;
        $branch->address_line2 = $request->address_line2;
        $branch->city = $request->city;
        $branch->state = $request->state;
        $branch->zip_code = $request->zip_code;
        $branch->status = $request->status;
        $branch->updated_by = auth()->id();
        $branch->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Branch updated successfully.',
                'redirect' => route('admin.company.branches.index', $company->id)
            ]);
        }

        return redirect()->route('admin.company.branches.index', $company->id)
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Request $request, Company $company, Branch $branch)
    {
        $branch->deleted_by = auth()->id();
        $branch->save();
        $branch->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Branch deleted successfully.'
            ]);
        }

        return redirect()->route('admin.company.branches.index', $company->id)
            ->with('success', 'Branch deleted successfully.');
    }
}
