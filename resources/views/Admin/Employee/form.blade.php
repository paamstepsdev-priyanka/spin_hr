<!-- Section 1: Organization & Work Details -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Organization & Work Details</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Company -->
            <div class="col-md-4">
                <label for="company_id" class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                @if(isset($selectedCompanyId) && $selectedCompanyId !== null)
                    @php
                        $activeCompId = isset($employee) ? $employee->company_id : $selectedCompanyId;
                        $compObj = $companies->firstWhere('id', $activeCompId);
                    @endphp
                    <input type="text" class="form-control bg-light" value="{{ $compObj ? $compObj->name : 'Current Company' }}" readonly>
                    <input type="hidden" name="company_id" id="company_id" value="{{ $activeCompId }}">
                @else
                    <select class="form-select" id="company_id" name="company_id">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (isset($employee) ? $employee->company_id : '') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <div class="text-danger small mt-1" id="company_id-error"></div>
            </div>

            <!-- Branch -->
            <div class="col-md-4">
                <label for="branch_id" class="form-label fw-semibold">Branch <span class="text-danger">*</span></label>
                <select class="form-select" id="branch_id" name="branch_id">
                    <option value="">Select Branch</option>
                    @if(isset($branches))
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ isset($employee) && $employee->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <div class="text-danger small mt-1" id="branch_id-error"></div>
            </div>

            <!-- Department -->
            <div class="col-md-4">
                <label for="department_id" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                <select class="form-select" id="department_id" name="department_id">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ isset($employee) && $employee->department_id == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                <div class="text-danger small mt-1" id="department_id-error"></div>
            </div>

            <!-- Employee Code -->
            <div class="col-md-4">
                <label for="employee_code" class="form-label fw-semibold">Employee Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="employee_code" name="employee_code" value="{{ isset($employee) ? $employee->employee_code : '' }}" placeholder="e.g. EMP-1001">
                <div class="text-danger small mt-1" id="employee_code-error"></div>
            </div>

            <!-- Employment Type -->
            <div class="col-md-4">
                <label for="employment_type" class="form-label fw-semibold">Employment Type <span class="text-danger">*</span></label>
                <select class="form-select" id="employment_type" name="employment_type">
                    <option value="">Select Type</option>
                    <option value="Permanent" {{ isset($employee) && $employee->employment_type === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="Consultant" {{ isset($employee) && $employee->employment_type === 'Consultant' ? 'selected' : '' }}>Consultant</option>
                </select>
                <div class="text-danger small mt-1" id="employment_type-error"></div>
            </div>

            <!-- Designation -->
            <div class="col-md-4">
                <label for="designation" class="form-label fw-semibold">Designation <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="designation" name="designation" value="{{ isset($employee) ? $employee->designation : '' }}" placeholder="e.g. Software Engineer">
                <div class="text-danger small mt-1" id="designation-error"></div>
            </div>

            <!-- Reporting To -->
            <div class="col-md-4">
                <label for="reporting_to" class="form-label fw-semibold">Reporting To <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="reporting_to" name="reporting_to" value="{{ isset($employee) ? $employee->reporting_to : '' }}" placeholder="Manager Name / Code">
                <div class="text-danger small mt-1" id="reporting_to-error"></div>
            </div>

            <!-- Joining Date -->
            <div class="col-md-4">
                <label for="joining_date" class="form-label fw-semibold">Joining Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{ isset($employee) ? $employee->joining_date : '' }}">
                <div class="text-danger small mt-1" id="joining_date-error"></div>
            </div>

            <!-- Status -->
            <div class="col-md-4">
                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="active" {{ (isset($employee) ? $employee->status : 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ (isset($employee) ? $employee->status : '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <div class="text-danger small mt-1" id="status-error"></div>
            </div>

            <!-- Work Phone 1 -->
            <div class="col-md-4">
                <label for="work_phone1" class="form-label fw-semibold">Work Phone 1</label>
                <input type="text" class="form-control" id="work_phone1" name="work_phone1" value="{{ isset($employee) ? $employee->work_phone1 : '' }}" placeholder="e.g. 022-12345678">
                <div class="text-danger small mt-1" id="work_phone1-error"></div>
            </div>

            <!-- Work Phone 2 -->
            <div class="col-md-4">
                <label for="work_phone2" class="form-label fw-semibold">Work Phone 2</label>
                <input type="text" class="form-control" id="work_phone2" name="work_phone2" value="{{ isset($employee) ? $employee->work_phone2 : '' }}" placeholder="Extension or phone">
                <div class="text-danger small mt-1" id="work_phone2-error"></div>
            </div>

            <!-- Cell Phone -->
            <div class="col-md-4">
                <label for="cell_phone" class="form-label fw-semibold">Cell Phone</label>
                <input type="text" class="form-control" id="cell_phone" name="cell_phone" value="{{ isset($employee) ? $employee->cell_phone : '' }}" placeholder="e.g. 9876543210">
                <div class="text-danger small mt-1" id="cell_phone-error"></div>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Personal Details & User Login -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Personal Info & User Credentials</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Full Name -->
            <div class="col-md-4">
                <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($employee) ? $employee->name : '' }}" placeholder="e.g. John Doe">
                <div class="text-danger small mt-1" id="name-error"></div>
            </div>

            <!-- Father Name -->
            <div class="col-md-4">
                <label for="father_name" class="form-label fw-semibold">Father's Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="father_name" name="father_name" value="{{ isset($employee) ? $employee->father_name : '' }}" placeholder="e.g. Robert Doe">
                <div class="text-danger small mt-1" id="father_name-error"></div>
            </div>

            <!-- Email -->
            <div class="col-md-4">
                <label for="email" class="form-label fw-semibold">Email (User Login) <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ isset($employee) ? $employee->email : '' }}" placeholder="e.g. john@example.com">
                <div class="text-danger small mt-1" id="email-error"></div>
            </div>

            <!-- Password -->
            <div class="col-md-4">
                <label for="password" class="form-label fw-semibold">
                    Password 
                    @if(!isset($employee))
                        <span class="text-danger">*</span>
                    @else
                        <small class="text-muted fw-normal">(Leave blank to keep existing)</small>
                    @endif
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ isset($employee) ? 'Leave blank to keep existing' : 'Enter login password' }}">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', 'togglePassIcon')">
                        <i class="bi bi-eye" id="togglePassIcon"></i>
                    </button>
                </div>
                <div class="text-danger small mt-1" id="password-error"></div>
            </div>

            <!-- Mobile -->
            <div class="col-md-4">
                <label for="mobile" class="form-label fw-semibold">Mobile <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="mobile" name="mobile" value="{{ isset($employee) ? $employee->mobile : '' }}" placeholder="e.g. 9876543210">
                <div class="text-danger small mt-1" id="mobile-error"></div>
            </div>

            <!-- Date of Birth -->
            <div class="col-md-4">
                <label for="dob" class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="dob" name="dob" value="{{ isset($employee) ? $employee->dob : '' }}">
                <div class="text-danger small mt-1" id="dob-error"></div>
            </div>

            <!-- Gender -->
            <div class="col-md-4">
                <label for="gender" class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                <select class="form-select" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ isset($employee) && $employee->gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ isset($employee) && $employee->gender === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ isset($employee) && $employee->gender === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <div class="text-danger small mt-1" id="gender-error"></div>
            </div>

            <!-- Marital Status -->
            <div class="col-md-4">
                <label for="marital_status" class="form-label fw-semibold">Marital Status <span class="text-danger">*</span></label>
                <select class="form-select" id="marital_status" name="marital_status">
                    <option value="">Select Marital Status</option>
                    <option value="Single" {{ isset($employee) && $employee->marital_status === 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ isset($employee) && $employee->marital_status === 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Divorced" {{ isset($employee) && $employee->marital_status === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    <option value="Widowed" {{ isset($employee) && $employee->marital_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                </select>
                <div class="text-danger small mt-1" id="marital_status-error"></div>
            </div>
        </div>
    </div>
</div>

<!-- Section 3: Address & Accommodation Details -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Address & Accommodation</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">

            <!-- City -->
            <div class="col-md-4">
                <label for="city" class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="city" name="city" value="{{ isset($employee) ? $employee->city : '' }}" placeholder="e.g. Mumbai">
                <div class="text-danger small mt-1" id="city-error"></div>
            </div>

            <!-- State -->
            <div class="col-md-4">
                <label for="state" class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="state" name="state" value="{{ isset($employee) ? $employee->state : '' }}" placeholder="e.g. Maharashtra">
                <div class="text-danger small mt-1" id="state-error"></div>
            </div>

            <!-- Zip Code -->
            <div class="col-md-4">
                <label for="zip_code" class="form-label fw-semibold">Zip Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ isset($employee) ? $employee->zip_code : '' }}" placeholder="e.g. 400001">
                <div class="text-danger small mt-1" id="zip_code-error"></div>
            </div>

            <!-- Address Line 1 -->
            <div class="col-md-6">
                <label for="address_line1" class="form-label fw-semibold">Address Line 1 <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="address_line1" name="address_line1" value="{{ isset($employee) ? $employee->address_line1 : '' }}" placeholder="Street / Flat / House No">
                <div class="text-danger small mt-1" id="address_line1-error"></div>
            </div>

            <!-- Address Line 2 -->
            <div class="col-md-6">
                <label for="address_line2" class="form-label fw-semibold">Address Line 2</label>
                <input type="text" class="form-control" id="address_line2" name="address_line2" value="{{ isset($employee) ? $employee->address_line2 : '' }}" placeholder="Landmark / Area">
                <div class="text-danger small mt-1" id="address_line2-error"></div>
            </div>

            <!-- Accommodation Type -->
            <div class="col-md-4">
                <label for="accommodation_type" class="form-label fw-semibold">Accommodation Type <span class="text-danger">*</span></label>
                <select class="form-select" id="accommodation_type" name="accommodation_type">
                    <option value="">Select Accommodation Type</option>
                    <option value="Own Accommodation" {{ isset($employee) && $employee->accommodation_type === 'Own Accommodation' ? 'selected' : '' }}>Own Accommodation</option>
                    <option value="Company Accommodation" {{ isset($employee) && $employee->accommodation_type === 'Company Accommodation' ? 'selected' : '' }}>Company Accommodation</option>
                    <option value="Rented Accommodation" {{ isset($employee) && $employee->accommodation_type === 'Rented Accommodation' ? 'selected' : '' }}>Rented Accommodation</option>
                </select>
                <div class="text-danger small mt-1" id="accommodation_type-error"></div>
            </div>

            <!-- Rent Paid by Company -->
            <div class="col-md-4" id="wrapper_rent_paid_by_company">
                <label for="rent_paid_by_company" class="form-label fw-semibold">Rent Paid By Company</label>
                <select class="form-select" id="rent_paid_by_company" name="rent_paid_by_company">
                    <option value="">Select Option</option>
                    <option value="No" {{ isset($employee) && $employee->rent_paid_by_company === 'No' ? 'selected' : '' }}>No</option>
                    <option value="Yes" {{ isset($employee) && $employee->rent_paid_by_company === 'Yes' ? 'selected' : '' }}>Yes</option>
                </select>
                <div class="text-danger small mt-1" id="rent_paid_by_company-error"></div>
            </div>

            <!-- Notional Rent -->
            <div class="col-md-4" id="wrapper_national_rent">
                <label for="national_rent" class="form-label fw-semibold">Notional Rent</label>
                <input type="text" class="form-control" id="national_rent" name="national_rent" value="{{ isset($employee) ? $employee->national_rent : '' }}" placeholder="Notional Rent Amount">
                <div class="text-danger small mt-1" id="national_rent-error"></div>
            </div>

            <!-- Property Owner Name -->
            <div class="col-md-4" id="wrapper_property_owner_name">
                <label for="property_owner_name" class="form-label fw-semibold">Property Owner Name</label>
                <input type="text" class="form-control" id="property_owner_name" name="property_owner_name" value="{{ isset($employee) ? $employee->property_owner_name : '' }}" placeholder="Landlord Name">
                <div class="text-danger small mt-1" id="property_owner_name-error"></div>
            </div>

            <!-- Property Owner Contact -->
            <div class="col-md-4" id="wrapper_property_owner_contact">
                <label for="property_owner_contact" class="form-label fw-semibold">Property Owner Contact</label>
                <input type="text" class="form-control" id="property_owner_contact" name="property_owner_contact" value="{{ isset($employee) ? $employee->property_owner_contact : '' }}" placeholder="Landlord Contact No">
                <div class="text-danger small mt-1" id="property_owner_contact-error"></div>
            </div>
        </div>
    </div>
