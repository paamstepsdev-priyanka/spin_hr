<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies (Supports AJAX DataTables & standard view).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $companies = Company::withCount('branches')->orderBy('id', 'desc');

            return DataTables::of($companies)
                ->addIndexColumn()
                ->addColumn('branches', function ($row) {
                    return '<a href="' . route('admin.company.branches.index', $row->id) . '" class="btn btn-xs btn-outline-info py-0 px-2 fw-semibold" title="Branches">
                                Branches (' . $row->branches_count . ')
                            </a>';
                })
                ->addColumn('edit', function ($row) {
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . route('companies.edit', $row->id) . '" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit">
                                    Edit
                                </a>
                            </div>';
                })
                ->editColumn('status', function ($row) {
                    $badgeClass = strtolower($row->status) === 'active' ? 'bg-warning text-dark' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('logo', function ($row) {
                    if ($row->logo) {
                        return '<img src="' . asset('storage/' . $row->logo) . '" alt="Logo" class="rounded" style="width: 32px; height: 32px; object-fit: cover;">';
                    }
                    return '<span class="badge bg-light text-muted border">N/A</span>';
                })
                ->editColumn('pf_applicable', function ($row) {
                    $badgeClass = strtoupper($row->pf_applicable) === 'YES' || $row->pf_applicable === 'Yes' ? 'bg-success' : 'bg-light text-muted border';
                    return '<span class="badge ' . $badgeClass . '">' . $row->pf_applicable . '</span>';
                })
                ->editColumn('email', function ($row) {
                    return '<a href="mailto:' . e($row->email) . '" class="text-decoration-none text-body">' . e($row->email) . '</a>';
                })
                ->rawColumns(['edit', 'branches', 'status', 'logo', 'pf_applicable', 'email'])
                ->make(true);
        }

        return view('Admin.Company.index');
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View
    {
        return view('Admin.Company.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:companies,name',
            'email' => 'required|email|unique:companies,email',
            'contact_no' => 'required|digits:10|unique:companies,contact_no',
            'address_line1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'pf_applicable' => 'required',
            'status' => 'required',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ],[
            'name.required' => 'Company name is required.',
            'name.unique' => 'Company name already exists.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email.',
            'email.unique' => 'Email already exists.',
            'contact_no.required' => 'Contact number is required.',
            'contact_no.unique' => 'Contact number already exists.',
            'contact_no.digits' => 'Contact number must be 10 digits.',
            'address_line1.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'pf_applicable.required' => 'Select PF Applicable.',
            'status.required' => 'Select Status.',
            'logo.image' => 'Only image files are allowed.',
            'logo.mimes' => 'Logo must be jpg, jpeg or png.',
            'logo.max' => 'Logo size must be less than 2MB.',
        ]);

        $company = new Company();

        $company->name = $request->name;
        $company->email = $request->email;
        $company->contact_no = $request->contact_no;
        $company->address_line1 = $request->address_line1;
        $company->address_line2 = $request->address_line2;
        $company->city = $request->city;
        $company->state = $request->state;
        $company->zip_code = $request->zip_code;
        $company->pf_applicable = $request->pf_applicable;
        $company->status = $request->status;

        if ($request->hasFile('logo')) {
            $company->logo = $request->file('logo')->store('companies', 'public');
        }

        $company->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Company created successfully.',
                'redirect' => route('companies.index')
            ]);
        }

        return redirect()->route('companies.index')
                         ->with('success', 'Company created successfully.');
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): View
    {
        return view('Admin.Company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:255|unique:companies,name,' . $company->id,
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'contact_no' => 'required|digits:10|unique:companies,contact_no,' . $company->id,
            'address_line1' => 'required',
            'city' => 'required',   
            'state' => 'required',
            'zip_code' => 'required',
            'pf_applicable' => 'required',
            'status' => 'required',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ],[
            'name.required' => 'Company name is required.',
            'name.unique' => 'Company name already exists.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email.',
            'email.unique' => 'Email already exists.',
            'contact_no.required' => 'Contact number is required.',
            'contact_no.unique' => 'Contact number already exists.',
            'contact_no.digits' => 'Contact number must be 10 digits.',
            'address_line1.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'pf_applicable.required' => 'Select PF Applicable.',
            'status.required' => 'Select Status.',
            'logo.image' => 'Only image files are allowed.',
            'logo.mimes' => 'Logo must be jpg, jpeg or png.',
            'logo.max' => 'Logo size must be less than 2MB.',
        ]);

        $company->name = $request->name;
        $company->email = $request->email;
        $company->contact_no = $request->contact_no;
        $company->address_line1 = $request->address_line1;
        $company->address_line2 = $request->address_line2;
        $company->city = $request->city;
        $company->state = $request->state;
        $company->zip_code = $request->zip_code;
        $company->pf_applicable = $request->pf_applicable;
        $company->status = $request->status;

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->file('logo')->store('companies', 'public');
        }

        $company->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Company updated successfully.',
                'redirect' => route('companies.index')
            ]);
        }

        return redirect()->route('companies.index')
                         ->with('success', 'Company updated successfully.');
    }
}
