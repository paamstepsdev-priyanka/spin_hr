@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Top Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Dashboard</h3>
                    <div class="text-secondary fs-6">Welcome back, {{ $user->name ?? 'Admin' }}</div>
                </div>
                <div class="text-end mt-2 mt-md-0">
                    <div class="badge bg-primary fs-6 px-3 py-2 mb-1">
                        <i class="bi bi-building me-1"></i>
                        {{ $currentCompany ? $currentCompany->name : 'All Companies' }}
                    </div>
                    <div class="small text-muted fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i>{{ $now->format('l, d F Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- First Row – 6 KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Employees -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <a href="{{ route('employees.index') }}" class="card text-decoration-none shadow-sm border-0 rounded h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted fw-bold small">Employees</span>
                    <i class="bi bi-people fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['totalActiveEmployees']) }}</div>
                    <div class="small text-muted">Active Employees</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Departments -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <a href="{{ route('departments.index') }}" class="card text-decoration-none shadow-sm border-0 rounded h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted fw-bold small">Departments</span>
                    <i class="bi bi-building fs-4 text-info"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['totalDepartments']) }}</div>
                    <div class="small text-muted">Total Departments</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 3: Branches -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <a href="{{ $currentCompany ? route('company.branches.index', $currentCompany->id) : route('companies.index') }}" class="card text-decoration-none shadow-sm border-0 rounded h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted fw-bold small">Branches</span>
                    <i class="bi bi-geo-alt fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['totalBranches']) }}</div>
                    <div class="small text-muted">Total Branches</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 4: Attendance Status -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <a href="{{ route('attendance.index') }}" class="card text-decoration-none shadow-sm border-0 rounded h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted fw-bold small">Attendance</span>
                    <i class="bi bi-calendar-check fs-4 text-success"></i>
                </div>
                <div>
                    @if($stats['isAllCompanies'])
                        <div class="fs-4 fw-bold text-dark">{{ $stats['attendanceCardStat'] }}</div>
                        <div class="small">
                            <span class="badge {{ $stats['attendanceBadgeClass'] }} px-2 py-1">{{ $stats['attendanceBadgeText'] }}</span>
                        </div>
                    @else
                        <div class="mb-1">
                            <span class="badge {{ $stats['attendanceBadgeClass'] }} px-2 py-1 fs-6">
                                {{ $stats['attendanceBadgeText'] }}
                            </span>
                        </div>
                        <div class="small text-muted">Current Month</div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <!-- Card 5: Payroll Status -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <a href="{{ route('payrolls.index') }}" class="card text-decoration-none shadow-sm border-0 rounded h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted fw-bold small">Payroll</span>
                    <i class="bi bi-cash-stack fs-4 text-primary"></i>
                </div>
                <div>
                    @if($stats['isAllCompanies'])
                        <div class="fs-4 fw-bold text-dark">{{ $stats['payrollCardStat'] }}</div>
                        <div class="small">
                            <span class="badge {{ $stats['payrollBadgeClass'] }} px-2 py-1">{{ $stats['payrollBadgeText'] }}</span>
                        </div>
                    @else
                        <div class="mb-1">
                            <span class="badge {{ $stats['payrollBadgeClass'] }} px-2 py-1 fs-6">
                                {{ $stats['payrollBadgeText'] }}
                            </span>
                        </div>
                        <div class="small text-muted">Current Month</div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <!-- Card 6: Monthly Salary -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <a href="{{ route('payrolls.index') }}" class="card text-decoration-none shadow-sm border-0 rounded h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted fw-bold small">Monthly Salary</span>
                    <i class="bi bi-currency-rupee fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-5 fw-bold text-success text-truncate" title="₹ {{ number_format($stats['monthlySalary'], 2) }}">
                        ₹ {{ number_format($stats['monthlySalary'], 2) }}
                    </div>
                    <div class="small text-muted">Total Net Salary</div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Second Row -->
