@extends('layouts.admin')

@section('title', 'Edit Payroll Batch')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-body">Edit Payroll — {{ $monthName }} {{ $payroll->year }}</h4>
                    <small class="text-muted">Company: <strong>{{ $payroll->company->name ?? 'N/A' }}</strong> | Branch: <strong>{{ $payroll->branch->name ?? 'N/A' }}</strong></small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('payroll-processing.show', [$payroll->year, $payroll->month]) }}" class="btn btn-outline-secondary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left me-1"></i> Back to Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Warning Alert -->
        <div class="alert alert-warning d-flex align-items-center shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 flex-shrink-0"></i>
            <div>
                <strong>Important Notice:</strong> Updating this payroll batch will recalculate all salary figures based on the latest employee salary configurations and attendance figures. <strong>Existing salary slips will be marked as invalid/pending and will need to be regenerated.</strong>
            </div>
        </div>

        <!-- Summary Cards Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Month / Year</div>
                    <div class="fw-bold text-primary fs-5 mt-1">{{ $monthName }} {{ $payroll->year }}</div>
                    <div class="small text-secondary">{{ $payroll->details->count() }} Employees</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Current Gross Salary</div>
                    <div class="fw-bold text-dark fs-5 mt-1">₹ {{ number_format($payroll->total_gross_salary, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Current Deductions</div>
                    <div class="fw-bold text-danger fs-5 mt-1">₹ {{ number_format($payroll->total_deduction, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100 bg-success bg-opacity-10 border-success border-opacity-25">
                    <div class="text-success small fw-bold">Current Net Salary</div>
                    <div class="fw-bold text-success fs-4 mt-1">₹ {{ number_format($payroll->total_net_salary, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Payroll Details Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-body">Employee Payroll Records</h5>
                <button type="button" id="btnUpdatePayroll" class="btn btn-primary btn-sm fw-semibold">
                    <i class="bi bi-arrow-repeat me-1"></i> Recalculate & Update Payroll
                </button>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 text-nowrap">
                        <thead class="table-light text-center">
                            <tr>
                                <th scope="col" style="width: 30px;">#</th>
                                <th scope="col" class="text-start">Emp Code</th>
                                <th scope="col" class="text-start">Employee Name</th>
                                <th scope="col" class="text-start">Department</th>
                                <th scope="col" class="text-end">Basic Salary</th>
                                <th scope="col" class="text-end">Gross Salary</th>
                                <th scope="col">Payable Days</th>
                                <th scope="col" class="text-end">Per Day Salary</th>
                                <th scope="col" class="text-end">Earned Salary</th>
                                <th scope="col" class="text-end">Total Deduction</th>
                                <th scope="col" class="text-end">Net Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payroll->details as $index => $detail)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold">{{ $detail->employee->employee_code ?? 'N/A' }}</td>
                                    <td class="text-start fw-bold text-body">{{ $detail->employee->name ?? 'N/A' }}</td>
                                    <td class="text-start">{{ $detail->employee->department->name ?? 'N/A' }}</td>
                                    <td class="text-end">₹ {{ number_format($detail->basic_salary, 2) }}</td>
                                    <td class="text-end fw-semibold">₹ {{ number_format($detail->gross_salary, 2) }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $detail->payable_days }}</td>
                                    <td class="text-end">₹ {{ number_format($detail->per_day_salary, 2) }}</td>
                                    <td class="text-end fw-semibold">₹ {{ number_format($detail->earned_salary, 2) }}</td>
                                    <td class="text-end text-danger">₹ {{ number_format($detail->total_deduction, 2) }}</td>
                                    <td class="text-end fw-bold text-success">₹ {{ number_format($detail->net_salary, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Totals:</td>
                                <td class="text-end">₹ {{ number_format($payroll->details->sum('basic_salary'), 2) }}</td>
                                <td class="text-end">₹ {{ number_format($payroll->total_gross_salary, 2) }}</td>
                                <td class="text-center">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">₹ {{ number_format($payroll->details->sum('earned_salary'), 2) }}</td>
                                <td class="text-end text-danger">₹ {{ number_format($payroll->total_deduction, 2) }}</td>
                                <td class="text-end text-success">₹ {{ number_format($payroll->total_net_salary, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-body-tertiary border-0 py-3 text-end">
                <button type="button" id="btnUpdatePayrollBottom" class="btn btn-primary fw-semibold">
                    <i class="bi bi-arrow-repeat me-1"></i> Recalculate & Update Payroll
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function triggerUpdate() {
            if (confirm("Editing payroll will invalidate existing salary slips. They will need to be regenerated. Do you want to continue?")) {
                const btnTop = document.getElementById('btnUpdatePayroll');
                const btnBottom = document.getElementById('btnUpdatePayrollBottom');
                if (btnTop) btnTop.disabled = true;
                if (btnBottom) btnBottom.disabled = true;

                fetch("{{ route('payrolls.update', $payroll->id) }}", {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        alert(data.message);
                        window.location.href = data.redirect || "{{ route('payroll-processing.show', [$payroll->year, $payroll->month]) }}";
                    } else {
                        alert(data.message || 'Failed to update payroll.');
                        if (btnTop) btnTop.disabled = false;
                        if (btnBottom) btnBottom.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred while updating payroll.');
                    if (btnTop) btnTop.disabled = false;
                    if (btnBottom) btnBottom.disabled = false;
                });
            }
        }

        const btnTop = document.getElementById('btnUpdatePayroll');
        const btnBottom = document.getElementById('btnUpdatePayrollBottom');
        if (btnTop) btnTop.addEventListener('click', triggerUpdate);
        if (btnBottom) btnBottom.addEventListener('click', triggerUpdate);
    });
</script>
@endpush
@endsection
