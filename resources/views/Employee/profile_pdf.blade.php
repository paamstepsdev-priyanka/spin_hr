<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile — {{ $employee->name }}</title>
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            color: #111;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="p-4">
    <!-- Print Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print border-bottom pb-3">
        <h5 class="fw-bold m-0 text-dark">Employee Profile Summary PDF</h5>
        <div>
            <button type="button" class="btn btn-primary btn-sm fw-semibold me-2" onclick="window.print();">
                Print Profile
            </button>
            <button type="button" class="btn btn-secondary btn-sm fw-semibold" onclick="window.close();">
                Close Window
            </button>
        </div>
    </div>

    <!-- Printable Container -->
    <div class="border rounded p-4 shadow-sm bg-white">
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-3 mb-3">
            <div class="col-3 text-center text-md-start">
                @if(!empty($employee->photo))
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="Photo" class="rounded border" style="max-height: 90px; width: 90px; object-fit: cover;">
                @else
                    <div class="avatar avatar-xl bg-primary text-white rounded d-inline-flex align-items-center justify-content-center fw-bold fs-2" style="width: 80px; height: 80px;">
                        {{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="col-9 text-end">
                <h3 class="fw-bold text-dark m-0">{{ $employee->company->name ?? 'SpinHR' }}</h3>
                <div class="small text-muted">{{ $employee->company->address_line1 ?? '' }} {{ $employee->company->city ?? '' }}</div>
                <h5 class="fw-bold text-primary mt-2 mb-0">EMPLOYEE SUMMARY SHEET</h5>
                <div class="small text-muted">Generated Date: {{ date('d/m/Y h:i A') }}</div>
            </div>
        </div>

        <!-- Employee Info Header Grid -->
        <div class="bg-body-tertiary border rounded p-3 mb-4">
            <div class="row text-center text-md-start">
                <div class="col-md-3 mb-2 mb-md-0">
                    <span class="text-muted small d-block">Employee Name</span>
                    <strong class="text-dark fs-6">{{ $employee->name }}</strong>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <span class="text-muted small d-block">Employee Code</span>
                    <strong class="text-primary fs-6">{{ $employee->employee_code }}</strong>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <span class="text-muted small d-block">Designation</span>
                    <strong class="text-dark">{{ $employee->designation ?? 'N/A' }}</strong>
                </div>
                <div class="col-md-3 text-md-end">
                    <span class="text-muted small d-block">Department</span>
                    <strong class="text-dark">{{ $employee->department->name ?? 'N/A' }}</strong>
                </div>
            </div>
        </div>

        <!-- Personal & Employment Details Grid -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-2 text-uppercase fs-7">Personal Details</h6>
                <table class="table table-sm table-bordered mb-0 small">
                    <tbody>
                        <tr><th class="table-light text-muted" style="width: 40%;">Father's Name</th><td>{{ $employee->father_name ?? 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">DOB</th><td>{{ $employee->dob ? date('d/m/Y', strtotime($employee->dob)) : 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">Gender</th><td>{{ ucfirst($employee->gender ?? 'N/A') }}</td></tr>
                        <tr><th class="table-light text-muted">Marital Status</th><td>{{ ucfirst($employee->marital_status ?? 'N/A') }}</td></tr>
                        <tr><th class="table-light text-muted">Email</th><td>{{ $employee->email }}</td></tr>
                        <tr><th class="table-light text-muted">Mobile</th><td>{{ $employee->cell_phone ?? $employee->mobile ?? 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">PAN</th><td>{{ $employee->pan_no ?? 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">Aadhaar</th><td>{{ $employee->aadhar_no ?? 'N/A' }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <h6 class="fw-bold text-success border-bottom pb-2 mb-2 text-uppercase fs-7">Employment Details</h6>
                <table class="table table-sm table-bordered mb-0 small">
                    <tbody>
                        <tr><th class="table-light text-muted" style="width: 40%;">Company</th><td>{{ $employee->company->name ?? 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">Branch</th><td>{{ $employee->branch->name ?? 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">Department</th><td>{{ $employee->department->name ?? 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">Employment Type</th><td>{{ $employee->employment_type ?? 'Permanent' }}</td></tr>
                        <tr><th class="table-light text-muted">Joining Date</th><td>{{ $employee->joining_date ? date('d/m/Y', strtotime($employee->joining_date)) : 'N/A' }}</td></tr>
                        <tr><th class="table-light text-muted">Experience</th><td>{{ $experience }}</td></tr>
                        <tr><th class="table-light text-muted">Reporting Manager</th><td>{{ $employee->reporting_to ?? 'Management' }}</td></tr>
                        <tr><th class="table-light text-muted">Status</th><td>{{ ucfirst($employee->status) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="mb-4">
            <h6 class="fw-bold text-info border-bottom pb-2 mb-2 text-uppercase fs-7">Bank Account Details</h6>
            <table class="table table-sm table-bordered mb-0 small">
                <tbody>
                    <tr>
                        <th class="table-light text-muted" style="width: 20%;">Account Holder</th>
                        <td style="width: 30%;">{{ $employee->account_holder_name ?? $employee->name }}</td>
                        <th class="table-light text-muted" style="width: 20%;">Account Number</th>
                        <td class="fw-bold" style="width: 30%;">{{ $employee->account_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="table-light text-muted">Bank Name</th>
                        <td>{{ $employee->bank_name ?? 'N/A' }}</td>
                        <th class="table-light text-muted">IFSC Code</th>
                        <td class="fw-bold">{{ $employee->ifsc_code ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Current Salary Summary -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-2 text-uppercase fs-7">Current Active Salary Summary</h6>
            @if($currentSalary)
                <table class="table table-sm table-bordered mb-0 small text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Basic Salary</th>
                            <th>HRA</th>
                            <th>Medical</th>
                            <th>Conveyance</th>
                            <th>Gross Monthly</th>
                            <th>Total Deduction</th>
                            <th class="table-success text-success">Net Monthly Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>₹ {{ number_format($currentSalary->basic_salary, 2) }}</td>
                            <td>₹ {{ number_format($currentSalary->hra, 2) }}</td>
                            <td>₹ {{ number_format($currentSalary->medical_allowance, 2) }}</td>
                            <td>₹ {{ number_format($currentSalary->conveyance_allowance, 2) }}</td>
                            <td class="fw-bold">₹ {{ number_format($currentSalary->gross_salary, 2) }}</td>
                            <td class="text-danger">₹ {{ number_format($currentSalary->total_deduction, 2) }}</td>
                            <td class="fw-bold text-success fs-6">₹ {{ number_format($currentSalary->net_salary, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="small text-muted italic">Salary Not Configured</div>
            @endif
        </div>

        <!-- Latest Attendance & Payroll Summaries -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-2 text-uppercase fs-7">Latest Attendance Summary</h6>
                @if($latestAttendance)
                    @php
                        $attMonthName = \Carbon\Carbon::createFromDate($latestAttendance->attendanceMonth->year, $latestAttendance->attendanceMonth->month, 1)->format('F Y');
                    @endphp
                    <table class="table table-sm table-bordered mb-0 small">
                        <tbody>
                            <tr><th class="table-light text-muted" style="width: 50%;">Month</th><td class="fw-bold">{{ $attMonthName }}</td></tr>
                            <tr><th class="table-light text-muted">Total Days</th><td>{{ $latestAttendance->total_days }}</td></tr>
                            <tr><th class="table-light text-muted">Present Days</th><td class="text-success fw-bold">{{ $latestAttendance->net_present }}</td></tr>
                            <tr><th class="table-light text-muted">Leave Taken</th><td class="text-warning fw-bold">{{ $latestAttendance->leave_taken }}</td></tr>
                            <tr><th class="table-light text-muted">Payable Days</th><td class="fw-bold text-primary">{{ $latestAttendance->payable_days }}</td></tr>
                        </tbody>
                    </table>
                @else
                    <div class="small text-muted">Attendance Not Generated</div>
                @endif
            </div>

            <div class="col-6">
                <h6 class="fw-bold text-success border-bottom pb-2 mb-2 text-uppercase fs-7">Latest Payroll Summary</h6>
                @if($latestPayroll)
                    @php
                        $payMonthName = \Carbon\Carbon::createFromDate($latestPayroll->payroll->year, $latestPayroll->payroll->month, 1)->format('F Y');
                    @endphp
                    <table class="table table-sm table-bordered mb-0 small">
                        <tbody>
                            <tr><th class="table-light text-muted" style="width: 50%;">Payroll Month</th><td class="fw-bold text-primary">{{ $payMonthName }}</td></tr>
                            <tr><th class="table-light text-muted">Gross Salary</th><td>₹ {{ number_format($latestPayroll->gross_salary, 2) }}</td></tr>
                            <tr><th class="table-light text-muted">Earned Salary</th><td>₹ {{ number_format($latestPayroll->earned_salary, 2) }}</td></tr>
                            <tr><th class="table-light text-muted">Total Deduction</th><td class="text-danger">₹ {{ number_format($latestPayroll->total_deduction, 2) }}</td></tr>
                            <tr><th class="table-light text-muted">Net Salary Paid</th><td class="fw-bold text-success">₹ {{ number_format($latestPayroll->net_salary, 2) }}</td></tr>
                        </tbody>
                    </table>
                @else
                    <div class="small text-muted">Payroll Not Generated Yet</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="border-top pt-3 text-center text-muted small">
            <div>This is an official system generated employee summary sheet from SpinHR.</div>
        </div>
    </div>
</body>
</html>
