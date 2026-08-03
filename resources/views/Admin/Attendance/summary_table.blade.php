<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle small mb-0 w-100 text-nowrap" id="employee-summary-grid">
                <thead class="table-light align-middle text-center fw-bold">
                    <tr>
                        <th scope="col" style="min-width: 180px;" class="text-start">Employee Name</th>
                        <th scope="col" style="width: 110px;" class="text-center">Salary</th>
                        <th scope="col" style="min-width: 140px;" class="text-start">Branch</th>
                        <th scope="col" style="width: 150px;" class="bg-light">No. of Days in Month</th>
                        <th scope="col" style="width: 130px;">Leave Taken</th>
                        <th scope="col" style="width: 130px;" class="table-info fw-bold">Net Present</th>
                        <th scope="col" style="width: 150px;">Leave Not Deducted</th>
                        <th scope="col" style="width: 160px;" class="table-success fw-bold">No. of Days Payable</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $index => $rec)
                        @php
                            $isIncomplete = ($rec['leave_taken'] === null || $rec['leave_not_deducted'] === null);
                        @endphp
                        <tr class="employee-row {{ $isIncomplete ? 'table-danger' : '' }}">
                            <td class="fw-semibold text-body text-start">{{ $rec['name'] }}</td>
                            <td class="text-center">
                                @if(!empty($rec['salary_exists']))
                                    <span class="badge bg-success py-1 px-2 text-nowrap" style="font-size: 0.75rem;">
                                        Salary Set
                                    </span>
                                @else
                                    <span class="badge bg-danger py-1 px-2 text-nowrap" style="font-size: 0.75rem;">
                                        Not Set
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted text-start">{{ $rec['branch_name'] }}</td>
                            
                            <td class="text-center bg-light fw-bold">
                                {{ $rec['total_days'] }}
                            </td>
                            
                            <td class="text-center">
                                {{ $rec['leave_taken'] !== null ? $rec['leave_taken'] : '-' }}
                            </td>
                            
                            <td class="table-info text-center fw-bold text-primary">
                                {{ $rec['net_present'] }}
                            </td>

                            <td class="text-center">
                                {{ $rec['leave_not_deducted'] !== null ? $rec['leave_not_deducted'] : '-' }}
                            </td>

                            <td class="table-success text-center fw-bold text-success">
                                {{ $rec['payable_days'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-semibold text-body small me-1"><i class="bi bi-bar-chart-fill me-1"></i>Attendance Progress:</span>
                <span class="badge bg-success px-2 py-1 fw-semibold">Completed: {{ $completedCount }} Employees</span>
                <span class="badge bg-danger px-2 py-1 fw-semibold">Pending: {{ $pendingCount }} Employees</span>
                <span class="badge bg-secondary px-2 py-1 fw-semibold">Total: {{ count($records) }} Employees</span>
            </div>
            <div class="text-muted small">
                <i class="bi bi-lock-fill me-1 text-secondary"></i> Read-Only Mode
            </div>
        </div>
    </div>
</div>
