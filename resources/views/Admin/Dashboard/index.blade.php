@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="fw-bold mb-1">Welcome to SpinHR</h4>
                    <p class="text-muted mb-0">Overview & Key HRMS Statistics</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="bi bi-building me-1"></i>
                        {{ $currentCompany ? $currentCompany->name : 'All Companies' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Employees Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['totalEmployees']) }}</div>
                    <div class="text-white-50 small">Total Employees ({{ $stats['activeEmployees'] }} Active)</div>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 p-2 text-end">
                <a href="{{ route('employees.index') }}" class="text-white text-decoration-none small">View Employees <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Branches Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-info shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['totalBranches']) }}</div>
                    <div class="text-white-50 small">Active Branches</div>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-diagram-3"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 p-2 text-end">
                <a href="{{ route('companies.index') }}" class="text-white text-decoration-none small">Manage Companies <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Departments Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-warning shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['totalDepartments']) }}</div>
                    <div class="text-white-50 small">Departments</div>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-briefcase"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 p-2 text-end">
                <a href="{{ route('departments.index') }}" class="text-white text-decoration-none small">View Departments <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Attendance Batches Card -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-success shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['attendanceBatches']) }}</div>
                    <div class="text-white-50 small">Attendance Batches</div>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 p-2 text-end">
                <a href="{{ route('attendance.index') }}" class="text-white text-decoration-none small">Manage Attendance <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Payroll Batches Card -->
    <div class="col-sm-6 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Payroll Batches Processed</div>
                    <div class="fs-3 fw-bold text-dark">{{ number_format($stats['payrollBatches']) }}</div>
                </div>
                <div class="fs-1 text-primary">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent p-2 text-end">
                <a href="{{ route('payrolls.index') }}" class="text-primary text-decoration-none small">View Payrolls <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Net Salary Card -->
    <div class="col-sm-6 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Net Salary Paid</div>
                    <div class="fs-3 fw-bold text-success">₹ {{ number_format($stats['totalNetSalary'], 2) }}</div>
                </div>
                <div class="fs-1 text-success">
                    <i class="bi bi-currency-rupee"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent p-2 text-end">
                <a href="{{ route('payrolls.index') }}" class="text-success text-decoration-none small">Payroll Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
