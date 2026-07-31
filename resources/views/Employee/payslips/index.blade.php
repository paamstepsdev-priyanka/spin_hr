@extends('layouts.employee')

@section('title', 'My Payslips')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-body"><i class="bi bi-file-earmark-text me-2 text-primary"></i>My Salary Payslips</h4>
            <small class="text-muted">Access and download your monthly salary slips.</small>
        </div>
    </div>
    <div class="card-body">
        @if($payslips->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th class="text-end">Gross Salary</th>
                            <th class="text-end text-success fw-bold">Net Salary</th>
                            <th class="text-center">Status</th>
                            <th>Generated Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payslips as $index => $row)
                            @php
                                $monthName = \Carbon\Carbon::createFromDate($row->payroll->year, $row->payroll->month, 1)->format('F');
                            @endphp
                            <tr>
                                <td class="text-center">{{ $payslips->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark">{{ $monthName }}</td>
                                <td>{{ $row->payroll->year }}</td>
                                <td class="text-end">₹ {{ number_format($row->gross_salary, 2) }}</td>
                                <td class="text-end fw-bold text-success fs-6">₹ {{ number_format($row->net_salary, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success px-2 py-1">{{ $row->payroll->status ?? 'Generated' }}</span>
                                </td>
                                <td>{{ $row->created_at ? $row->created_at->format('d/m/Y h:i A') : 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('employee.payslips.show', $row->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2 me-1" title="View Payslip">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                    <a href="{{ route('employee.payslips.pdf', $row->id) }}" class="btn btn-xs btn-outline-danger py-0 px-2 me-1" target="_blank" title="Download PDF">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                    </a>
                                    <a href="{{ route('employee.payslips.show', $row->id) }}?print=true" class="btn btn-xs btn-outline-secondary py-0 px-1" target="_blank" title="Print">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($payslips->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $payslips->links() }}
                </div>
            @endif
        @else
            <!-- Enhancement 5: Clear Empty State Card -->
            <div class="text-center py-5 my-3">
                <i class="bi bi-file-earmark-x display-3 text-secondary mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Payroll Not Generated Yet</h5>
                <p class="text-muted small mb-0">Your monthly salary payslips will appear here once payroll is processed by HR.</p>
            </div>
        @endif
    </div>
</div>
@endsection
