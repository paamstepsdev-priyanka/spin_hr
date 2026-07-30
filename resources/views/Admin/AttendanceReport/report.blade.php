<!-- Export Toolbar Card -->
<div class="card border-0 shadow-sm mb-3 d-print-none">
    <div class="card-body bg-body-tertiary rounded p-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-body ps-2">Report Actions</h6>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary px-3" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            <button type="button" class="btn btn-outline-danger px-3" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </button>
            <button type="button" class="btn btn-outline-success px-3" onclick="exportReportToExcel()">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
        </div>
    </div>
</div>

<!-- Employee Information Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-body-tertiary border-0 py-2">
        <h6 class="mb-0 fw-bold text-body">Employee Information</h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-2 small">
            <div class="col-md-3 col-6">
                <span class="text-muted d-block">Employee Name:</span>
                <strong class="text-body fs-6">{{ $employee->name }}</strong>
            </div>
            <div class="col-md-3 col-6">
                <span class="text-muted d-block">Employee Code:</span>
                <strong class="text-body fs-6">{{ $employee->employee_code }}</strong>
            </div>
            <div class="col-md-3 col-6">
                <span class="text-muted d-block">Employment Type:</span>
                <strong class="text-body">{{ $employee->employment_type ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3 col-6">
                <span class="text-muted d-block">Month & Year:</span>
                <strong class="text-primary fs-6">{{ $monthName }} {{ $year }}</strong>
            </div>

            <div class="col-md-3 col-6 mt-2">
                <span class="text-muted d-block">Company:</span>
                <strong class="text-body">{{ $employee->company ? $employee->company->name : 'N/A' }}</strong>
            </div>
            <div class="col-md-3 col-6 mt-2">
                <span class="text-muted d-block">Branch:</span>
                <strong class="text-body">{{ $employee->branch ? $employee->branch->name : 'N/A' }}</strong>
            </div>
            <div class="col-md-3 col-6 mt-2">
                <span class="text-muted d-block">Department:</span>
                <strong class="text-body">{{ $employee->department ? $employee->department->name : 'N/A' }}</strong>
            </div>
            <div class="col-md-3 col-6 mt-2">
                <span class="text-muted d-block">Designation:</span>
                <strong class="text-body">{{ $employee->designation ?? 'N/A' }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Compact Monthly Summary (Placed ON TOP of Table) -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-body-tertiary border-0 py-2">
        <h6 class="mb-0 fw-bold text-body">Monthly Summary</h6>
    </div>
    <div class="card-body p-3">
        <div class="row g-2 text-center">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border rounded bg-body-tertiary">
                    <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Calendar Days</span>
                    <strong class="fs-6 text-body">{{ $summary['total_calendar_days'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border rounded bg-body-tertiary">
                    <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Working Days</span>
                    <strong class="fs-6 text-dark">{{ $summary['working_days'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border border-success-subtle rounded bg-success-subtle text-success">
                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Present</span>
                    <strong class="fs-6">{{ $summary['present'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border border-danger-subtle rounded bg-danger-subtle text-danger">
                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Absent</span>
                    <strong class="fs-6">{{ $summary['absent'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border border-info-subtle rounded bg-info-subtle text-info-emphasis">
                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Leave</span>
                    <strong class="fs-6">{{ $summary['leave'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border border-warning-subtle rounded bg-warning-subtle text-warning-emphasis">
                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Half Day</span>
                    <strong class="fs-6">{{ $summary['half_day'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border border-secondary-subtle rounded bg-secondary-subtle text-dark">
                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Holiday</span>
                    <strong class="fs-6">{{ $summary['holiday'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border rounded bg-body-tertiary">
                    <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Not Marked</span>
                    <strong class="fs-6 text-muted">{{ $summary['not_marked'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border rounded bg-body-tertiary">
                    <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Working Hours</span>
                    <strong class="fs-6 text-dark">{{ $summary['total_working_hours_formatted'] }}</strong>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="p-2 border rounded bg-body-tertiary">
                    <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">Overtime</span>
                    <strong class="fs-6 text-dark">{{ $summary['overtime_hours_formatted'] }}</strong>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="p-2 border rounded bg-primary text-white shadow-sm">
                    <span class="d-block text-uppercase fw-bold text-white-50" style="font-size: 0.7rem;">Payable Days</span>
                    <strong class="fs-6 text-white">{{ $summary['payable_days'] }} Days</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Attendance Table Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary border-0 py-2">
        <h6 class="mb-0 fw-bold text-body">Monthly Attendance Log</h6>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="report-details-table">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="fw-bold text-center" style="width: 100px;">Date</th>
                        <th scope="col" class="fw-bold text-center" style="width: 70px;">Day</th>
                        <th scope="col" class="fw-bold text-center" style="width: 140px;">Attendance Status</th>
                        <th scope="col" class="fw-bold text-center" style="width: 100px;">Check In</th>
                        <th scope="col" class="fw-bold text-center" style="width: 100px;">Check Out</th>
                        <th scope="col" class="fw-bold text-center" style="width: 110px;">Working Hours</th>
                        <th scope="col" class="fw-bold text-start">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyLogs as $log)
                        <tr>
                            <td class="text-center fw-semibold text-body">{{ $log['date'] }}</td>
                            <td class="text-center fw-semibold text-muted">{{ $log['day'] }}</td>
                            <td class="text-center">
                                @if($log['status'] === 'Present')
                                    <span class="badge bg-success text-white px-2 py-1">🟢 Present</span>
                                @elseif($log['status'] === 'Absent')
                                    <span class="badge bg-danger text-white px-2 py-1">🔴 Absent</span>
                                @elseif($log['status'] === 'Leave')
                                    <span class="badge bg-info text-dark px-2 py-1">🔵 Leave</span>
                                @elseif($log['status'] === 'Half Day')
                                    <span class="badge bg-warning text-dark px-2 py-1">🟡 Half Day</span>
                                @elseif($log['status'] === 'Holiday')
                                    <span class="badge bg-dark text-white px-2 py-1">⚫ Holiday</span>
                                @else
                                    <span class="badge bg-secondary text-white px-2 py-1">⚪ Not Marked</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $log['check_in'] }}</td>
                            <td class="text-center">{{ $log['check_out'] }}</td>
                            <td class="text-center fw-semibold">{{ $log['working_hours'] }}</td>
                            <td>{{ $log['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function exportReportToExcel() {
        let table = document.getElementById("report-details-table");
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        downloadLink.href = url;
        downloadLink.download = 'Attendance_Report_{{ $employee->employee_code }}_{{ $monthName }}_{{ $year }}.xls';
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
