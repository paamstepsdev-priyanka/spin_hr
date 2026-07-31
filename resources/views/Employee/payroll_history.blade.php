@extends('layouts.employee')

@section('title', 'Payroll History')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-body"><i class="bi bi-clock-history me-2 text-info"></i>Payroll History</h4>
            <small class="text-muted">View all processed monthly payroll records for your account.</small>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th>Payroll Month</th>
                        <th class="text-end">Gross Salary</th>
                        <th class="text-end text-primary fw-semibold">Earned Salary</th>
                        <th class="text-end text-danger">Deduction</th>
                        <th class="text-end text-success fw-bold">Net Salary</th>
                        <!-- Enhancement 4: Payment Status column -->
                        <th class="text-center">Payment Status</th>
                        <th>Generated Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $index => $row)
                        @php
                            $monthName = \Carbon\Carbon::createFromDate($row->payroll->year, $row->payroll->month, 1)->format('F Y');
                            $statusBadge = match ($row->payroll->status ?? 'Generated') {
                                'Draft' => 'bg-secondary',
                                'Generated' => 'bg-info text-dark',
                                'Locked' => 'bg-warning text-dark',
                                'Paid' => 'bg-success',
                                default => 'bg-primary'
                            };
                        @endphp
                        <tr>
                            <td class="text-center">{{ $payrolls->firstItem() + $index }}</td>
                            <td class="fw-bold text-dark">{{ $monthName }}</td>
                            <td class="text-end">₹ {{ number_format($row->gross_salary, 2) }}</td>
                            <td class="text-end fw-semibold text-primary">₹ {{ number_format($row->earned_salary, 2) }}</td>
                            <td class="text-end text-danger">₹ {{ number_format($row->total_deduction, 2) }}</td>
                            <td class="text-end fw-bold text-success fs-6">₹ {{ number_format($row->net_salary, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $statusBadge }} px-2 py-1">{{ $row->payroll->status ?? 'Generated' }}</span>
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
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-x display-6 d-block mb-2 text-secondary"></i>
                                No payroll history records found for your account.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
