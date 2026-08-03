<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserCompany;
use App\Services\CompanyScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $selectedCompanyId = CompanyScope::id();

        if ($request->ajax()) {
            $employees = Employee::forCurrentCompany()
                ->with(['company', 'branch', 'department'])
                ->withCount('salaries')
                ->when($selectedCompanyId === null && $request->filled('company_id'), function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
                ->when($request->filled('branch_id'), function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->when($request->filled('employment_type'), function ($query) use ($request) {
                    return $query->where('employment_type', $request->employment_type);
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->orderBy('id', 'desc');

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->company ? e($row->company->name) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? e($row->branch->name) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('department_name', function ($row) {
                    return $row->department ? e($row->department->name) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('salary', function ($row) {
                    return '<a href="' . route('employees.salaries.index', $row->id) . '" 
                        class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" 
                        title="Manage Salary">
                        Salary(' . $row->salaries_count . ')
                    </a>';
                })
                ->editColumn('name', function ($row) {
                    return '<a href="' . route('employees.show', $row->id) . '" class="fw-semibold text-primary text-decoration-none" title="View Employee Profile">' . e($row->name) . '</a>';
                })
                ->addColumn('view', function ($row) {
                    return '<a href="' . route('employees.show', $row->id) . '" class="btn btn-xs btn-info text-white py-0 px-1" title="View Profile">
                                <i class="bi bi-eye"></i>
                            </a>';
                })
                ->addColumn('edit', function ($row) {
                    return '<a href="' . route('employees.edit', $row->id) . '" class="btn btn-xs btn-primary py-0 px-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>';
                })
                ->addColumn('delete', function ($row) {
                    return '<button type="button" class="btn btn-xs btn-danger text-white py-0 px-1 btn-delete" data-url="' . route('employees.destroy', $row->id) . '" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>';
                })
                ->editColumn('status', function ($row) {
                    $status = strtolower($row->status);
                    $badgeClass = $status === 'active' ? 'bg-warning text-dark' : 'bg-danger text-white';
                    $statusLabel = ucfirst($status);
                    $leaveDateStr = '';
                    if (!empty($row->leave_date)) {
                        $leaveDateStr = ($row->leave_date instanceof \DateTimeInterface) 
                            ? $row->leave_date->format('Y-m-d') 
                            : date('Y-m-d', strtotime($row->leave_date));
                    }
                    $reasonStr = e($row->disable_reason ?? '');

                    return '<button type="button" 
                                class="badge ' . $badgeClass . ' border-0 px-2 py-1 btn-status-modal" 
                                style="cursor: pointer;"
                                data-bs-toggle="modal" 
                                data-bs-target="#statusUpdateModal"
                                data-id="' . $row->id . '" 
                                data-name="' . e($row->name) . '" 
                                data-status="' . $status . '" 
                                data-leave-date="' . $leaveDateStr . '" 
                                data-reason="' . $reasonStr . '" 
                                data-url="' . route('employees.update-status', $row->id) . '" 
                                title="Click to change status">
                                ' . $statusLabel . '
                            </button>';
                })
                ->editColumn('email', function ($row) {
                    return '<a href="mailto:' . e($row->email) . '" class="text-decoration-none text-body">' . e($row->email) . '</a>';
                })
                ->rawColumns(['company_name', 'branch_name', 'department_name', 'salary', 'name', 'view', 'edit', 'delete', 'status', 'email'])
                ->make(true);
        }

        $companies = CompanyScope::companies();
        $branches = Branch::forCurrentCompany()->where('status', 'active')->orderBy('name', 'asc')->get();

        return view('Admin.Employee.index', compact('companies', 'branches'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $companies = CompanyScope::companies();
        $departments = Department::where('status', 'active')->orderBy('name', 'asc')->get();
        $selectedCompanyId = CompanyScope::id();

        return view('Admin.Employee.create', compact('companies', 'departments', 'selectedCompanyId'));
    }

    /**
     * Get branches for a specific company (AJAX helper).
     */
    public function getBranches(Company $company)
    {
        if (CompanyScope::id() !== null && CompanyScope::id() !== (int)$company->id) {
            return response()->json([], 403);
        }

        $branches = Branch::where('company_id', $company->id)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($branches);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        if (CompanyScope::id() !== null) {
            $request->merge(['company_id' => CompanyScope::id()]);
        }
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'employee_code' => 'required|unique:employees,employee_code',
            'name' => 'required|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'password' => 'required|min:6',
            'mobile' => 'required|digits:10',
            'status' => 'required',
            'designation' => 'required|max:255',
            'joining_date' => 'required|date',
            'reporting_to' => 'required|max:255',
            'employment_type' => 'required',
            'father_name' => 'required|max:255',
            'gender' => 'required',
            'marital_status' => 'required',
            'dob' => 'required|date',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zip_code' => 'required|max:20',
            'address_line1' => 'required|max:255',
            'accommodation_type' => 'required',
            'account_holder_name' => 'required|max:255',
            'account_no' => 'required|max:50',
            'ifsc_code' => 'required|max:20',
            'bank_name' => 'required|max:255',
            'bank_branch_name' => 'required|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_card' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'aadhar_card' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'cancelled_cheque' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ], [
            'company_id.required' => 'Select Company.',
            'company_id.exists' => 'Selected company is invalid.',
            'branch_id.required' => 'Select Branch.',
            'branch_id.exists' => 'Selected branch is invalid.',
            'department_id.required' => 'Select Department.',
            'department_id.exists' => 'Selected department is invalid.',
            'employee_code.required' => 'Employee code is required.',
            'employee_code.unique' => 'Employee code already exists.',
            'name.required' => 'Employee name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'Email already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be 10 digits.',
            'status.required' => 'Select Status.',
            'designation.required' => 'Designation is required.',
            'joining_date.required' => 'Joining date is required.',
            'joining_date.date' => 'Enter a valid joining date.',
            'reporting_to.required' => 'Reporting manager/code is required.',
            'employment_type.required' => 'Select Employment Type.',
            'father_name.required' => 'Father\'s name is required.',
            'gender.required' => 'Select Gender.',
            'marital_status.required' => 'Select Marital Status.',
            'dob.required' => 'Date of birth is required.',
            'dob.date' => 'Enter a valid date of birth.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'address_line1.required' => 'Address line 1 is required.',
            'accommodation_type.required' => 'Select Accommodation Type.',
            'account_holder_name.required' => 'Account holder name is required.',
            'account_no.required' => 'Bank account number is required.',
            'ifsc_code.required' => 'IFSC code is required.',
            'bank_name.required' => 'Bank name is required.',
            'bank_branch_name.required' => 'Bank branch name is required.',
            'photo.image' => 'Photo must be an image file.',
            'photo.mimes' => 'Photo must be a file of type: jpg, jpeg, png.',
            'photo.max' => 'Photo size must not exceed 2MB.',
            'pan_card.max' => 'PAN Card file size must not exceed 2MB.',
            'aadhar_card.max' => 'Aadhaar Card file size must not exceed 2MB.',
            'cancelled_cheque.max' => 'Cancelled Cheque file size must not exceed 2MB.',
            'resume.max' => 'Resume file size must not exceed 2MB.',
        ]);

        DB::beginTransaction();

        try {
            // Create user for login integration
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = Hash::make($request->password);
            $user->role = 'employee';
            $user->status = $request->status;
            $user->save();

            // Create employee record using normal property assignments
            $employee = new Employee();
            $employee->user_id = $user->id;
            $employee->company_id = $request->company_id;
            $employee->branch_id = $request->branch_id;
            $employee->department_id = $request->department_id;
            $employee->fill($request->all());

            if ($request->hasFile('photo')) {
                $employee->photo = $request->file('photo')->store('employees/photos', 'public');
            }
            if ($request->hasFile('pan_card')) {
                $employee->pan_card = $request->file('pan_card')->store('employees/pan', 'public');
            }
            if ($request->hasFile('aadhar_card')) {
                $employee->aadhar_card = $request->file('aadhar_card')->store('employees/aadhar', 'public');
            }
            if ($request->hasFile('cancelled_cheque')) {
                $employee->cancelled_cheque = $request->file('cancelled_cheque')->store('employees/cheques', 'public');
            }
            if ($request->hasFile('resume')) {
                $employee->resume = $request->file('resume')->store('employees/resumes', 'public');
            }

            $employee->save();

            // Link user to company in user_companies mapping table
            UserCompany::updateOrInsert(
                ['user_id' => $user->id, 'company_id' => $employee->company_id],
                ['is_default' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
            );

            DB::commit();

            session()->flash('success', 'Employee created successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Employee created successfully.',
                'redirect' => route('employees.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while saving: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): View
    {
        $companies = CompanyScope::companies();
        $branches = Branch::where('company_id', $employee->company_id)->orderBy('name', 'asc')->get();
        $departments = Department::where('status', 'active')->orderBy('name', 'asc')->get();
        $selectedCompanyId = CompanyScope::id();

        return view('Admin.Employee.edit', compact('employee', 'companies', 'branches', 'departments', 'selectedCompanyId'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        if (CompanyScope::id() !== null) {
            $request->merge(['company_id' => CompanyScope::id()]);
        }
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id,
            'name' => 'required|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|unique:users,email,' . ($employee->user_id ?? 0),
            'password' => 'nullable|min:6',
            'mobile' => 'required|digits:10',
            'status' => 'required',
            'designation' => 'required|max:255',
            'joining_date' => 'required|date',
            'reporting_to' => 'required|max:255',
            'employment_type' => 'required',
            'father_name' => 'required|max:255',
            'gender' => 'required',
            'marital_status' => 'required',
            'dob' => 'required|date',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zip_code' => 'required|max:20',
            'address_line1' => 'required|max:255',
            'accommodation_type' => 'required',
            'account_holder_name' => 'required|max:255',
            'account_no' => 'required|max:50',
            'ifsc_code' => 'required|max:20',
            'bank_name' => 'required|max:255',
            'bank_branch_name' => 'required|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_card' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'aadhar_card' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'cancelled_cheque' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ], [
            'company_id.required' => 'Select Company.',
            'company_id.exists' => 'Selected company is invalid.',
            'branch_id.required' => 'Select Branch.',
            'branch_id.exists' => 'Selected branch is invalid.',
            'department_id.required' => 'Select Department.',
            'department_id.exists' => 'Selected department is invalid.',
            'employee_code.required' => 'Employee code is required.',
            'employee_code.unique' => 'Employee code already exists.',
            'name.required' => 'Employee name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'Email already exists.',
            'password.min' => 'Password must be at least 6 characters.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be 10 digits.',
            'status.required' => 'Select Status.',
            'designation.required' => 'Designation is required.',
            'joining_date.required' => 'Joining date is required.',
            'joining_date.date' => 'Enter a valid joining date.',
            'reporting_to.required' => 'Reporting manager/code is required.',
            'employment_type.required' => 'Select Employment Type.',
            'father_name.required' => 'Father\'s name is required.',
            'gender.required' => 'Select Gender.',
            'marital_status.required' => 'Select Marital Status.',
            'dob.required' => 'Date of birth is required.',
            'dob.date' => 'Enter a valid date of birth.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'address_line1.required' => 'Address line 1 is required.',
            'accommodation_type.required' => 'Select Accommodation Type.',
            'account_holder_name.required' => 'Account holder name is required.',
            'account_no.required' => 'Bank account number is required.',
            'ifsc_code.required' => 'IFSC code is required.',
            'bank_name.required' => 'Bank name is required.',
            'bank_branch_name.required' => 'Bank branch name is required.',
            'photo.image' => 'Photo must be an image file.',
            'photo.mimes' => 'Photo must be a file of type: jpg, jpeg, png.',
            'photo.max' => 'Photo size must not exceed 2MB.',
            'pan_card.max' => 'PAN Card file size must not exceed 2MB.',
            'aadhar_card.max' => 'Aadhaar Card file size must not exceed 2MB.',
            'cancelled_cheque.max' => 'Cancelled Cheque file size must not exceed 2MB.',
            'resume.max' => 'Resume file size must not exceed 2MB.',
        ]);

        DB::beginTransaction();

        try {
            // Synchronize corresponding user record
            $user = User::find($employee->user_id);
            if (!$user && $employee->email) {
                $user = User::where('email', $employee->email)->first();
            }
            if ($user) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
                $user->status = $request->status;
                $user->save();
            }

            // Update employee record using normal property assignments
            $employee->company_id = $request->company_id;
            $employee->branch_id = $request->branch_id;
            $employee->department_id = $request->department_id;
            $employee->update($request->all());

            if ($request->hasFile('photo')) {
                if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $employee->photo = $request->file('photo')->store('employees/photos', 'public');
            }
            if ($request->hasFile('pan_card')) {
                if ($employee->pan_card && Storage::disk('public')->exists($employee->pan_card)) {
                    Storage::disk('public')->delete($employee->pan_card);
                }
                $employee->pan_card = $request->file('pan_card')->store('employees/pan', 'public');
            }
            if ($request->hasFile('aadhar_card')) {
                if ($employee->aadhar_card && Storage::disk('public')->exists($employee->aadhar_card)) {
                    Storage::disk('public')->delete($employee->aadhar_card);
                }
                $employee->aadhar_card = $request->file('aadhar_card')->store('employees/aadhar', 'public');
            }
            if ($request->hasFile('cancelled_cheque')) {
                if ($employee->cancelled_cheque && Storage::disk('public')->exists($employee->cancelled_cheque)) {
                    Storage::disk('public')->delete($employee->cancelled_cheque);
                }
                $employee->cancelled_cheque = $request->file('cancelled_cheque')->store('employees/cheques', 'public');
            }
            if ($request->hasFile('resume')) {
                if ($employee->resume && Storage::disk('public')->exists($employee->resume)) {
                    Storage::disk('public')->delete($employee->resume);
                }
                $employee->resume = $request->file('resume')->store('employees/resumes', 'public');
            }

            $employee->save();

            DB::commit();

            session()->flash('success', 'Employee updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Employee updated successfully.',
                'redirect' => route('employees.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        DB::beginTransaction();

        try {
            // Delete uploaded files
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            if ($employee->pan_card && Storage::disk('public')->exists($employee->pan_card)) {
                Storage::disk('public')->delete($employee->pan_card);
            }
            if ($employee->aadhar_card && Storage::disk('public')->exists($employee->aadhar_card)) {
                Storage::disk('public')->delete($employee->aadhar_card);
            }
            if ($employee->cancelled_cheque && Storage::disk('public')->exists($employee->cancelled_cheque)) {
                Storage::disk('public')->delete($employee->cancelled_cheque);
            }
            if ($employee->resume && Storage::disk('public')->exists($employee->resume)) {
                Storage::disk('public')->delete($employee->resume);
            }

            // Delete related user record
            if ($employee->user_id) {
                User::where('id', $employee->user_id)->delete();
            } else if ($employee->email) {
                User::where('email', $employee->email)->delete();
            }

            // Soft delete employee record
            $employee->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Employee deleted successfully.'
                ]);
            }

            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete employee: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete employee.');
        }
    }

    /**
     * Update employee status (Active / Inactive) with leave date and remark.
     */
    public function updateStatus(Request $request, Employee $employee)
    {
        if (CompanyScope::id() !== null && (int)$employee->company_id !== (int)CompanyScope::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access to employee record.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,disabled',
            'leave_date' => 'required_if:status,inactive,disabled|nullable|date',
            'disable_reason' => 'nullable|string|max:1000',
        ], [
            'leave_date.required_if' => 'Effective Leave Date is required when status is set to Inactive.',
            'leave_date.date' => 'Enter a valid date for Effective Leave Date.',
        ]);

        DB::beginTransaction();

        try {
            $newStatus = strtolower($request->status);

            if ($newStatus === 'inactive' || $newStatus === 'disabled') {
                $employee->status = 'inactive';
                $employee->leave_date = $request->leave_date;
                $employee->disable_reason = $request->disable_reason;
                $employee->disabled_by = auth()->id();
                $employee->disabled_at = now();

                // Deactivate user login account
                if ($employee->user_id) {
                    User::where('id', $employee->user_id)->update(['status' => 'inactive']);
                } elseif ($employee->email) {
                    User::where('email', $employee->email)->update(['status' => 'inactive']);
                }
            } else {
                $employee->status = 'active';
                $employee->leave_date = null;
                $employee->disable_reason = null;
                $employee->disabled_by = null;
                $employee->disabled_at = null;

                // Activate user login account
                if ($employee->user_id) {
                    User::where('id', $employee->user_id)->update(['status' => 'active']);
                } elseif ($employee->email) {
                    User::where('email', $employee->email)->update(['status' => 'active']);
                }
            }

            $employee->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Employee status updated to ' . ucfirst($employee->status) . ' successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the 360 degree profile of the specified employee.
     */
    public function show(Employee $employee): View
    {
        // Security: CompanyScope validation
        if (CompanyScope::id() !== null && (int)$employee->company_id !== (int)CompanyScope::id()) {
            abort(403, 'Unauthorized access to employee record.');
        }

        // Eager load all relations to prevent N+1 queries
        $employee->load([
            'company',
            'branch',
            'department',
            'user',
            'salaries' => function ($q) {
                $q->orderBy('effective_from', 'desc')->orderBy('id', 'desc');
            },
            'monthlyAttendanceDetails' => function ($q) {
                $q->whereHas('attendanceMonth', function ($am) {
                    $am->forCurrentCompany();
                })->with(['attendanceMonth.company', 'attendanceMonth.branch', 'attendanceMonth.creator'])
                    ->orderBy('id', 'desc');
            },
            'payrollDetails' => function ($q) {
                $q->whereHas('payroll', function ($pr) {
                    $pr->forCurrentCompany();
                })->with(['payroll.company', 'payroll.branch', 'payroll.creator'])
                    ->orderBy('id', 'desc');
            },
        ]);

        // 1. Calculate Reporting Manager
        $reportingManager = null;
        if (!empty($employee->reporting_to)) {
            $reportingManager = Employee::forCurrentCompany()
                ->where(function ($q) use ($employee) {
                    $q->where('employee_code', $employee->reporting_to)
                        ->orWhere('name', $employee->reporting_to)
                        ->orWhere('id', $employee->reporting_to);
                })
                ->first();
        }

        // 2. Calculate Employee Experience (Years, Months, Days)
        $experienceStr = 'N/A';
        $experienceParts = ['years' => 0, 'months' => 0, 'days' => 0];
        if ($employee->joining_date) {
            $joiningDate = Carbon::parse($employee->joining_date);
            $now = Carbon::now();
            $diff = $joiningDate->diff($now);

            $experienceParts = [
                'years' => $diff->y,
                'months' => $diff->m,
                'days' => $diff->d,
            ];

            $strArr = [];
            if ($diff->y > 0) {
                $strArr[] = $diff->y . ' ' . ($diff->y === 1 ? 'Year' : 'Years');
            }
            if ($diff->m > 0) {
                $strArr[] = $diff->m . ' ' . ($diff->m === 1 ? 'Month' : 'Months');
            }
            if ($diff->d > 0 && $diff->y === 0) {
                $strArr[] = $diff->d . ' ' . ($diff->d === 1 ? 'Day' : 'Days');
            }
            $experienceStr = !empty($strArr) ? implode(' ', $strArr) : '0 Days';
        }

        // 3. Calculate Current Active Salary Configuration & Status Badges
        $today = Carbon::today()->toDateString();
        $currentSalary = $employee->salaries->first(function ($s) use ($today) {
            return strtolower($s->status) === 'active'
                && $s->effective_from <= $today
                && (is_null($s->effective_to) || $s->effective_to >= $today);
        });

        if (!$currentSalary) {
            $currentSalary = $employee->salaries->firstWhere('status', 'active') ?? $employee->salaries->first();
        }

        $salaryStatus = 'Missing';
        $salaryBadgeClass = 'bg-danger';
        if ($currentSalary) {
            if (strtolower($currentSalary->status) === 'active') {
                if ($currentSalary->effective_to && $currentSalary->effective_to < $today) {
                    $salaryStatus = 'Expired';
                    $salaryBadgeClass = 'bg-secondary';
                } elseif ($currentSalary->effective_from > $today) {
                    $salaryStatus = 'Future';
                    $salaryBadgeClass = 'bg-info text-dark';
                } else {
                    $salaryStatus = 'Active';
                    $salaryBadgeClass = 'bg-success';
                }
            } elseif ($currentSalary->effective_to && $currentSalary->effective_to < $today) {
                $salaryStatus = 'Expired';
                $salaryBadgeClass = 'bg-secondary';
            } elseif ($currentSalary->effective_from > $today) {
                $salaryStatus = 'Future';
                $salaryBadgeClass = 'bg-info text-dark';
            } else {
                $salaryStatus = ucfirst($currentSalary->status);
                $salaryBadgeClass = 'bg-warning text-dark';
            }
        }

        // 4. Calculate Current Month Attendance Summary & Attendance Status
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        $currentMonthAttDetail = $employee->monthlyAttendanceDetails->first(function ($det) use ($currentMonth, $currentYear) {
            return $det->attendanceMonth
                && (int)$det->attendanceMonth->month === $currentMonth
                && (int)$det->attendanceMonth->year === $currentYear;
        });

        if (!$currentMonthAttDetail) {
            // Fallback to latest available attendance detail
            $currentMonthAttDetail = $employee->monthlyAttendanceDetails->sortByDesc(function ($det) {
                return $det->attendanceMonth ? ($det->attendanceMonth->year * 100 + $det->attendanceMonth->month) : 0;
            })->first();
        }

        $attendanceStatus = $currentMonthAttDetail ? 'Completed' : 'Missing';
        $attendanceBadgeClass = $currentMonthAttDetail ? 'bg-success' : 'bg-danger';

        // 5. Calculate Latest Payroll Summary & Payroll Status
        $latestPayrollDetail = $employee->payrollDetails->sortByDesc(function ($p) {
            return $p->payroll ? ($p->payroll->year * 100 + $p->payroll->month) : 0;
        })->first();

        $currentMonthPayroll = $employee->payrollDetails->first(function ($p) use ($currentMonth, $currentYear) {
            return $p->payroll
                && (int)$p->payroll->month === $currentMonth
                && (int)$p->payroll->year === $currentYear;
        });

        $payrollStatus = 'Pending';
        $payrollBadgeClass = 'bg-warning text-dark';

        $activePayrollDetail = $currentMonthPayroll ?? $latestPayrollDetail;
        if ($activePayrollDetail && $activePayrollDetail->payroll) {
            $statusName = $activePayrollDetail->payroll->status ?? 'Generated';
            $payrollStatus = ucfirst($statusName);
            $payrollBadgeClass = match (strtolower($statusName)) {
                'generated' => 'bg-success',
                'locked' => 'bg-warning text-dark',
                'paid' => 'bg-info text-dark',
                'draft' => 'bg-secondary',
                default => 'bg-primary',
            };
        }

        // 6. Documents List Preparation
        $documents = [];
        $docKeys = [
            'photo' => 'Employee Photo',
            'pan_card' => 'PAN Card',
            'aadhar_card' => 'Aadhaar Card',
            'cancelled_cheque' => 'Cancelled Cheque',
            'resume' => 'Resume',
        ];

        foreach ($docKeys as $field => $label) {
            if ($employee->$field && Storage::disk('public')->exists($employee->$field)) {
                $filePath = $employee->$field;
                $fullPath = Storage::disk('public')->path($filePath);
                $sizeBytes = file_exists($fullPath) ? filesize($fullPath) : 0;
                $formattedSize = $sizeBytes > 0 ? round($sizeBytes / 1024, 1) . ' KB' : 'N/A';

                $documents[] = [
                    'key' => $field,
                    'label' => $label,
                    'file_name' => basename($filePath),
                    'uploaded_date' => date('d M Y', filemtime($fullPath) ?: time()),
                    'file_size' => $formattedSize,
                    'url' => asset('storage/' . $filePath),
                ];
            }
        }

        // 7. Dynamic Timeline Construction (Sorted Latest First)
        $timelineEvents = [];

        // Event: Created
        if ($employee->created_at) {
            $timelineEvents[] = [
                'date' => $employee->created_at->format('d M Y'),
                'time' => $employee->created_at->format('h:i A'),
                'timestamp' => $employee->created_at->timestamp,
                'title' => 'Employee Profile Created',
                'description' => 'Employee account was added to the HRMS portal.',
                'performed_by' => 'System / HR Admin',
                'badge' => 'bg-primary'
            ];
        }

        // Event: Joined
        if ($employee->joining_date) {
            $joiningTime = Carbon::parse($employee->joining_date);
            $timelineEvents[] = [
                'date' => $joiningTime->format('d M Y'),
                'time' => '09:00 AM',
                'timestamp' => $joiningTime->timestamp,
                'title' => 'Joined Company',
                'description' => 'Officially joined ' . e($employee->company->name ?? 'Company') . ' as ' . e($employee->designation ?? 'Employee') . '.',
                'performed_by' => 'HR Department',
                'badge' => 'bg-success'
            ];
        }

        // Event: Salary History Timeline
        foreach ($employee->salaries as $sal) {
            $eventTime = $sal->created_at ?? Carbon::parse($sal->effective_from);
            $timelineEvents[] = [
                'date' => $eventTime->format('d M Y'),
                'time' => $eventTime->format('h:i A'),
                'timestamp' => $eventTime->timestamp,
                'title' => 'Salary Configured / Updated',
                'description' => 'Salary effective from ' . date('d M Y', strtotime($sal->effective_from)) . '. Gross: ₹ ' . number_format($sal->gross_salary, 2) . ', Net: ₹ ' . number_format($sal->net_salary, 2),
                'performed_by' => 'HR / Accounts',
                'badge' => 'bg-info'
            ];
        }

        // Event: Attendance History Timeline
        foreach ($employee->monthlyAttendanceDetails as $attDet) {
            if ($attDet->attendanceMonth) {
                $monthName = Carbon::createFromDate($attDet->attendanceMonth->year, $attDet->attendanceMonth->month, 1)->format('F Y');
                $eventTime = $attDet->created_at ?? Carbon::createFromDate($attDet->attendanceMonth->year, $attDet->attendanceMonth->month, 1);
                $timelineEvents[] = [
                    'date' => $eventTime->format('d M Y'),
                    'time' => $eventTime->format('h:i A'),
                    'timestamp' => $eventTime->timestamp,
                    'title' => 'Attendance Generated (' . $monthName . ')',
                    'description' => 'Attendance recorded: ' . $attDet->net_present . ' Present Days, ' . $attDet->leave_taken . ' Leaves, ' . $attDet->payable_days . ' Payable Days.',
                    'performed_by' => $attDet->attendanceMonth->creator ? $attDet->attendanceMonth->creator->name : 'System Admin',
                    'badge' => 'bg-secondary'
                ];
            }
        }

        // Event: Payroll History Timeline
        foreach ($employee->payrollDetails as $payDet) {
            if ($payDet->payroll) {
                $monthName = Carbon::createFromDate($payDet->payroll->year, $payDet->payroll->month, 1)->format('F Y');
                $eventTime = $payDet->created_at ?? Carbon::createFromDate($payDet->payroll->year, $payDet->payroll->month, 1);
                $timelineEvents[] = [
                    'date' => $eventTime->format('d M Y'),
                    'time' => $eventTime->format('h:i A'),
                    'timestamp' => $eventTime->timestamp,
                    'title' => 'Payroll Processed (' . $monthName . ')',
                    'description' => 'Monthly payslip generated. Earned Salary: ₹ ' . number_format($payDet->earned_salary, 2) . ', Net Salary: ₹ ' . number_format($payDet->net_salary, 2),
                    'performed_by' => $payDet->payroll->creator ? $payDet->payroll->creator->name : 'System Admin',
                    'badge' => 'bg-warning text-dark'
                ];
            }
        }

        // Sort timeline latest first
        usort($timelineEvents, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return view('Admin.Employee.show', compact(
            'employee',
            'reportingManager',
            'experienceStr',
            'experienceParts',
            'currentSalary',
            'salaryStatus',
            'salaryBadgeClass',
            'currentMonthAttDetail',
            'attendanceStatus',
            'attendanceBadgeClass',
            'latestPayrollDetail',
            'currentMonthPayroll',
            'payrollStatus',
            'payrollBadgeClass',
            'documents',
            'timelineEvents'
        ));
    }

    /**
     * Export / Print printable A4 Employee Profile PDF.
     */
    public function exportPdf(Employee $employee): View
    {
        // Security: CompanyScope validation
        if (CompanyScope::id() !== null && (int)$employee->company_id !== (int)CompanyScope::id()) {
            abort(403, 'Unauthorized access to employee record.');
        }

        $employee->load([
            'company',
            'branch',
            'department',
            'user',
            'salaries' => function ($q) {
                $q->orderBy('effective_from', 'desc')->orderBy('id', 'desc');
            },
            'monthlyAttendanceDetails' => function ($q) {
                $q->whereHas('attendanceMonth', function ($am) {
                    $am->forCurrentCompany();
                })->with(['attendanceMonth'])
                    ->orderBy('id', 'desc');
            },
            'payrollDetails' => function ($q) {
                $q->whereHas('payroll', function ($pr) {
                    $pr->forCurrentCompany();
                })->with(['payroll'])
                    ->orderBy('id', 'desc');
            },
        ]);

        $today = Carbon::today()->toDateString();
        $currentSalary = $employee->salaries->first(function ($s) use ($today) {
            return strtolower($s->status) === 'active'
                && $s->effective_from <= $today
                && (is_null($s->effective_to) || $s->effective_to >= $today);
        }) ?? ($employee->salaries->firstWhere('status', 'active') ?? $employee->salaries->first());

        $latestAttendance = $employee->monthlyAttendanceDetails->first();
        $latestPayroll = $employee->payrollDetails->first();

        $experienceStr = 'N/A';
        if ($employee->joining_date) {
            $diff = Carbon::parse($employee->joining_date)->diff(Carbon::now());
            $strArr = [];
            if ($diff->y > 0) $strArr[] = $diff->y . ' ' . ($diff->y === 1 ? 'Year' : 'Years');
            if ($diff->m > 0) $strArr[] = $diff->m . ' ' . ($diff->m === 1 ? 'Month' : 'Months');
            $experienceStr = !empty($strArr) ? implode(' ', $strArr) : '0 Days';
        }

        return view('Admin.Employee.pdf', compact(
            'employee',
            'currentSalary',
            'latestAttendance',
            'latestPayroll',
            'experienceStr'
        ));
    }
}