</div>

<!-- Section 4: Emergency Contact Details -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Emergency Contact Details</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Contact Person Name -->
            <div class="col-md-4">
                <label for="contact_person_name" class="form-label fw-semibold">Contact Person Name</label>
                <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" value="{{ isset($employee) ? $employee->contact_person_name : '' }}" placeholder="e.g. Jane Doe">
                <div class="text-danger small mt-1" id="contact_person_name-error"></div>
            </div>

            <!-- Relationship -->
            <div class="col-md-4">
                <label for="relationship" class="form-label fw-semibold">Relationship</label>
                <input type="text" class="form-control" id="relationship" name="relationship" value="{{ isset($employee) ? $employee->relationship : '' }}" placeholder="e.g. Spouse / Parent / Sibling">
                <div class="text-danger small mt-1" id="relationship-error"></div>
            </div>

            <!-- Primary Phone -->
            <div class="col-md-4">
                <label for="primary_phone" class="form-label fw-semibold">Primary Emergency Phone</label>
                <input type="text" class="form-control" id="primary_phone" name="primary_phone" value="{{ isset($employee) ? $employee->primary_phone : '' }}" placeholder="e.g. 9876543210">
                <div class="text-danger small mt-1" id="primary_phone-error"></div>
            </div>

            <!-- Alternative Phone -->
            <div class="col-md-4">
                <label for="alternative_phone" class="form-label fw-semibold">Alternative Emergency Phone</label>
                <input type="text" class="form-control" id="alternative_phone" name="alternative_phone" value="{{ isset($employee) ? $employee->alternative_phone : '' }}" placeholder="e.g. 9876543211">
                <div class="text-danger small mt-1" id="alternative_phone-error"></div>
            </div>
        </div>
    </div>
