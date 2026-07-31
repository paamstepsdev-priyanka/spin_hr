@extends('layouts.employee')

@section('title', 'My Profile')

@section('content')
<!-- Header Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-body">My Profile</h4>
        <p class="text-muted small mb-0">View your personal and employment records (Read-Only).</p>
    </div>
    <div>
        <a href="{{ route('employee.profile.pdf') }}" class="btn btn-primary btn-sm fw-semibold" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download Profile PDF
        </a>
    </div>
</div>

<!-- Enhancement 1: Profile Status Summary Section -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Completion</span>
                <div class="fs-4 fw-bold text-primary mb-1">{{ $completionPercentage }}%</div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $completionPercentage }}%;" aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Profile Status</span>
                <span class="badge {{ $profileStatus === 'Complete' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 fs-7 mt-1">{{ $profileStatus }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Employee Status</span>
                <span class="badge bg-success px-3 py-2 fs-7 mt-1 text-uppercase">{{ $employee->status }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Joining Date</span>
                <div class="fw-bold text-dark mt-1">{{ $employee->joining_date ? date('d/m/Y', strtotime($employee->joining_date)) : 'N/A' }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Experience</span>
                <div class="fw-bold text-info mt-1">{{ $experience }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Reporting Manager</span>
                <div class="fw-bold text-dark mt-1 text-truncate">{{ $employee->reporting_to ?? 'Management' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Profile Content Grid -->
<div class="row g-4 mb-4">
    <!-- Top Identity Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 bg-body-tertiary rounded">
                <div class="row align-items-center">
                    <div class="col-auto text-center">
                        @if(!empty($employee->photo))
                            <img src="{{ asset('storage/' . $employee->photo) }}" alt="Photo" class="rounded-circle border border-3 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="avatar avatar-xl bg-primary text-white rounded-circle fw-bold d-inline-flex align-items-center justify-content-center shadow-sm fs-1 px-3 py-2" style="width: 100px; height: 100px;">
                                {{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="col">
                        <h3 class="fw-bold text-body mb-1">{{ $employee->name }}</h3>
                        <p class="text-muted mb-2">
                            <span class="badge bg-primary me-2"><i class="bi bi-person-badge me-1"></i>{{ $employee->employee_code }}</span>
                            <span class="fw-semibold text-dark">{{ $employee->designation ?? 'N/A' }}</span> — 
                            <span class="text-muted">{{ $employee->department->name ?? 'N/A' }}</span>
                        </p>
                        <div class="small text-muted">
                            <span class="me-3"><i class="bi bi-envelope me-1"></i>{{ $employee->email }}</span>
                            <span><i class="bi bi-telephone me-1"></i>{{ $employee->cell_phone ?? $employee->mobile ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Information Card -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 40%;">Full Name</th>
                            <td class="fw-bold text-dark">{{ $employee->name }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Father's Name</th>
                            <td>{{ $employee->father_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Date of Birth</th>
                            <td>{{ $employee->dob ? date('d/m/Y', strtotime($employee->dob)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Gender</th>
                            <td>{{ ucfirst($employee->gender ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Marital Status</th>
                            <td>{{ ucfirst($employee->marital_status ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Email Address</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Cell / Mobile Phone</th>
                            <td>{{ $employee->cell_phone ?? $employee->mobile ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Employment Information Card -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-briefcase me-2 text-success"></i>Employment Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 40%;">Company</th>
                            <td class="fw-bold text-dark">{{ $employee->company->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Branch</th>
                            <td>{{ $employee->branch->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Department</th>
                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Designation</th>
                            <td>{{ $employee->designation ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Employment Type</th>
                            <td><span class="badge bg-secondary px-2 py-1">{{ $employee->employment_type ?? 'Permanent' }}</span></td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Joining Date</th>
                            <td>{{ $employee->joining_date ? date('d/m/Y', strtotime($employee->joining_date)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Reporting Manager</th>
                            <td>{{ $employee->reporting_to ?? 'Management' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Address Information Card -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-geo-alt me-2 text-warning"></i>Residential Address</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 40%;">Address Line 1</th>
                            <td>{{ $employee->address_line1 ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Address Line 2</th>
                            <td>{{ $employee->address_line2 ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">City</th>
                            <td>{{ $employee->city ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">State</th>
                            <td>{{ $employee->state ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Zip Code</th>
                            <td>{{ $employee->zip_code ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bank Details Card -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-bank me-2 text-info"></i>Bank Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 40%;">Account Holder</th>
                            <td class="fw-bold text-dark">{{ $employee->account_holder_name ?? $employee->name }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Account Number</th>
                            <td class="fw-bold text-primary">{{ $employee->account_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Bank Name</th>
                            <td>{{ $employee->bank_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">IFSC Code</th>
                            <td class="fw-bold">{{ $employee->ifsc_code ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Bank Branch</th>
                            <td>{{ $employee->bank_branch_name ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Emergency Contact Card -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-telephone-plus me-2 text-danger"></i>Emergency Contact</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 40%;">Contact Person</th>
                            <td class="fw-bold text-dark">{{ $employee->contact_person_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Relationship</th>
                            <td>{{ $employee->relationship ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Primary Phone</th>
                            <td>{{ $employee->primary_phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Alternative Phone</th>
                            <td>{{ $employee->alternative_phone ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Government IDs Card -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-shield-check me-2 text-dark"></i>Government IDs</h5>
                <span class="badge bg-secondary px-2 py-1 small">Read Only</span>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 40%;">PAN Number</th>
                            <td class="fw-bold text-dark">{{ $employee->pan_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Aadhaar Number</th>
                            <td class="fw-bold text-dark">{{ $employee->aadhar_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">UAN (Universal Account No)</th>
                            <td>N/A</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">ESIC Number</th>
                            <td>N/A</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
