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
                                <a href="' . route('admin.company.branches.edit', [$company->id, $row->id]) . '" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit">
                                    Edit
                                </a>
                            </div>';
                })
                ->editColumn('status', function ($row) {
                    $badgeClass = strtolower($row->status) === 'active' ? 'bg-warning text-dark' : 'bg-danger';
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
        $branch->fill($request->all());
        $branch->created_by = auth()->id();
        $branch->save();

        session()->flash('success', 'Branch created successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Branch created successfully.',
            'redirect' => route('admin.company.branches.index', $company->id)
        ]);
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

        $branch->fill($request->all());

        $branch->updated_by = auth()->id();
        $branch->save();

        session()->flash('success', 'Branch updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Branch updated successfully.',
            'redirect' => route('admin.company.branches.index', $company->id)
        ]);
    }
}
