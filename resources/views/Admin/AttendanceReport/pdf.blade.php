<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - {{ $company->name ?? 'Company' }} - {{ $monthName }} {{ $year }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
            color: #1a1a1a;
            background: #ffffff;
            padding: 20px;
        }
        .report-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .report-table th {
            background-color: #f8f9fa !important;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }
        .report-table td {
            vertical-align: middle;
            font-size: 11.5px;
        }
        .report-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 12px;
            margin-top: 25px;
            font-size: 11px;
            color: #6c757d;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <!-- Print Action Bar (Hidden when printing) -->
    <div class="no-print d-flex justify-content-between align-items-center bg-light p-3 rounded mb-4 border">
        <div>
            <strong>Attendance Report PDF Preview</strong> — {{ $company->name ?? 'Company' }} ({{ $monthName }} {{ $year }})
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-semibold">
                <i class="bi bi-printer"></i> Print / Save as PDF
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm ms-1 fw-semibold">Close</button>
        </div>
    </div>

    <!-- PDF Header -->
    <div class="report-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1 text-primary">{{ $company->name ?? 'Company' }}</h3>
            <h5 class="fw-semibold text-secondary mb-0">Monthly Attendance Report</h5>
        </div>
        <div class="text-end small">
            <div><strong>Month/Year:</strong> {{ $monthName }} {{ $year }}</div>
            <div><strong>Branch:</strong> {{ $branch ? $branch->name : 'All Branches' }}</div>
            <div><strong>Employee:</strong> {{ $employee ? ($employee->employee_code . ' - ' . $employee->name) : 'All Employees' }}</div>
            <div><strong>Generated Date:</strong> {{ date('d-M-Y h:i A') }}</div>
        </div>
    </div>

    <!-- Report Table -->
    @if($records->isEmpty())
        <div class="alert alert-warning text-center py-4">
            No attendance records found for the selected filters.
        </div>
    @else
        <table class="table table-bordered table-striped align-middle report-table mb-4">
            <thead>
                <tr class="text-center">
                    <th style="width: 35px;">#</th>
                    <th class="text-start">Emp Code</th>
                    <th class="text-start">Employee Name</th>
                    <th class="text-start">Branch</th>
                    <th class="text-start">Department</th>
                    <th>Days in Month</th>
                    <th>Leave Taken</th>
                    <th>Net Present</th>
                    <th>Leave Not Deducted</th>
                    <th class="table-success">No. of Days Payable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    @php
                        $emp = $record->employee;
                        $branchName = $record->attendanceMonth->branch->name ?? ($emp->branch->name ?? '-');
                        $deptName = $emp->department->name ?? '-';
                        $totalDays = (int) $record->total_days;
                        $leaveTaken = (int) $record->leave_taken;
                        $netPresent = (int) $record->net_present;
                        $leaveNotDeducted = (int) $record->leave_not_deducted;
                        $payableDays = (int) $record->payable_days;
                    @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ $emp->employee_code ?? '-' }}</td>
                        <td class="fw-semibold">{{ $emp->name ?? '-' }}</td>
                        <td>{{ $branchName }}</td>
                        <td>{{ $deptName }}</td>
                        <td class="text-center">{{ $totalDays }}</td>
                        <td class="text-center text-danger">{{ $leaveTaken }}</td>
                        <td class="text-center text-primary">{{ $netPresent }}</td>
                        <td class="text-center text-secondary">{{ $leaveNotDeducted }}</td>
                        <td class="text-center fw-bold table-success">{{ $payableDays }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="report-footer d-flex justify-content-between align-items-center">
        <div>
            <strong>Total Employees:</strong> {{ $records->count() }}
        </div>
        <div>
            <strong>Generated By:</strong> {{ auth()->check() ? auth()->user()->name : 'System Admin' }}
        </div>
        <div>
            <strong>Generated On:</strong> {{ date('d-M-Y H:i:s') }}
        </div>
    </div>

    <script>
        // Auto trigger print when page opens
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
