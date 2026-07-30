@php
    $queryParams = [
        'company_id' => $company->id ?? '',
        'branch_id' => $branch->id ?? '',
        'employee_id' => $employee->id ?? '',
        'month' => $month,
        'year' => $year,
    ];
    $queryString = http_build_query(array_filter($queryParams, function($val) { return $val !== '' && $val !== null; }));
@endphp

<!-- Attendance Report Results Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-1 fw-bold text-body">
                Attendance Report - {{ $company->name ?? 'Company' }}
            </h5>
            <div class="small text-muted">
                <strong>Period:</strong> {{ $monthName }} {{ $year }}
                @if($branch)
                    | <strong>Branch:</strong> {{ $branch->name }}
                @else
                    | <strong>Branch:</strong> All Branches
                @endif
                @if($employee)
                    | <strong>Employee:</strong> {{ $employee->employee_code }} - {{ $employee->name }}
                @else
                    | <strong>Employee:</strong> All Employees
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('attendance-report.export-pdf') }}?{{ $queryString }}" target="_blank" 
               class="btn btn-outline-danger btn-sm fw-semibold {{ $records->isEmpty() ? 'disabled' : '' }}"
               {{ $records->isEmpty() ? 'tabindex=-1 aria-disabled=true' : '' }}>
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('attendance-report.export-excel') }}?{{ $queryString }}" 
               class="btn btn-outline-success btn-sm fw-semibold {{ $records->isEmpty() ? 'disabled' : '' }}"
               {{ $records->isEmpty() ? 'tabindex=-1 aria-disabled=true' : '' }}>
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="card-body p-3">
        @if($records->isEmpty())
            <div class="alert alert-warning mb-0 text-center py-4 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4 d-block mb-2 text-warning"></i>
                <span class="fw-semibold">No attendance records found for the selected filters.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap">
                    <thead class="table-light text-center">
                        <tr>
                            <th scope="col" class="fw-bold" style="width: 40px;">#</th>
                            <th scope="col" class="fw-bold text-start">Emp Code</th>
                            <th scope="col" class="fw-bold text-start">Employee Name</th>
                            <th scope="col" class="fw-bold text-start">Company</th>
                            <th scope="col" class="fw-bold text-start">Branch</th>
                            <th scope="col" class="fw-bold text-start">Department</th>
                            <th scope="col" class="fw-bold">Month</th>
                            <th scope="col" class="fw-bold">Year</th>
                            <th scope="col" class="fw-bold">No. of Days in Month</th>
                            <th scope="col" class="fw-bold">Leave Taken</th>
                            <th scope="col" class="fw-bold">Net Present</th>
                            <th scope="col" class="fw-bold">Leave Not Deducted</th>
                            <th scope="col" class="fw-bold text-success">No. of Days Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $index => $record)
                            @php
                                $emp = $record->employee;
                                $companyName = $record->attendanceMonth->company->name ?? ($emp->company->name ?? '-');
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
                                <td>{{ $companyName }}</td>
                                <td>{{ $branchName }}</td>
                                <td>{{ $deptName }}</td>
                                <td class="text-center">{{ $monthName }}</td>
                                <td class="text-center">{{ $year }}</td>
                                <td class="text-center fw-semibold">{{ $totalDays }}</td>
                                <td class="text-center text-danger fw-semibold">{{ $leaveTaken }}</td>
                                <td class="text-center text-primary fw-semibold">{{ $netPresent }}</td>
                                <td class="text-center text-secondary fw-semibold">{{ $leaveNotDeducted }}</td>
                                <td class="text-center text-success fw-bold bg-success-subtle">{{ $payableDays }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