</div>

<!-- Section 5: Statutory & Identity Details -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Statutory & Identity Details</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- PAN No -->
            <div class="col-md-4">
                <label for="pan_no" class="form-label fw-semibold">PAN Card Number</label>
                <input type="text" class="form-control text-uppercase" id="pan_no" name="pan_no" value="{{ isset($employee) ? $employee->pan_no : '' }}" placeholder="e.g. ABCDE1234F">
                <div class="text-danger small mt-1" id="pan_no-error"></div>
            </div>

            <!-- Aadhaar No -->
            <div class="col-md-4">
                <label for="aadhar_no" class="form-label fw-semibold">Aadhaar Card Number</label>
                <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" value="{{ isset($employee) ? $employee->aadhar_no : '' }}" placeholder="e.g. 123456789012">
                <div class="text-danger small mt-1" id="aadhar_no-error"></div>
            </div>
        </div>
    </div>
</div>

<!-- Section 6: Bank Account Details -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Bank Account Details</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Account Holder Name -->
            <div class="col-md-4">
                <label for="account_holder_name" class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{ isset($employee) ? $employee->account_holder_name : '' }}" placeholder="Name as per bank">
                <div class="text-danger small mt-1" id="account_holder_name-error"></div>
            </div>

            <!-- Account No -->
            <div class="col-md-4">
                <label for="account_no" class="form-label fw-semibold">Bank Account Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="account_no" name="account_no" value="{{ isset($employee) ? $employee->account_no : '' }}" placeholder="Account Number">
                <div class="text-danger small mt-1" id="account_no-error"></div>
            </div>

            <!-- IFSC Code -->
            <div class="col-md-4">
                <label for="ifsc_code" class="form-label fw-semibold">IFSC Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-uppercase" id="ifsc_code" name="ifsc_code" value="{{ isset($employee) ? $employee->ifsc_code : '' }}" placeholder="e.g. SBIN0001234">
                <div class="text-danger small mt-1" id="ifsc_code-error"></div>
            </div>

            <!-- Bank Name -->
            <div class="col-md-4">
                <label for="bank_name" class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ isset($employee) ? $employee->bank_name : '' }}" placeholder="e.g. State Bank of India">
                <div class="text-danger small mt-1" id="bank_name-error"></div>
            </div>

            <!-- Bank Branch Name -->
            <div class="col-md-4">
                <label for="bank_branch_name" class="form-label fw-semibold">Bank Branch Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="bank_branch_name" name="bank_branch_name" value="{{ isset($employee) ? $employee->bank_branch_name : '' }}" placeholder="e.g. Andheri East Branch">
                <div class="text-danger small mt-1" id="bank_branch_name-error"></div>
            </div>
        </div>
    </div>
