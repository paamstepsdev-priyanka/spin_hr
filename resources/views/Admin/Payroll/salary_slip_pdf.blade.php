<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salary_Slip_{{ $detail->employee->employee_code ?? 'EMP' }}_{{ $monthName }}_{{ $detail->payroll->year }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            color: #212529;
            font-size: 13px;
        }
        .page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        .table-sm th, .table-sm td {
            padding: 5px 8px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .page-container {
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print text-center my-3">
        <button type="button" class="btn btn-primary btn-sm me-2 fw-semibold" onclick="window.print();">
            Print / Save as PDF
        </button>
        <button type="button" class="btn btn-secondary btn-sm fw-semibold" onclick="window.close();">
            Close Window
        </button>
    </div>

    <div class="page-container">
        <!-- Company Header -->
        <div class="row border-bottom pb-3 mb-3 align-items-center">
            <div class="col-3 text-start">
                @if($detail->payroll->company && $detail->payroll->company->logo)
                    <img src="{{ asset('storage/' . $detail->payroll->company->logo) }}" alt="Logo" class="img-fluid" style="max-height: 65px;">
                @else
                    <div class="bg-primary text-white fw-bold d-inline-flex align-items-center justify-content-center rounded fs-4 px-3 py-2">
                        {{ strtoupper(substr($detail->payroll->company->name ?? 'CO', 0, 2)) }}
                    </div>
                @endif
            </div>
            <div class="col-9 text-end">
                <h3 class="fw-bold mb-1 text-dark">{{ $detail->payroll->company->name ?? 'Company Name' }}</h3>
                <div class="small text-muted">
                    {{ implode(', ', array_filter([
                        $detail->payroll->company->address_line1 ?? '',
                        $detail->payroll->company->address_line2 ?? '',
                        $detail->payroll->company->city ?? '',
                        $detail->payroll->company->state ?? '',
                        $detail->payroll->company->zip_code ?? ''
                    ])) }}
                </div>
                <div class="small text-muted">
                    <span><strong>PAN:</strong> {{ $detail->payroll->company->pan_no ?? 'N/A' }}</span>
                    <span class="ms-3"><strong>GST:</strong> {{ $detail->payroll->company->gst_no ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="bg-light border rounded p-2 text-center mb-3">
            <h5 class="fw-bold m-0 text-uppercase">SALARY SLIP — {{ strtoupper($monthName) }} {{ $detail->payroll->year }}</h5>
        </div>

        <!-- Employee Info -->
        <table class="table table-bordered table-sm align-middle small mb-3">
            <tbody>
                <tr>
                    <th class="table-light text-muted" style="width: 18%;">Employee Code</th>
                    <td class="fw-bold text-dark" style="width: 32%;">{{ $detail->employee->employee_code ?? 'N/A' }}</td>
                    <th class="table-light text-muted" style="width: 18%;">Employee Name</th>
                    <td class="fw-bold text-dark" style="width: 32%;">{{ $detail->employee->name ?? 'N/A' }}</td>
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

        <!-- Attendance Summary -->
        <table class="table table-bordered table-sm text-center align-middle small mb-3">
            <thead class="table-light">
                <tr>
                    <th>Total Days</th>
                    <th>Leave Taken</th>
                    <th>Net Present</th>
                    <th>Leave Not Deducted</th>
                    <th class="table-primary text-primary fw-bold">Payable Days</th>
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

        <!-- Earnings and Deductions Table -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="border rounded p-2">
                    <h6 class="fw-bold text-success border-bottom pb-1 mb-2">EARNINGS</h6>
                    <table class="table table-sm table-borderless small mb-0">
                        <tbody>
                            <tr><td>Basic Salary</td><td class="text-end">₹ {{ number_format($detail->basic_salary, 2) }}</td></tr>
                            <tr><td>HRA</td><td class="text-end">₹ {{ number_format($detail->hra, 2) }}</td></tr>
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

            <div class="col-6">
                <div class="border rounded p-2">
                    <h6 class="fw-bold text-danger border-bottom pb-1 mb-2">DEDUCTIONS</h6>
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

        <!-- Net Salary Summary Box -->
        <div class="bg-light border rounded p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-3">
                    <small class="text-muted d-block">Gross Salary</small>
                    <span class="fw-bold">₹ {{ number_format($detail->gross_salary, 2) }}</span>
                </div>
                <div class="col-3">
                    <small class="text-muted d-block">Earned Salary</small>
                    <span class="fw-bold text-primary">₹ {{ number_format($detail->earned_salary, 2) }}</span>
                </div>
                <div class="col-3">
                    <small class="text-muted d-block">Total Deduction</small>
                    <span class="fw-bold text-danger">₹ {{ number_format($detail->total_deduction, 2) }}</span>
                </div>
                <div class="col-3 text-end">
                    <small class="text-success fw-bold d-block">NET PAYABLE</small>
                    <span class="fw-bold text-success fs-5">₹ {{ number_format($detail->net_salary, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-top pt-3 text-center text-muted small">
            <p class="mb-0 fw-semibold">This is a system generated salary slip. No signature required.</p>
        </div>
    </div>

</body>
</html>
