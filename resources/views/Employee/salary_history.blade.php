@extends('layouts.employee')

@section('title', 'Salary History')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-body"><i class="bi bi-currency-rupee me-2 text-success"></i>Salary Structure History</h4>
            <small class="text-muted">View your effective salary structure records over time.</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="window.print();">
            <i class="bi bi-printer me-1"></i> Print Page
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th>Effective From</th>
                        <th>Effective To</th>
                        <!-- Enhancement 3: Salary Type column -->
                        <th>Salary Type</th>
                        <th class="text-end">Basic Salary</th>
                        <th class="text-end">Gross Salary</th>
                        <th class="text-end text-danger">Total Deduction</th>
                        <th class="text-end text-success fw-bold">Net Salary</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $index => $sal)
                        <tr>
                            <td class="text-center">{{ $salaries->firstItem() + $index }}</td>
                            <td class="fw-semibold">{{ date('d/m/Y', strtotime($sal->effective_from)) }}</td>
                            <td>{{ $sal->effective_to ? date('d/m/Y', strtotime($sal->effective_to)) : 'Present' }}</td>
                            <td><span class="badge bg-secondary px-2 py-1">{{ $employee->employment_type ?? 'Permanent' }}</span></td>
                            <td class="text-end">₹ {{ number_format($sal->basic_salary, 2) }}</td>
                            <td class="text-end fw-semibold">₹ {{ number_format($sal->gross_salary, 2) }}</td>
                            <td class="text-end text-danger">₹ {{ number_format($sal->total_deduction, 2) }}</td>
                            <td class="text-end fw-bold text-success fs-6">₹ {{ number_format($sal->net_salary, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ strtolower($sal->status) === 'active' ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                    {{ ucfirst($sal->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-info py-0 px-2" data-bs-toggle="modal" data-bs-target="#salaryModal{{ $sal->id }}">
                                    <i class="bi bi-eye me-1"></i> View Details
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail View -->
                        <div class="modal fade" id="salaryModal{{ $sal->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-body-tertiary">
                                        <h5 class="modal-title fw-bold text-body">Salary Structure Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <div class="border rounded p-3 bg-light">
                                                    <h6 class="fw-bold text-success border-bottom pb-2 mb-2">ALLOWANCES</h6>
                                                    <table class="table table-sm table-borderless small mb-0">
                                                        <tbody>
                                                            <tr><td>Basic Salary:</td><td class="text-end fw-bold">₹ {{ number_format($sal->basic_salary, 2) }}</td></tr>
                                                            <tr><td>HRA:</td><td class="text-end">₹ {{ number_format($sal->hra, 2) }}</td></tr>
                                                            <tr><td>Conveyance Allowance:</td><td class="text-end">₹ {{ number_format($sal->conveyance_allowance, 2) }}</td></tr>
                                                            <tr><td>Medical Allowance:</td><td class="text-end">₹ {{ number_format($sal->medical_allowance, 2) }}</td></tr>
                                                            <tr><td>Special Allowance:</td><td class="text-end">₹ {{ number_format($sal->special_allowance, 2) }}</td></tr>
                                                            <tr><td>Other Allowance:</td><td class="text-end">₹ {{ number_format($sal->other_allowance, 2) }}</td></tr>
                                                            <tr><td>Variable Allowance:</td><td class="text-end">₹ {{ number_format($sal->variable_allowance, 2) }}</td></tr>
                                                        </tbody>
                                                        <tfoot class="border-top fw-bold">
                                                            <tr><td>Gross Monthly Salary:</td><td class="text-end text-success">₹ {{ number_format($sal->gross_salary, 2) }}</td></tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="border rounded p-3 bg-light">
                                                    <h6 class="fw-bold text-danger border-bottom pb-2 mb-2">DEDUCTIONS</h6>
                                                    <table class="table table-sm table-borderless small mb-0">
                                                        <tbody>
                                                            <tr><td>Employee PF:</td><td class="text-end">₹ {{ number_format($sal->employee_pf, 2) }}</td></tr>
                                                            <tr><td>ESI:</td><td class="text-end">₹ {{ number_format($sal->esi, 2) }}</td></tr>
                                                            <tr><td>Professional Tax:</td><td class="text-end">₹ {{ number_format($sal->professional_tax, 2) }}</td></tr>
                                                            <tr><td>TDS:</td><td class="text-end">₹ {{ number_format($sal->tds, 2) }}</td></tr>
                                                            <tr><td>Other Deduction:</td><td class="text-end">₹ {{ number_format($sal->other_deduction, 2) }}</td></tr>
                                                        </tbody>
                                                        <tfoot class="border-top fw-bold">
                                                            <tr><td class="text-danger">Total Deduction:</td><td class="text-end text-danger">₹ {{ number_format($sal->total_deduction, 2) }}</td></tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card bg-success bg-opacity-10 border-success p-3 text-center">
                                            <span class="text-success small fw-bold uppercase">NET MONTHLY SALARY PAYABLE</span>
                                            <span class="fs-4 fw-bold text-success">₹ {{ number_format($sal->net_salary, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-currency-exchange display-6 d-block mb-2 text-secondary"></i>
                                No salary history records found for your profile.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salaries->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
