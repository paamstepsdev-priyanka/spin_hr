@extends('layouts.admin')

@section('title', 'View Payroll Batch')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-body">Payroll Summary — {{ $monthName }} {{ $payroll->year }}</h4>
                    <small class="text-muted">Status: <span class="badge bg-success ms-1">{{ $payroll->status }}</span> | Generated on {{ $payroll->created_at ? $payroll->created_at->format('d/m/Y h:i A') : 'N/A' }}</small>
                </div>
                <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left me-1"></i> Back to History
                </a>
            </div>
        </div>

        <!-- Summary Cards Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Company & Branch</div>
                    <div class="fw-bold text-dark fs-6 mt-1">{{ $payroll->company->name ?? 'N/A' }}</div>
                    <div class="small text-secondary">{{ $payroll->branch->name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Month / Year</div>
                    <div class="fw-bold text-primary fs-5 mt-1">{{ $monthName }} {{ $payroll->year }}</div>
                    <div class="small text-secondary">{{ $payroll->details->count() }} Employees</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Total Gross Salary</div>
                    <div class="fw-bold text-dark fs-5 mt-1">₹ {{ number_format($payroll->total_gross_salary, 2) }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="text-muted small fw-semibold">Total Deductions</div>
                    <div class="fw-bold text-danger fs-5 mt-1">₹ {{ number_format($payroll->total_deduction, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100 bg-success bg-opacity-10 border-success border-opacity-25">
                    <div class="text-success small fw-bold">Total Net Salary</div>
                    <div class="fw-bold text-success fs-4 mt-1">₹ {{ number_format($payroll->total_net_salary, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Detailed Employee Payroll Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Employee Payroll Breakdown</h5>
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
                                <th scope="col" class="text-center" style="width: 140px;">Salary Slip Actions</th>
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
                                    <td class="text-center">
                                        <!-- View Salary Slip -->
                                        <a href="{{ route('payrolls.salary-slip', $detail->id) }}" class="btn btn-xs btn-outline-info py-0 px-1 me-1" title="View Salary Slip">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <!-- Print Salary Slip -->
                                        <a href="{{ route('payrolls.salary-slip', $detail->id) }}?print=true" class="btn btn-xs btn-outline-secondary py-0 px-1 me-1" title="Print Salary Slip" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <!-- PDF Download -->
                                        <a href="{{ route('payrolls.salary-slip.pdf', $detail->id) }}" class="btn btn-xs btn-outline-danger py-0 px-1" title="Download PDF / Print A4" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>
                                    </td>
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
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
