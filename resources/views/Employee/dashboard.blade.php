@extends('layouts.employee')

@section('title', 'Employee Dashboard')

@section('content')
<!-- Top Welcome Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 bg-body-tertiary rounded">
        <div class="row align-items-center">
            <div class="col-auto text-center mb-3 mb-md-0">
                @if(!empty($employee->photo))
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="Photo" class="rounded-circle border border-3 border-white shadow-sm" style="width: 85px; height: 85px; object-fit: cover;">
                @else
                    <div class="avatar avatar-xl bg-primary text-white rounded-circle fw-bold d-inline-flex align-items-center justify-content-center shadow-sm fs-2 px-3 py-2" style="width: 85px; height: 85px;">
                        {{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-body">Welcome back, {{ $employee->name }}!</h4>
                        <p class="text-muted mb-2 small">
                            <span class="fw-semibold text-primary me-2"><i class="bi bi-person-badge me-1"></i>{{ $employee->employee_code }}</span> | 
                            <span class="ms-2"><i class="bi bi-building me-1"></i>{{ $employee->company->name ?? 'Company' }}</span>
                        </p>
                    </div>
                    <div class="text-md-end">
                        <span class="badge bg-success px-3 py-2 fs-7 text-uppercase"><i class="bi bi-check-circle me-1"></i>{{ ucfirst($employee->status) }}</span>
                        <div class="small text-muted mt-1"><i class="bi bi-calendar-event me-1"></i>Today: <strong>{{ $currentDate }}</strong></div>
                    </div>
                </div>
                <div class="row g-2 mt-2 pt-2 border-top small text-muted">
                    <div class="col-6 col-md-3">
                        <strong class="text-body d-block">Department:</strong>
                        <span>{{ $employee->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <strong class="text-body d-block">Designation:</strong>
                        <span>{{ $employee->designation ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <strong class="text-body d-block">Branch:</strong>
                        <span>{{ $employee->branch->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <strong class="text-body d-block">Joining Date:</strong>
                        <span>{{ $employee->joining_date ? date('d M Y', strtotime($employee->joining_date)) : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Present Days</div>
                <div class="fs-4 fw-bold text-primary">{{ $currentAttendance ? $currentAttendance->net_present : '0' }}</div>
                <div class="small text-muted mt-1">{{ $currentMonthName }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Leave Taken</div>
                <div class="fs-4 fw-bold text-warning">{{ $currentAttendance ? $currentAttendance->leave_taken : '0' }}</div>
                <div class="small text-muted mt-1">{{ $currentMonthName }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Payable Days</div>
                <div class="fs-4 fw-bold text-info">{{ $currentAttendance ? $currentAttendance->payable_days : '0' }}</div>
                <div class="small text-muted mt-1">{{ $currentMonthName }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Gross Salary</div>
                <div class="fs-5 fw-bold text-success">₹ {{ $currentSalary ? number_format($currentSalary->gross_salary, 2) : '0.00' }}</div>
                <div class="small text-muted mt-1">Current Active</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-secondary">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Net Salary</div>
                <div class="fs-5 fw-bold text-dark">₹ {{ $currentSalary ? number_format($currentSalary->net_salary, 2) : '0.00' }}</div>
                <div class="small text-muted mt-1">Current Active</div>
            </div>
        </div>
    </div>
    <!-- Enhancement 2: Current Attendance Month KPI Card -->
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100 border-start border-4 {{ $currentAttendance ? 'border-success' : 'border-danger' }}">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Attendance Month</div>
                <div class="fs-6 fw-bold text-body text-truncate">{{ $currentMonthName }}</div>
                <div class="mt-1">
                    @if($currentAttendance)
                        <span class="badge bg-success px-2 py-1 small">Completed</span>
                    @else
                        <span class="badge bg-danger px-2 py-1 small">Not Generated</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Current Attendance Summary -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-calendar-check text-primary me-2"></i>Current Attendance Summary</h5>
                <a href="{{ route('employee.attendance') }}" class="btn btn-sm btn-outline-primary fw-semibold">View All</a>
            </div>
            <div class="card-body">
                @if($currentAttendance)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th class="table-light" style="width: 45%;">Month & Year</th>
                                    <td class="fw-bold">{{ $currentMonthName }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Total Days</th>
                                    <td>{{ $currentAttendance->total_days }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Present Days</th>
                                    <td class="fw-bold text-success">{{ $currentAttendance->net_present }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Leave Taken</th>
                                    <td class="text-warning fw-bold">{{ $currentAttendance->leave_taken }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Leave Not Deducted</th>
                                    <td>{{ $currentAttendance->leave_not_deducted }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light text-primary">Payable Days</th>
                                    <td class="fw-bold text-primary fs-6">{{ $currentAttendance->payable_days }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Attendance Status</th>
                                    <td><span class="badge bg-success px-2 py-1">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted display-4"></i>
                        <h6 class="fw-bold mt-2 text-secondary">Attendance Not Generated</h6>
                        <p class="text-muted small mb-0">Attendance for {{ $currentMonthName }} has not been published yet by HR.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Current Salary Summary -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-currency-rupee text-success me-2"></i>Current Salary Summary</h5>
                <a href="{{ route('employee.salary-history') }}" class="btn btn-sm btn-outline-success fw-semibold">Salary History</a>
            </div>
            <div class="card-body">
                @if($currentSalary)
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-sm-4">
                            <div class="border rounded p-2 text-center bg-body-tertiary">
                                <span class="text-muted small d-block">Basic Salary</span>
                                <strong class="text-dark">₹ {{ number_format($currentSalary->basic_salary, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="border rounded p-2 text-center bg-body-tertiary">
                                <span class="text-muted small d-block">HRA</span>
                                <strong class="text-dark">₹ {{ number_format($currentSalary->hra, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="border rounded p-2 text-center bg-body-tertiary">
                                <span class="text-muted small d-block">Medical</span>
                                <strong class="text-dark">₹ {{ number_format($currentSalary->medical_allowance, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="border rounded p-2 text-center bg-body-tertiary">
                                <span class="text-muted small d-block">Conveyance</span>
                                <strong class="text-dark">₹ {{ number_format($currentSalary->conveyance_allowance, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="border rounded p-2 text-center bg-body-tertiary">
                                <span class="text-muted small d-block">Special</span>
                                <strong class="text-dark">₹ {{ number_format($currentSalary->special_allowance, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="border rounded p-2 text-center bg-body-tertiary">
                                <span class="text-muted small d-block">Other</span>
                                <strong class="text-dark">₹ {{ number_format($currentSalary->other_allowance, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                                <tr>
                                    <th class="table-light">Gross Monthly Salary</th>
                                    <td class="fw-bold text-success">₹ {{ number_format($currentSalary->gross_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Total Deduction</th>
                                    <td class="fw-bold text-danger">₹ {{ number_format($currentSalary->total_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Net Monthly Salary</th>
                                    <td class="fw-bold text-dark fs-6">₹ {{ number_format($currentSalary->net_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Effective Range</th>
                                    <td class="small">
                                        {{ date('d/m/Y', strtotime($currentSalary->effective_from)) }}
                                        to
                                        {{ $currentSalary->effective_to ? date('d/m/Y', strtotime($currentSalary->effective_to)) : 'Present' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-wallet2 text-muted display-4"></i>
                        <h6 class="fw-bold mt-2 text-secondary">Salary Not Configured</h6>
                        <p class="text-muted small mb-0">Your salary structure has not been configured yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Latest Payroll Summary -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-clock-history text-info me-2"></i>Latest Payroll Summary</h5>
                <a href="{{ route('employee.payroll-history') }}" class="btn btn-sm btn-outline-info fw-semibold">Payroll History</a>
            </div>
            <div class="card-body">
                @if($latestPayroll)
                    @php
                        $payrollMonthName = \Carbon\Carbon::createFromDate($latestPayroll->payroll->year, $latestPayroll->payroll->month, 1)->format('F Y');
                    @endphp
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th class="table-light" style="width: 40%;">Payroll Month</th>
                                    <td class="fw-bold text-primary">{{ $payrollMonthName }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Gross Salary</th>
                                    <td>₹ {{ number_format($latestPayroll->gross_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Earned Salary</th>
                                    <td class="fw-bold text-success">₹ {{ number_format($latestPayroll->earned_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Total Deduction</th>
                                    <td class="text-danger">₹ {{ number_format($latestPayroll->total_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Net Salary Payable</th>
                                    <td class="fw-bold text-success fs-6">₹ {{ number_format($latestPayroll->net_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Payment Status</th>
                                    <td>
                                        <span class="badge bg-success px-2 py-1">{{ $latestPayroll->payroll->status ?? 'Generated' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light">Generated Date</th>
                                    <td class="small">{{ $latestPayroll->created_at ? $latestPayroll->created_at->format('d/m/Y h:i A') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('employee.payslips.show', $latestPayroll->id) }}" class="btn btn-primary btn-sm fw-semibold me-1">
                            <i class="bi bi-file-earmark-text me-1"></i> View Payslip
                        </a>
                        <a href="{{ route('employee.payslips.pdf', $latestPayroll->id) }}" class="btn btn-danger btn-sm fw-semibold" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF Download
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x text-muted display-4"></i>
                        <h6 class="fw-bold mt-2 text-secondary">Payroll Not Processed Yet</h6>
                        <p class="text-muted small mb-0">No generated payroll records found for your account.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="card-title fw-bold m-0 text-body"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('employee.profile') }}" class="btn btn-outline-primary text-start fw-semibold py-2">
                        <i class="bi bi-person-badge me-2"></i> View My Profile
                    </a>
                    <a href="{{ route('employee.attendance') }}" class="btn btn-outline-secondary text-start fw-semibold py-2">
                        <i class="bi bi-calendar-check me-2"></i> View Attendance History
                    </a>
                    <a href="{{ route('employee.salary-history') }}" class="btn btn-outline-success text-start fw-semibold py-2">
                        <i class="bi bi-currency-rupee me-2"></i> View Salary History
                    </a>
                    <a href="{{ route('employee.payroll-history') }}" class="btn btn-outline-info text-start fw-semibold py-2">
                        <i class="bi bi-clock-history me-2"></i> View Payroll History
                    </a>
                    @if($latestPayroll)
                        <a href="{{ route('employee.payslips.pdf', $latestPayroll->id) }}" class="btn btn-outline-danger text-start fw-semibold py-2" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Download Latest Payslip
                        </a>
                    @endif
                    <a href="{{ route('employee.profile.pdf') }}" class="btn btn-outline-dark text-start fw-semibold py-2" target="_blank">
                        <i class="bi bi-download me-2"></i> Download Profile PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