<div class="row g-3 mb-4">
    <!-- Left side (8 columns): Employee Distribution -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 rounded h-100">
            <div class="card-header bg-body-tertiary border-0 py-3 fw-bold text-dark">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Employee Distribution
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Department</th>
                                <th class="text-end">Employees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['departmentDistribution'] as $dept)
                                <tr>
                                    <td class="fw-semibold">{{ $dept['name'] }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary rounded-pill px-3 py-1">{{ $dept['count'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No department records available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right side (4 columns): Quick Summary -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 rounded h-100">
            <div class="card-header bg-body-tertiary border-0 py-3 fw-bold text-dark">
                <i class="bi bi-list-check me-2 text-primary"></i>Quick Summary
            </div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <span class="text-muted">Current Month</span>
                        <span class="fw-bold text-dark">{{ $now->format('F Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <span class="text-muted">Total Days</span>
                        <span class="fw-bold text-dark">{{ $now->daysInMonth }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <span class="text-muted">{{ $stats['isAllCompanies'] ? 'Attendance Progress' : 'Attendance' }}</span>
                        @if($stats['isAllCompanies'])
                            <span class="badge {{ $stats['attendanceBadgeClass'] }}">
                                {{ $stats['attendanceCompletedCompanies'] }} / {{ $stats['totalCompanies'] }} Companies
                            </span>
                        @else
                            <span class="badge {{ $stats['attendanceBadgeClass'] }}">
                                {{ $stats['attendanceStatusText'] }}
                            </span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <span class="text-muted">{{ $stats['isAllCompanies'] ? 'Payroll Progress' : 'Payroll' }}</span>
                        @if($stats['isAllCompanies'])
                            <span class="badge {{ $stats['payrollBadgeClass'] }}">
                                {{ $stats['payrollCompletedCompanies'] }} / {{ $stats['totalCompanies'] }} Companies
                            </span>
                        @else
                            <span class="badge {{ $stats['payrollBadgeClass'] }}">
                                {{ $stats['payrollStatusText'] }}
                            </span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <span class="text-muted">Employees Missing Salary</span>
                        <span class="fw-bold text-danger fs-6">{{ $stats['employeesMissingSalary'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted">Employees Missing Attendance</span>
                        <span class="fw-bold text-danger fs-6">{{ $stats['employeesMissingAttendance'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Third Row: Recent Payroll -->
<div class="card shadow-sm border-0 rounded mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>Recent Payroll
        </h5>
        <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Company</th>
                        <th>Branch</th>
                        <th>Month</th>
                        <th>Employees</th>
                        <th>Total Net Salary</th>
                        <th>Status</th>
                        <th>Generated On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recentPayrolls'] as $payroll)
                        <tr>
                            <td class="fw-semibold text-primary">
                                {{ $payroll->company ? $payroll->company->name : 'N/A' }}
                            </td>
                            <td>{{ $payroll->branch ? $payroll->branch->name : 'N/A' }}</td>
                            <td>
                                {{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }}
                            </td>
                            <td><span class="badge bg-secondary px-2 py-1">{{ $payroll->details_count }}</span></td>
                            <td><strong class="text-success">₹ {{ number_format($payroll->total_net_salary, 2) }}</strong></td>
                            <td>
                                @switch($payroll->status)
                                    @case('Paid')
                                        <span class="badge bg-info text-dark px-2 py-1">Paid</span>
                                        @break
                                    @case('Generated')
                                        <span class="badge bg-success px-2 py-1">Generated</span>
                                        @break
                                    @case('Locked')
                                        <span class="badge bg-warning text-dark px-2 py-1">Locked</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary px-2 py-1">{{ $payroll->status }}</span>
                                @endswitch
                            </td>
                            <td class="text-muted small">
                                {{ $payroll->created_at ? $payroll->created_at->format('d/m/Y h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No recent payroll batches found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Fourth Row: Recent Attendance -->
<div class="card shadow-sm border-0 rounded mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i>Recent Attendance
        </h5>
        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Company</th>
                        <th>Branch</th>
                        <th>Month</th>
                        <th>Employees</th>
                        <th>Status</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recentAttendances'] as $att)
                        <tr>
                            <td class="fw-semibold text-primary">
                                {{ $att->company ? $att->company->name : 'N/A' }}
                            </td>
                            <td>{{ $att->branch ? $att->branch->name : 'N/A' }}</td>
                            <td>
                                {{ \Carbon\Carbon::createFromDate($att->year, $att->month, 1)->format('F Y') }}
                            </td>
                            <td><span class="badge bg-secondary px-2 py-1">{{ $att->details_count }}</span></td>
                            <td><span class="badge bg-success px-2 py-1">Completed</span></td>
                            <td class="text-muted small">
                                {{ $att->created_at ? $att->created_at->format('d/m/Y h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No recent attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Fifth Row: Quick Actions -->
<div class="card shadow-sm border-0 rounded mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 fw-bold text-dark">
        <i class="bi bi-lightning-charge me-2 text-primary"></i>Quick Actions
    </div>
    <div class="card-body p-4">
        <!-- Row 1 -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <a href="{{ route('attendance.create') }}" class="btn btn-outline-primary btn-lg w-100 py-3 d-flex align-items-center justify-content-center fw-semibold">
                    <i class="bi bi-calendar-plus me-2 fs-4"></i>Generate Monthly Attendance
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="{{ route('payrolls.create') }}" class="btn btn-outline-success btn-lg w-100 py-3 d-flex align-items-center justify-content-center fw-semibold">
                    <i class="bi bi-cash-coin me-2 fs-4"></i>Generate Payroll
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="{{ route('attendance-report.index') }}" class="btn btn-outline-info btn-lg w-100 py-3 d-flex align-items-center justify-content-center fw-semibold">
                    <i class="bi bi-file-earmark-text me-2 fs-4"></i>Attendance Report
                </a>
            </div>
        </div>
        <!-- Row 2 -->
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary btn-lg w-100 py-3 d-flex align-items-center justify-content-center fw-semibold">
                    <i class="bi bi-journal-text me-2 fs-4"></i>Payroll Report
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-warning btn-lg w-100 py-3 d-flex align-items-center justify-content-center fw-semibold">
                    <i class="bi bi-people me-2 fs-4"></i>Employees
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-dark btn-lg w-100 py-3 d-flex align-items-center justify-content-center fw-semibold">
                    <i class="bi bi-gear me-2 fs-4"></i>Salary Management
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Sixth Row: Notifications -->
<div class="card shadow-sm border-0 rounded mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 fw-bold text-dark">
        <i class="bi bi-bell me-2 text-primary"></i>Notifications
    </div>
    <div class="card-body p-4">
        @forelse($stats['notifications'] as $notif)
            <div class="alert alert-{{ $notif['type'] }} d-flex align-items-center mb-2 rounded border-0 shadow-sm" role="alert">
                <i class="bi {{ $notif['icon'] }} me-3 fs-4"></i>
                <div class="fw-medium">{{ $notif['message'] }}</div>
            </div>
        @empty
            <div class="alert alert-success d-flex align-items-center mb-0 rounded border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                <div class="fw-medium">No pending notifications.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection


