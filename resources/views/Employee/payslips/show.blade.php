@extends('layouts.employee')

@section('title', 'Salary Slip — ' . ($detail->employee->name ?? 'Employee'))

@push('styles')
<style media="print">
    body * {
        visibility: hidden;
    }
    #salary-slip-printable, #salary-slip-printable * {
        visibility: visible;
    }
    #salary-slip-printable {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-lg-10 mx-auto">
        <!-- Action Header Bar -->
        <div class="card border-0 shadow-sm mb-4 no-print">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-body">Salary Slip</h4>
                    <small class="text-muted">{{ $detail->employee->name ?? 'N/A' }} ({{ $detail->employee->employee_code ?? 'N/A' }}) — {{ $monthName }} {{ $detail->payroll->year }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="window.print();">
                        <i class="bi bi-printer me-1"></i> Print Slip
                    </button>
                    <a href="{{ route('employee.payslips.pdf', $detail->id) }}" class="btn btn-danger btn-sm fw-semibold" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                    </a>
                    <a href="{{ route('employee.payslips.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Printable Salary Slip Card -->
        <div class="card border shadow-sm p-4 mb-4" id="salary-slip-printable">
            <!-- Company Header Block -->
            <div class="row border-bottom pb-3 mb-3 align-items-center">
                <div class="col-md-3 text-center text-md-start mb-2 mb-md-0">
                    @if($detail->payroll->company && $detail->payroll->company->logo)
                        <img src="{{ asset('storage/' . $detail->payroll->company->logo) }}" alt="Company Logo" class="img-fluid" style="max-height: 70px;">
                    @else
                        <div class="avatar avatar-xl bg-primary text-white fw-bold d-inline-flex align-items-center justify-content-center rounded-3 fs-3 px-3 py-2">
                            {{ strtoupper(substr($detail->payroll->company->name ?? 'C', 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="col-md-9 text-center text-md-end">
                    <h3 class="fw-bold mb-1 text-dark">{{ $detail->payroll->company->name ?? 'Company Name' }}</h3>
                    <div class="small text-muted mb-1">
                        {{ implode(', ', array_filter([
                            $detail->payroll->company->address_line1 ?? '',
                            $detail->payroll->company->address_line2 ?? '',
                            $detail->payroll->company->city ?? '',
                            $detail->payroll->company->state ?? '',
                            $detail->payroll->company->zip_code ?? ''
                        ])) }}
                    </div>
                    <div class="small text-secondary">
                        @if(!empty($detail->payroll->company->email)) Email: {{ $detail->payroll->company->email }} | @endif
                        @if(!empty($detail->payroll->company->contact_no)) Contact: {{ $detail->payroll->company->contact_no }} @endif
                    </div>
                </div>
            </div>

            <!-- Payslip Title Bar -->
            <div class="bg-body-tertiary border rounded p-2 text-center mb-3">
                <h5 class="fw-bold m-0 text-uppercase tracking-wider">SALARY SLIP FOR {{ strtoupper($monthName) }} {{ $detail->payroll->year }}</h5>
            </div>

            <!-- Employee & Branch Details Table -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm align-middle small mb-0">
                    <tbody>
                        <tr>
                            <th class="table-light text-muted" style="width: 15%;">Employee Code</th>
                            <td class="fw-bold text-dark" style="width: 35%;">{{ $detail->employee->employee_code ?? 'N/A' }}</td>
                            <th class="table-light text-muted" style="width: 15%;">Employee Name</th>
                            <td class="fw-bold text-dark" style="width: 35%;">{{ $detail->employee->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Department</th>
                            <td>{{ $detail->employee->department->name ?? 'N/A' }}</td>
                            <th class="table-light text-muted">Designation</th>
                            <td>{{ $detail->employee->designation ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table-light text-muted">Branch</th>
                            <td>{{ $detail->payroll->branch->name ?? 'N/A' }}</td>
                            <th class="table-light text-muted">Joining Date</th>
                            <td>{{ $detail->employee->joining_date ? date('d/m/Y', strtotime($detail->employee->joining_date)) : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Attendance Summary Table -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm text-center align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Total Days in Month</th>
                            <th>Leave Taken</th>
                            <th>Net Present Days</th>
                            <th>Leave Not Deducted</th>
                            <th class="table-primary text-primary">Payable Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $detail->total_days }}</td>
                            <td>{{ $detail->leave_taken }}</td>
                            <td>{{ $detail->net_present }}</td>
                            <td>{{ $detail->leave_not_deducted }}</td>
                            <td class="fw-bold text-primary fs-6">{{ $detail->payable_days }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Earnings & Deductions Grid -->
            <div class="row g-3 mb-3">
                <!-- Earnings Column -->
                <div class="col-md-6">
                    <div class="border rounded p-2">
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-2 text-uppercase">Earnings Details</h6>
                        <table class="table table-sm table-borderless small mb-0">
                            <tbody>
                                <tr><td>Basic Salary</td><td class="text-end">₹ {{ number_format($detail->basic_salary, 2) }}</td></tr>
                                <tr><td>House Rent Allowance (HRA)</td><td class="text-end">₹ {{ number_format($detail->hra, 2) }}</td></tr>
                                <tr><td>Conveyance Allowance</td><td class="text-end">₹ {{ number_format($detail->conveyance_allowance, 2) }}</td></tr>
                                <tr><td>Medical Allowance</td><td class="text-end">₹ {{ number_format($detail->medical_allowance, 2) }}</td></tr>
                                <tr><td>Special Allowance</td><td class="text-end">₹ {{ number_format($detail->special_allowance, 2) }}</td></tr>
                                <tr><td>Other Allowance</td><td class="text-end">₹ {{ number_format($detail->other_allowance, 2) }}</td></tr>
                                <tr><td>Variable Allowance</td><td class="text-end">₹ {{ number_format($detail->variable_allowance, 2) }}</td></tr>
                            </tbody>
                            <tfoot class="border-top fw-bold">
                                <tr><td>Gross Monthly Salary</td><td class="text-end">₹ {{ number_format($detail->gross_salary, 2) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="col-md-6">
                    <div class="border rounded p-2">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-2 text-uppercase">Deduction Details</h6>
                        <table class="table table-sm table-borderless small mb-0">
                            <tbody>
                                <tr><td>Employee PF</td><td class="text-end">₹ {{ number_format($detail->employee_pf, 2) }}</td></tr>
                                <tr><td>ESI</td><td class="text-end">₹ {{ number_format($detail->esi, 2) }}</td></tr>
                                <tr><td>Professional Tax</td><td class="text-end">₹ {{ number_format($detail->professional_tax, 2) }}</td></tr>
                                <tr><td>TDS</td><td class="text-end">₹ {{ number_format($detail->tds, 2) }}</td></tr>
                                <tr><td>Other Deduction</td><td class="text-end">₹ {{ number_format($detail->other_deduction, 2) }}</td></tr>
                            </tbody>
                            <tfoot class="border-top fw-bold">
                                <tr><td class="text-danger">Total Deduction</td><td class="text-end text-danger">₹ {{ number_format($detail->total_deduction, 2) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Final Summary Block -->
            <div class="card bg-body-tertiary border-0 mb-4">
                <div class="card-body p-3">
                    <div class="row align-items-center text-center text-md-start">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <span class="text-muted small d-block">Gross Salary</span>
                            <span class="fw-bold text-dark fs-6">₹ {{ number_format($detail->gross_salary, 2) }}</span>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <span class="text-muted small d-block">Earned Salary</span>
                            <span class="fw-bold text-primary fs-6">₹ {{ number_format($detail->earned_salary, 2) }}</span>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <span class="text-muted small d-block">Total Deduction</span>
                            <span class="fw-bold text-danger fs-6">₹ {{ number_format($detail->total_deduction, 2) }}</span>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <span class="text-success small d-block fw-bold">NET SALARY PAYABLE</span>
                            <span class="fw-bold text-success fs-4">₹ {{ number_format($detail->net_salary, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Generated Footer -->
            <div class="border-top pt-3 text-center text-muted small">
                <p class="mb-0 fw-semibold">This is a system generated salary slip. No signature required.</p>
            </div>
        </div>
    </div>
</div>

@if(request()->get('print') == 'true')
@push('scripts')
<script>
    window.addEventListener('load', function() {
        window.print();
    });
</script>
@endpush
@endif

@endsection
