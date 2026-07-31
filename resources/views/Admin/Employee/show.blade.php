@extends('layouts.admin')

@section('title', 'Employee Profile - ' . $employee->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <!-- Main Content Area -->
    <div class="col-lg-12 col-md-8">
        
        <!-- Top Profile Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 border-bottom pb-3 mb-3">
                    <!-- Left Side: Photo & Key Information -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            @if($employee->photo && Storage::disk('public')->exists($employee->photo))
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 80px; height: 80px;">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-body">{{ $employee->name }}</h3>
                            <div class="text-body-secondary small d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-secondary text-white">{{ $employee->employee_code }}</span>
                                <span><i class="bi bi-person-badge me-1"></i>{{ $employee->designation ?? 'N/A' }}</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-diagram-3 me-1"></i>{{ $employee->department->name ?? 'N/A' }}</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-geo-alt me-1"></i>{{ $employee->branch->name ?? 'N/A' }}</span>
                            </div>
                            <div class="text-body-secondary small mt-1">
                                <i class="bi bi-building me-1"></i>{{ $employee->company->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Status Badges & Quick Action Buttons -->
                    <div class="d-flex flex-column align-items-start align-items-md-end gap-2 w-100 w-md-auto">
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge {{ strtolower($employee->status) === 'active' ? 'bg-success' : 'bg-danger' }} px-2 py-1" title="Employee Status">
                                Employee: {{ ucfirst($employee->status) }}
                            </span>
                            <span class="badge {{ $attendanceBadgeClass }} px-2 py-1" title="Attendance Status">
                                Attendance: {{ $attendanceStatus }}
                            </span>
                            <span class="badge {{ $salaryBadgeClass }} px-2 py-1" title="Salary Status">
                                Salary: {{ $salaryStatus }}
                            </span>
                            <span class="badge {{ $payrollBadgeClass }} px-2 py-1" title="Payroll Status">
                                Payroll: {{ $payrollStatus }}
                            </span>
                        </div>

                        <div class="btn-group btn-group-sm mt-2" role="group">
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-outline-primary fw-semibold">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="{{ route('employees.pdf', $employee->id) }}" target="_blank" class="btn btn-outline-danger fw-semibold">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                            </a>
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Header Key Metadata Grid -->
                <div class="row g-3 small">
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Email:</span>
                        <a href="mailto:{{ $employee->email }}" class="text-body fw-semibold text-decoration-none text-truncate d-block">{{ $employee->email }}</a>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Mobile:</span>
                        <span class="fw-semibold text-body">{{ $employee->mobile ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Joining Date:</span>
                        <span class="fw-semibold text-body">{{ $employee->joining_date ? date('d M Y', strtotime($employee->joining_date)) : 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Experience:</span>
                        <span class="fw-semibold text-body">{{ $experienceStr }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Gender:</span>
                        <span class="fw-semibold text-body">{{ ucfirst($employee->gender ?? 'N/A') }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Date of Birth:</span>
                        <span class="fw-semibold text-body">{{ $employee->dob ? date('d M Y', strtotime($employee->dob)) : 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Blood Group:</span>
                        <span class="fw-semibold text-body">N/A</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-body-secondary d-block">Reporting Manager:</span>
                        <span class="fw-semibold text-body">{{ $reportingManager->name ?? ($employee->reporting_to ?? 'N/A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6 Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Present Days -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 text-center py-2 bg-body-tertiary">
                    <div class="card-body p-2">
                        <div class="text-body-secondary small fw-semibold">Present Days</div>
                        <h4 class="fw-bold mb-0 text-success mt-1">
                            {{ $currentMonthAttDetail ? $currentMonthAttDetail->net_present : 0 }}
                        </h4>
                        <div class="text-muted" style="font-size: 0.75rem;">Current Month</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Leave Taken -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 text-center py-2 bg-body-tertiary">
                    <div class="card-body p-2">
                        <div class="text-body-secondary small fw-semibold">Leave Taken</div>
                        <h4 class="fw-bold mb-0 text-danger mt-1">
                            {{ $currentMonthAttDetail ? $currentMonthAttDetail->leave_taken : 0 }}
                        </h4>
                        <div class="text-muted" style="font-size: 0.75rem;">Current Month</div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Payable Days -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 text-center py-2 bg-body-tertiary">
                    <div class="card-body p-2">
                        <div class="text-body-secondary small fw-semibold">Payable Days</div>
                        <h4 class="fw-bold mb-0 text-primary mt-1">
                            {{ $currentMonthAttDetail ? $currentMonthAttDetail->payable_days : 0 }}
                        </h4>
                        <div class="text-muted" style="font-size: 0.75rem;">Current Month</div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Gross Salary -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 text-center py-2 bg-body-tertiary">
                    <div class="card-body p-2">
                        <div class="text-body-secondary small fw-semibold">Gross Salary</div>
                        <h4 class="fw-bold mb-0 text-body mt-1">
                            ₹ {{ $currentSalary ? number_format($currentSalary->gross_salary, 0) : '0' }}
                        </h4>
                        <div class="text-muted" style="font-size: 0.75rem;">Current Active</div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Net Salary -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 text-center py-2 bg-body-tertiary">
                    <div class="card-body p-2">
                        <div class="text-body-secondary small fw-semibold">Net Salary</div>
                        <h4 class="fw-bold mb-0 text-success mt-1">
                            ₹ {{ $currentSalary ? number_format($currentSalary->net_salary, 0) : '0' }}
                        </h4>
                        <div class="text-muted" style="font-size: 0.75rem;">Current Active</div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Last Payroll -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100 text-center py-2 bg-body-tertiary">
                    <div class="card-body p-2">
                        <div class="text-body-secondary small fw-semibold">Last Payroll</div>
                        <h5 class="fw-bold mb-0 text-info text-truncate mt-1">
                            @if($latestPayrollDetail && $latestPayrollDetail->payroll)
                                {{ \Carbon\Carbon::createFromDate($latestPayrollDetail->payroll->year, $latestPayrollDetail->payroll->month, 1)->format('M Y') }}
                            @else
                                N/A
                            @endif
                        </h5>
                        <div class="text-muted" style="font-size: 0.75rem;">Processed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Nav Tabs Container -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 p-0">
                <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="employeeProfileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="tab-profile-btn" data-bs-toggle="tab" data-coreui-toggle="tab" data-bs-target="#tab-profile" data-coreui-target="#tab-profile" type="button" role="tab" aria-controls="tab-profile" aria-selected="true">
                            <i class="bi bi-person me-1"></i>Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-attendance-btn" data-bs-toggle="tab" data-coreui-toggle="tab" data-bs-target="#tab-attendance" data-coreui-target="#tab-attendance" type="button" role="tab" aria-controls="tab-attendance" aria-selected="false">
                            <i class="bi bi-calendar-check me-1"></i>Attendance
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-salary-btn" data-bs-toggle="tab" data-coreui-toggle="tab" data-bs-target="#tab-salary" data-coreui-target="#tab-salary" type="button" role="tab" aria-controls="tab-salary" aria-selected="false">
                            <i class="bi bi-cash-stack me-1"></i>Salary History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-payroll-btn" data-bs-toggle="tab" data-coreui-toggle="tab" data-bs-target="#tab-payroll" data-coreui-target="#tab-payroll" type="button" role="tab" aria-controls="tab-payroll" aria-selected="false">
                            <i class="bi bi-receipt me-1"></i>Payroll History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-documents-btn" data-bs-toggle="tab" data-coreui-toggle="tab" data-bs-target="#tab-documents" data-coreui-target="#tab-documents" type="button" role="tab" aria-controls="tab-documents" aria-selected="false">
                            <i class="bi bi-folder2-open me-1"></i>Documents ({{ count($documents) }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-timeline-btn" data-bs-toggle="tab" data-coreui-toggle="tab" data-bs-target="#tab-timeline" data-coreui-target="#tab-timeline" type="button" role="tab" aria-controls="tab-timeline" aria-selected="false">
                            <i class="bi bi-clock-history me-1"></i>Timeline
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="employeeProfileTabsContent">
                    
                    <!-- TAB 1: PROFILE -->
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel" aria-labelledby="tab-profile-btn">
                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-person-lines-fill me-2"></i>Personal Information
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Employee Code:</span>
                                    <span class="fw-semibold text-body">{{ $employee->employee_code }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Full Name:</span>
                                    <span class="fw-semibold text-body">{{ $employee->name }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Father's Name:</span>
                                    <span class="fw-semibold text-body">{{ $employee->father_name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Gender:</span>
                                    <span class="fw-semibold text-body">{{ ucfirst($employee->gender ?? 'N/A') }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Date of Birth:</span>
                                    <span class="fw-semibold text-body">{{ $employee->dob ? date('d M Y', strtotime($employee->dob)) : 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Blood Group:</span>
                                    <span class="fw-semibold text-body">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Marital Status:</span>
                                    <span class="fw-semibold text-body">{{ ucfirst($employee->marital_status ?? 'N/A') }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Nationality:</span>
                                    <span class="fw-semibold text-body">Indian</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Mobile:</span>
                                    <span class="fw-semibold text-body">{{ $employee->mobile ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Email:</span>
                                    <span class="fw-semibold text-body">{{ $employee->email }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Emergency Contact Person:</span>
                                    <span class="fw-semibold text-body">{{ $employee->contact_person_name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Emergency Phone:</span>
                                    <span class="fw-semibold text-body">{{ $employee->primary_phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-briefcase-fill me-2"></i>Employment Information
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Company:</span>
                                    <span class="fw-semibold text-body">{{ $employee->company->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Branch:</span>
                                    <span class="fw-semibold text-body">{{ $employee->branch->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Department:</span>
                                    <span class="fw-semibold text-body">{{ $employee->department->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Designation:</span>
                                    <span class="fw-semibold text-body">{{ $employee->designation ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Reporting Manager:</span>
                                    <span class="fw-semibold text-body">{{ $reportingManager->name ?? ($employee->reporting_to ?? 'N/A') }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Joining Date:</span>
                                    <span class="fw-semibold text-body">{{ $employee->joining_date ? date('d M Y', strtotime($employee->joining_date)) : 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Confirmation Date:</span>
                                    <span class="fw-semibold text-body">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Experience:</span>
                                    <span class="fw-semibold text-body">{{ $experienceStr }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Employment Type:</span>
                                    <span class="fw-semibold text-body">{{ ucfirst($employee->employment_type ?? 'N/A') }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Employee Status:</span>
                                    <span class="badge {{ strtolower($employee->status) === 'active' ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Accommodation Type:</span>
                                    <span class="fw-semibold text-body">{{ ucfirst($employee->accommodation_type ?? 'N/A') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-geo-alt-fill me-2"></i>Address Information
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-md-6">
                                    <span class="text-body-secondary d-block">Current Address:</span>
                                    <span class="fw-semibold text-body">
                                        {{ implode(', ', array_filter([$employee->address_line1, $employee->address_line2, $employee->city, $employee->state, $employee->zip_code])) ?: 'N/A' }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-body-secondary d-block">Permanent Address:</span>
                                    <span class="fw-semibold text-body">
                                        {{ implode(', ', array_filter([$employee->address_line1, $employee->address_line2, $employee->city, $employee->state, $employee->zip_code])) ?: 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank & Statutory Details -->
                        <div>
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-bank2 me-2"></i>Bank & Statutory Details
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Bank Name:</span>
                                    <span class="fw-semibold text-body">{{ $employee->bank_name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Account Number:</span>
                                    <span class="fw-semibold text-body">{{ $employee->account_no ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">IFSC Code:</span>
                                    <span class="fw-semibold text-body">{{ $employee->ifsc_code ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Branch Name:</span>
                                    <span class="fw-semibold text-body">{{ $employee->bank_branch_name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">PAN Number:</span>
                                    <span class="fw-semibold text-body">{{ $employee->pan_no ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">Aadhaar Number:</span>
                                    <span class="fw-semibold text-body">{{ $employee->aadhar_no ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">UAN Number:</span>
                                    <span class="fw-semibold text-body">N/A</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-body-secondary d-block">ESIC Number:</span>
                                    <span class="fw-semibold text-body">N/A</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: ATTENDANCE HISTORY -->
                    <div class="tab-pane fade" id="tab-attendance" role="tabpanel" aria-labelledby="tab-attendance-btn">
                        @if($employee->monthlyAttendanceDetails->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">Attendance Not Available</h6>
                                <p class="small mb-0">No monthly attendance records found for this employee.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="attendance-history-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="fw-bold text-center">#</th>
                                            <th scope="col" class="fw-bold text-start">Month</th>
                                            <th scope="col" class="fw-bold text-center">Year</th>
                                            <th scope="col" class="fw-bold text-center">Total Days</th>
                                            <th scope="col" class="fw-bold text-center">Present</th>
                                            <th scope="col" class="fw-bold text-center">Leave</th>
                                            <th scope="col" class="fw-bold text-center">Leave Not Deducted</th>
                                            <th scope="col" class="fw-bold text-center">Payable Days</th>
                                            <th scope="col" class="fw-bold text-center">Status</th>
                                            <th scope="col" class="fw-bold text-center">View</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->monthlyAttendanceDetails as $index => $att)
                                            @php
                                                $monthMaster = $att->attendanceMonth;
                                                $monthName = $monthMaster ? \Carbon\Carbon::createFromDate($monthMaster->year, $monthMaster->month, 1)->format('F') : 'N/A';
                                                $yearNum = $monthMaster ? $monthMaster->year : 'N/A';
                                                $status = $monthMaster ? ucfirst($monthMaster->status) : 'Completed';
                                            @endphp
                                            <tr>
                                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                <td class="fw-semibold text-body">{{ $monthName }}</td>
                                                <td class="text-center">{{ $yearNum }}</td>
                                                <td class="text-center">{{ $att->total_days }}</td>
                                                <td class="text-center fw-bold text-success">{{ $att->net_present }}</td>
                                                <td class="text-center text-danger">{{ $att->leave_taken }}</td>
                                                <td class="text-center text-muted">{{ $att->leave_not_deducted }}</td>
                                                <td class="text-center fw-bold text-primary">{{ $att->payable_days }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-success px-2 py-1">{{ $status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($monthMaster)
                                                        <a href="{{ route('attendance.show', $monthMaster->id) }}" class="btn btn-xs btn-outline-info py-0 px-2" title="View Attendance Batch">
                                                            <i class="bi bi-eye me-1"></i>View
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 3: SALARY HISTORY -->
                    <div class="tab-pane fade" id="tab-salary" role="tabpanel" aria-labelledby="tab-salary-btn">
                        @if($employee->salaries->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-cash-coin fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">Salary Not Configured</h6>
                                <p class="small mb-3">No salary history records found for this employee.</p>
                                <a href="{{ route('employees.salaries.index', $employee->id) }}" class="btn btn-sm btn-primary fw-semibold">
                                    <i class="bi bi-plus-lg me-1"></i>Configure Salary
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="salary-history-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="fw-bold text-center">#</th>
                                            <th scope="col" class="fw-bold text-center">Effective From</th>
                                            <th scope="col" class="fw-bold text-center">Effective To</th>
                                            <th scope="col" class="fw-bold text-end">Basic Salary</th>
                                            <th scope="col" class="fw-bold text-end">Gross Salary</th>
                                            <th scope="col" class="fw-bold text-end">Total Deduction</th>
                                            <th scope="col" class="fw-bold text-end">Net Salary</th>
                                            <th scope="col" class="fw-bold text-center">Status</th>
                                            <th scope="col" class="fw-bold text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $todayStr = date('Y-m-d'); @endphp
                                        @foreach($employee->salaries as $index => $sal)
                                            @php
                                                $effFrom = $sal->effective_from;
                                                $effTo = $sal->effective_to;
                                                
                                                if (strtolower($sal->status) === 'active') {
                                                    if ($effTo && $effTo < $todayStr) {
                                                        $badgeLabel = 'Expired';
                                                        $badgeBg = 'bg-secondary';
                                                    } elseif ($effFrom > $todayStr) {
                                                        $badgeLabel = 'Future';
                                                        $badgeBg = 'bg-info text-dark';
                                                    } else {
                                                        $badgeLabel = 'Active';
                                                        $badgeBg = 'bg-success';
                                                    }
                                                } elseif ($effTo && $effTo < $todayStr) {
                                                    $badgeLabel = 'Expired';
                                                    $badgeBg = 'bg-secondary';
                                                } elseif ($effFrom > $todayStr) {
                                                    $badgeLabel = 'Future';
                                                    $badgeBg = 'bg-info text-dark';
                                                } else {
                                                    $badgeLabel = ucfirst($sal->status);
                                                    $badgeBg = 'bg-danger';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                <td class="text-center fw-semibold">{{ date('d M Y', strtotime($sal->effective_from)) }}</td>
                                                <td class="text-center">{{ $sal->effective_to ? date('d M Y', strtotime($sal->effective_to)) : 'Present' }}</td>
                                                <td class="text-end">₹ {{ number_format($sal->basic_salary, 2) }}</td>
                                                <td class="text-end fw-bold text-body">₹ {{ number_format($sal->gross_salary, 2) }}</td>
                                                <td class="text-end text-danger">₹ {{ number_format($sal->total_deduction, 2) }}</td>
                                                <td class="text-end fw-bold text-success">₹ {{ number_format($sal->net_salary, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $badgeBg }} px-2 py-1">{{ $badgeLabel }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('employees.salaries.index', $employee->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2" title="Manage Salaries">
                                                        <i class="bi bi-pencil me-1"></i>Manage
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 4: PAYROLL HISTORY -->
                    <div class="tab-pane fade" id="tab-payroll" role="tabpanel" aria-labelledby="tab-payroll-btn">
                        @if($employee->payrollDetails->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-spreadsheet fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">Payroll Not Generated</h6>
                                <p class="small mb-3">No payroll history records found for this employee.</p>
                                <a href="{{ route('payrolls.create') }}" class="btn btn-sm btn-primary fw-semibold">
                                    <i class="bi bi-gear-wide-connected me-1"></i>Generate Payroll
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="payroll-history-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="fw-bold text-center">#</th>
                                            <th scope="col" class="fw-bold text-start">Payroll Month</th>
                                            <th scope="col" class="fw-bold text-end">Gross Salary</th>
                                            <th scope="col" class="fw-bold text-end">Earned Salary</th>
                                            <th scope="col" class="fw-bold text-end">Deduction</th>
                                            <th scope="col" class="fw-bold text-end">Net Salary</th>
                                            <th scope="col" class="fw-bold text-center">Status</th>
                                            <th scope="col" class="fw-bold text-center">Generated Date</th>
                                            <th scope="col" class="fw-bold text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->payrollDetails as $index => $payDetail)
                                            @php
                                                $payrollMaster = $payDetail->payroll;
                                                $monthName = $payrollMaster ? \Carbon\Carbon::createFromDate($payrollMaster->year, $payrollMaster->month, 1)->format('F Y') : 'N/A';
                                                $status = $payrollMaster ? ucfirst($payrollMaster->status) : 'Generated';
                                                $badgeClass = match (strtolower($status)) {
                                                    'generated' => 'bg-success',
                                                    'locked' => 'bg-warning text-dark',
                                                    'paid' => 'bg-info text-dark',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <tr>
                                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                <td class="fw-semibold text-body">{{ $monthName }}</td>
                                                <td class="text-end">₹ {{ number_format($payDetail->gross_salary, 2) }}</td>
                                                <td class="text-end text-primary">₹ {{ number_format($payDetail->earned_salary, 2) }}</td>
                                                <td class="text-end text-danger">₹ {{ number_format($payDetail->total_deduction, 2) }}</td>
                                                <td class="text-end fw-bold text-success">₹ {{ number_format($payDetail->net_salary, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $badgeClass }} px-2 py-1">{{ $status }}</span>
                                                </td>
                                                <td class="text-center text-muted">
                                                    {{ $payDetail->created_at ? $payDetail->created_at->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('payrolls.salary-slip', $payDetail->id) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-1" title="View Salary Slip">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('payrolls.salary-slip.pdf', $payDetail->id) }}" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-1" title="Download PDF Slip">
                                                            <i class="bi bi-file-earmark-pdf"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 5: DOCUMENTS -->
                    <div class="tab-pane fade" id="tab-documents" role="tabpanel" aria-labelledby="tab-documents-btn">
                        @if(empty($documents))
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">No Documents Uploaded</h6>
                                <p class="small mb-0">There are no uploaded documents available for this employee profile.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($documents as $doc)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border border-light-subtle shadow-sm h-100">
                                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded">
                                                            <i class="bi bi-file-earmark-text fs-4"></i>
                                                        </div>
                                                        <div class="overflow-hidden">
                                                            <h6 class="fw-bold text-body mb-0 text-truncate" title="{{ $doc['label'] }}">{{ $doc['label'] }}</h6>
                                                            <span class="text-muted small text-truncate d-block">{{ $doc['file_name'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-body-secondary border-top pt-2 mt-2">
                                                        <span>Uploaded: {{ $doc['uploaded_date'] }}</span>
                                                        <span class="badge bg-light text-dark border">{{ $doc['file_size'] }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 mt-3">
                                                    <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary w-50 fw-semibold">
                                                        <i class="bi bi-eye me-1"></i>Preview
                                                    </a>
                                                    <a href="{{ $doc['url'] }}" download="{{ $doc['file_name'] }}" class="btn btn-sm btn-primary w-50 fw-semibold">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- TAB 6: TIMELINE -->
                    <div class="tab-pane fade" id="tab-timeline" role="tabpanel" aria-labelledby="tab-timeline-btn">
                        @if(empty($timelineEvents))
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clock fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">No Timeline Events Recorded</h6>
                            </div>
                        @else
                            <div class="position-relative ps-4 ms-2 border-start border-2 border-primary border-opacity-25 py-2">
                                @foreach($timelineEvents as $event)
                                    <div class="mb-4 position-relative">
                                        <!-- Timeline Bullet Indicator -->
                                        <span class="position-absolute top-0 start-0 translate-middle p-1 rounded-circle {{ $event['badge'] ?? 'bg-primary' }} border border-white" style="left: -17px; width: 14px; height: 14px;"></span>
                                        
                                        <div class="bg-body-tertiary p-3 rounded border border-light-subtle">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold text-body mb-0">{{ $event['title'] }}</h6>
                                                <span class="badge bg-light text-secondary border small">
                                                    {{ $event['date'] }} at {{ $event['time'] }}
                                                </span>
                                            </div>
                                            <p class="text-body-secondary small mb-2">{{ $event['description'] }}</p>
                                            <div class="text-muted small border-top pt-2">
                                                <i class="bi bi-person me-1"></i>Performed By: <strong class="text-body">{{ $event['performed_by'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Dynamic click handler for nav tabs
        $('#employeeProfileTabs').on('click', 'button.nav-link', function(e) {
            e.preventDefault();
            
            // Deactivate all tab links in tablist
            $('#employeeProfileTabs button.nav-link').removeClass('active').attr('aria-selected', 'false');
            
            // Activate clicked tab link
            $(this).addClass('active').attr('aria-selected', 'true');
            
            // Hide all tab content panes
            $('#employeeProfileTabsContent .tab-pane').removeClass('show active');
            
            // Show target tab pane
            var targetSelector = $(this).attr('data-bs-target') || $(this).attr('data-coreui-target');
            if (targetSelector) {
                $(targetSelector).addClass('show active');
                
                // Recalculate DataTables inside the shown tab pane
                if ($.fn.DataTable) {
                    $(targetSelector).find('table').each(function() {
                        if ($.fn.DataTable.isDataTable(this)) {
                            $(this).DataTable().columns.adjust().draw();
                        }
                    });
                }
            }
        });

        // Initialize DataTables for history tables if present
        if ($('#attendance-history-table').length) {
            $('#attendance-history-table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[1, 'desc']],
                language: { search: "Search Attendance:" }
            });
        }

        if ($('#salary-history-table').length) {
            $('#salary-history-table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[1, 'desc']],
                language: { search: "Search Salary:" }
            });
        }

        if ($('#payroll-history-table').length) {
            $('#payroll-history-table').DataTable({
                pageLength: 10,
                ordering: true,
                order: [[1, 'desc']],
                language: { search: "Search Payroll:" }
            });
        }

        // Quick action tab switchers
        $('#quick-act-attendance').on('click', function() {
            $('#tab-attendance-btn').trigger('click');
            $('html, body').animate({ scrollTop: $('#employeeProfileTabs').offset().top - 100 }, 300);
        });

        $('#quick-act-payroll').on('click', function() {
            $('#tab-payroll-btn').trigger('click');
            $('html, body').animate({ scrollTop: $('#employeeProfileTabs').offset().top - 100 }, 300);
        });
    });
</script>
@endpush
