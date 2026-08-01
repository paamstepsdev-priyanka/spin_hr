@extends('layouts.admin')

@section('title', 'View Monthly Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body"> Attendance Summary </h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('attendance.edit', $attendanceMonth->id) }}" class="btn btn-primary btn-sm fw-semibold">
                        <i class="bi bi-pencil me-1"></i> Edit Attendance
                    </a>
                    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Attendance
                    </a>
                </div>
            </div>
        </div>

        <!-- Master Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Attendance Information</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Company</label>
                        <div class="fw-bold text-body fs-6">{{ $attendanceMonth->company->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Branch</label>
                        <div class="fw-bold text-body fs-6">{{ $attendanceMonth->branch->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Month & Year</label>
                        <div class="fw-bold text-body fs-6">{{ $monthName }} {{ $attendanceMonth->year }}</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Created By</label>
                        <div class="fw-bold text-body fs-6">{{ $attendanceMonth->creator->name ?? 'System' }}</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                        <div><span class="badge bg-success px-2 py-1">{{ ucfirst($attendanceMonth->status) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Summary Read-Only Table Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-body">Employee Attendance Summary</h5>
                <span class="badge bg-secondary px-2 py-1">Total Employees: {{ $attendanceMonth->details->count() }}</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap">
                        <thead class="table-light align-middle text-center fw-bold">
                            <tr>
                                <th scope="col" style="width: 40px;">#</th>
                                <th scope="col" class="text-start">Employee Name</th>
                                <th scope="col" class="text-center" style="width: 110px;">Salary</th>
                                <th scope="col" class="text-start">Branch</th>
                                <th scope="col" class="text-center" style="width: 150px;">No. of Days in Month</th>
                                <th scope="col" class="text-center text-danger" style="width: 130px;">Leave Taken</th>
                                <th scope="col" class="text-center table-info fw-bold" style="width: 130px;">Net Present</th>
                                <th scope="col" class="text-center text-primary" style="width: 150px;">Leave Not Deducted</th>
                                <th scope="col" class="text-center table-success fw-bold text-success" style="width: 160px;">No. of Days Payable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceMonth->details as $index => $detail)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-semibold text-body text-start">{{ $detail->employee->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if(!empty($detail->employee->salary_exists))
                                            <button type="button" class="btn btn-success btn-sm py-0 px-2 small text-nowrap" disabled style="font-size: 0.75rem;">
                                                Salary Set
                                            </button>
                                        @else
                                            <a href="{{ route('employees.salaries.index', $detail->employee_id) }}" 
                                               class="btn btn-danger btn-sm py-0 px-2 small text-nowrap" 
                                               target="_blank" 
                                               title="Salary is not configured for this employee."
                                               data-bs-toggle="tooltip">
                                                Set Salary
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-muted text-start">{{ $detail->employee->branch->name ?? 'N/A' }}</td>
                                    <td class="text-center fw-bold bg-light">{{ $detail->total_days }}</td>
                                    <td class="text-center text-danger fw-semibold">{{ $detail->leave_taken !== null ? ((float)$detail->leave_taken == (int)$detail->leave_taken ? (int)$detail->leave_taken : (float)$detail->leave_taken) : '-' }}</td>
                                    <td class="text-center text-primary">{{ $detail->leave_not_deducted !== null ? ((float)$detail->leave_not_deducted == (int)$detail->leave_not_deducted ? (int)$detail->leave_not_deducted : (float)$detail->leave_not_deducted) : '-' }}</td>
                                    <td class="text-center table-success fw-bold text-success">{{ (float)$detail->payable_days == (int)$detail->payable_days ? (int)$detail->payable_days : (float)$detail->payable_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">No employee details available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
