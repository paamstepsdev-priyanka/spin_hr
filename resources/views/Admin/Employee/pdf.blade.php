<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile - {{ $employee->employee_code }} - {{ $employee->name }}</title>
    <!-- Core Theme & Bootstrap 5 Styles -->
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style media="print">
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            background-color: #fff !important;
            color: #000 !important;
            font-size: 11pt;
        }
        .no-print {
            display: none !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="bg-light py-4">

    <div class="container-lg bg-white p-4 p-md-5 rounded shadow-sm border" style="max-width: 900px;">
        <!-- Top Toolbar (Hidden on Print) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print pb-3 border-bottom">
            <div class="fw-bold text-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i>Employee Profile Document
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print / Save PDF
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="window.close()">
                    <i class="bi bi-x-lg me-1"></i>Close
                </button>
            </div>
        </div>

        <!-- Document Header -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div class="d-flex align-items-center gap-3">
                @if($employee->photo && Storage::disk('public')->exists($employee->photo))
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 90px; height: 90px;">
                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h2 class="fw-bold text-body mb-1">{{ $employee->name }}</h2>
                    <div class="text-body-secondary fw-semibold">
                        <span class="badge bg-dark text-white me-2">{{ $employee->employee_code }}</span>
                        <span>{{ $employee->designation ?? 'N/A' }}</span>
                    </div>
                    <div class="small text-muted mt-1">
                        {{ $employee->company->name ?? 'N/A' }} &bull; {{ $employee->branch->name ?? 'N/A' }} &bull; {{ $employee->department->name ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="text-end small">
                <div class="fw-bold text-uppercase text-primary fs-5">SpinHR</div>
                <div class="text-muted">360° Employee Profile</div>
                <div class="text-muted mt-1">Date: {{ date('d M Y') }}</div>
            </div>
        </div>

        <!-- Personal Details -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-1 mb-3 small" style="letter-spacing: 0.5px;">
                Personal Details
            </h6>
            <div class="row g-2 small">
                <div class="col-4">
                    <span class="text-muted d-block">Full Name:</span>
                    <strong class="text-body">{{ $employee->name }}</strong>
                </div>
                <div class="col-4">
                    <span class="text-muted d-block">Father's Name:</span>
                    <strong class="text-body">{{ $employee->father_name ?? 'N/A' }}</strong>
                </div>
                <div class="col-4">
                    <span class="text-muted d-block">Gender / DOB:</span>
                    <strong class="text-body">{{ ucfirst($employee->gender ?? 'N/A') }} / {{ $employee->dob ? date('d M Y', strtotime($employee->dob)) : 'N/A' }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Marital Status:</span>
                    <strong class="text-body">{{ ucfirst($employee->marital_status ?? 'N/A') }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Mobile:</span>
                    <strong class="text-body">{{ $employee->mobile ?? 'N/A' }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Email:</span>
                    <strong class="text-body">{{ $employee->email }}</strong>
                </div>
            </div>
        </div>

        <!-- Employment Details -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-1 mb-3 small" style="letter-spacing: 0.5px;">
                Employment Details
            </h6>
            <div class="row g-2 small">
                <div class="col-4">
                    <span class="text-muted d-block">Company:</span>
                    <strong class="text-body">{{ $employee->company->name ?? 'N/A' }}</strong>
                </div>
                <div class="col-4">
                    <span class="text-muted d-block">Branch:</span>
                    <strong class="text-body">{{ $employee->branch->name ?? 'N/A' }}</strong>
                </div>
                <div class="col-4">
                    <span class="text-muted d-block">Department:</span>
                    <strong class="text-body">{{ $employee->department->name ?? 'N/A' }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Designation:</span>
                    <strong class="text-body">{{ $employee->designation ?? 'N/A' }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Joining Date:</span>
                    <strong class="text-body">{{ $employee->joining_date ? date('d M Y', strtotime($employee->joining_date)) : 'N/A' }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Total Experience:</span>
                    <strong class="text-body">{{ $experienceStr }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Employment Type:</span>
                    <strong class="text-body">{{ ucfirst($employee->employment_type ?? 'N/A') }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Reporting Manager:</span>
                    <strong class="text-body">{{ $employee->reporting_to ?? 'N/A' }}</strong>
                </div>
                <div class="col-4 mt-2">
                    <span class="text-muted d-block">Status:</span>
                    <strong class="text-body">{{ ucfirst($employee->status) }}</strong>
                </div>
            </div>
        </div>

        <!-- Address & Bank Information -->
        <div class="row g-4 mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-1 mb-2 small" style="letter-spacing: 0.5px;">
                    Address Information
                </h6>
                <div class="small">
                    <span class="text-muted d-block">Current Address:</span>
                    <div class="fw-semibold text-body">
                        {{ implode(', ', array_filter([$employee->address_line1, $employee->address_line2, $employee->city, $employee->state, $employee->zip_code])) ?: 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-1 mb-2 small" style="letter-spacing: 0.5px;">
                    Bank & Identification
                </h6>
                <div class="row g-2 small">
                    <div class="col-6">
                        <span class="text-muted d-block">Bank:</span>
                        <strong class="text-body">{{ $employee->bank_name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">A/C No:</span>
                        <strong class="text-body">{{ $employee->account_no ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">IFSC:</span>
                        <strong class="text-body">{{ $employee->ifsc_code ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">PAN / Aadhaar:</span>
                        <strong class="text-body">{{ $employee->pan_no ?? 'N/A' }} / {{ $employee->aadhar_no ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial & HR Summary (Salary, Attendance, Payroll) -->
        <div class="row g-3 mb-4">
            <!-- Current Salary Summary -->
            <div class="col-4">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="fw-bold text-uppercase text-secondary small mb-2">Current Active Salary</div>
                    @if($currentSalary)
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Basic Salary:</span>
                            <span class="fw-semibold">₹ {{ number_format($currentSalary->basic_salary, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Gross Salary:</span>
                            <span class="fw-bold">₹ {{ number_format($currentSalary->gross_salary, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Deduction:</span>
                            <span class="text-danger">₹ {{ number_format($currentSalary->total_deduction, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                            <span class="fw-bold">Net Salary:</span>
                            <span class="fw-bold text-success">₹ {{ number_format($currentSalary->net_salary, 2) }}</span>
                        </div>
                    @else
                        <div class="text-muted small italic py-2">Salary Not Configured</div>
                    @endif
                </div>
            </div>

            <!-- Latest Attendance Summary -->
            <div class="col-4">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="fw-bold text-uppercase text-secondary small mb-2">Latest Attendance</div>
                    @if($latestAttendance && $latestAttendance->attendanceMonth)
                        @php
                            $monthName = \Carbon\Carbon::createFromDate($latestAttendance->attendanceMonth->year, $latestAttendance->attendanceMonth->month, 1)->format('F Y');
                        @endphp
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Month:</span>
                            <span class="fw-semibold">{{ $monthName }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Total Days:</span>
                            <span>{{ $latestAttendance->total_days }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Present / Leave:</span>
                            <span class="fw-bold text-success">{{ $latestAttendance->net_present }} / {{ $latestAttendance->leave_taken }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                            <span class="fw-bold">Payable Days:</span>
                            <span class="fw-bold text-primary">{{ $latestAttendance->payable_days }}</span>
                        </div>
                    @else
                        <div class="text-muted small italic py-2">Attendance Not Available</div>
                    @endif
                </div>
            </div>

            <!-- Latest Payroll Summary -->
            <div class="col-4">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="fw-bold text-uppercase text-secondary small mb-2">Latest Payroll Slip</div>
                    @if($latestPayroll && $latestPayroll->payroll)
                        @php
                            $payMonthName = \Carbon\Carbon::createFromDate($latestPayroll->payroll->year, $latestPayroll->payroll->month, 1)->format('F Y');
                        @endphp
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Month:</span>
                            <span class="fw-semibold">{{ $payMonthName }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Earned Salary:</span>
                            <span>₹ {{ number_format($latestPayroll->earned_salary, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Deduction:</span>
                            <span class="text-danger">₹ {{ number_format($latestPayroll->total_deduction, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                            <span class="fw-bold">Net Salary:</span>
                            <span class="fw-bold text-success">₹ {{ number_format($latestPayroll->net_salary, 2) }}</span>
                        </div>
                    @else
                        <div class="text-muted small italic py-2">Payroll Not Generated</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="border-top pt-3 mt-4 text-center text-muted small">
            <p class="mb-1">This is a system generated employee profile summary. No signature required.</p>
            <div>Generated Date: {{ date('d/m/Y h:i A') }} &bull; Generated By: {{ Auth::user()->name ?? 'System Admin' }}</div>
        </div>

    </div>

</body>
</html>