</div>

<!-- Section 7: Documents & File Uploads -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary py-3">
        <h5 class="mb-0 fw-bold text-body">Document Uploads</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Photo -->
            <div class="col-md-4">
                <label for="photo" class="form-label fw-semibold">Photo</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                <div class="text-danger small mt-1" id="photo-error"></div>
                @if(isset($employee) && $employee->photo)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $employee->photo) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2">View Existing Photo</a>
                    </div>
                @endif
            </div>

            <!-- PAN Card Document -->
            <div class="col-md-4">
                <label for="pan_card" class="form-label fw-semibold">PAN Card Document</label>
                <input type="file" class="form-control" id="pan_card" name="pan_card" accept="image/*,.pdf">
                <div class="text-danger small mt-1" id="pan_card-error"></div>
                @if(isset($employee) && $employee->pan_card)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $employee->pan_card) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2">View Existing PAN Card</a>
                    </div>
                @endif
            </div>

            <!-- Aadhaar Card Document -->
            <div class="col-md-4">
                <label for="aadhar_card" class="form-label fw-semibold">Aadhaar Card Document</label>
                <input type="file" class="form-control" id="aadhar_card" name="aadhar_card" accept="image/*,.pdf">
                <div class="text-danger small mt-1" id="aadhar_card-error"></div>
                @if(isset($employee) && $employee->aadhar_card)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $employee->aadhar_card) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2">View Existing Aadhaar</a>
                    </div>
                @endif
            </div>

            <!-- Cancelled Cheque -->
            <div class="col-md-4">
                <label for="cancelled_cheque" class="form-label fw-semibold">Cancelled Cheque Document</label>
                <input type="file" class="form-control" id="cancelled_cheque" name="cancelled_cheque" accept="image/*,.pdf">
                <div class="text-danger small mt-1" id="cancelled_cheque-error"></div>
                @if(isset($employee) && $employee->cancelled_cheque)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $employee->cancelled_cheque) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2">View Existing Cheque</a>
                    </div>
                @endif
            </div>

            <!-- Resume -->
            <div class="col-md-4">
                <label for="resume" class="form-label fw-semibold">Resume Document</label>
                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                <div class="text-danger small mt-1" id="resume-error"></div>
                @if(isset($employee) && $employee->resume)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $employee->resume) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2">View Existing Resume</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
}
</script>
