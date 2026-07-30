<form id="form-generate-payroll" action="{{ route('payrolls.store') }}" method="POST">
    @csrf
    <input type="hidden" name="company_id" value="{{ $companyId }}">
    <input type="hidden" name="branch_id" value="{{ $branchId }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="year" value="{{ $year }}">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-body">Payroll Calculation Preview — {{ $monthName }} {{ $year }}</h5>
                <small class="text-muted">Review server-calculated salary figures before final generation</small>
            </div>
            <button type="submit" class="btn btn-success btn-sm fw-semibold px-4" id="btn-generate-payroll" {{ $hasErrors ? 'disabled' : '' }}>
                <i class="bi bi-check-circle me-1"></i> Generate Payroll
            </button>
        </div>
        <div class="card-body p-3">
            @if($hasErrors)
                <div class="alert alert-danger py-2 mb-3 small fw-semibold" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Cannot generate payroll: One or more employees have missing salary or attendance data.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped align-middle small mb-0 text-nowrap">
                    <thead class="table-light text-center">
                        <tr>
                            <th scope="col" style="width: 30px;">#</th>
                            <th scope="col" class="text-start">Emp Code</th>
                            <th scope="col" class="text-start">Employee Name</th>
                            <th scope="col" class="text-start">Department</th>
                            <th scope="col" class="text-end">Basic Salary</th>
                            <th scope="col" class="text-end">Gross Salary</th>
                            <th scope="col">Total Days</th>
                            <th scope="col">Leave Taken</th>
                            <th scope="col">Net Present</th>
                            <th scope="col">Leave Not Ded.</th>
                            <th scope="col">Payable Days</th>
                            <th scope="col" class="text-end">Per Day Salary</th>
                            <th scope="col" class="text-end">Earned Salary</th>
                            <th scope="col" class="text-end">Total Deduction</th>
                            <th scope="col" class="text-end">Net Salary</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $index => $row)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $row['employee_code'] }}</td>
                                <td class="text-start fw-bold text-body">{{ $row['name'] }}</td>
                                <td class="text-start">{{ $row['department_name'] }}</td>
                                
                                <td class="text-end">₹ {{ number_format($row['basic_salary'], 2) }}</td>
                                <td class="text-end fw-semibold">₹ {{ number_format($row['gross_salary'], 2) }}</td>
                                
                                <td class="text-center">{{ $row['total_days'] }}</td>
                                <td class="text-center">{{ $row['leave_taken'] }}</td>
                                <td class="text-center">{{ $row['net_present'] }}</td>
                                <td class="text-center">{{ $row['leave_not_deducted'] }}</td>
                                <td class="text-center fw-bold text-primary">{{ $row['payable_days'] }}</td>

                                <td class="text-end">₹ {{ number_format($row['per_day_salary'], 2) }}</td>
                                <td class="text-end fw-semibold">₹ {{ number_format($row['earned_salary'], 2) }}</td>
                                <td class="text-end text-danger">₹ {{ number_format($row['total_deduction'], 2) }}</td>
                                <td class="text-end fw-bold text-success">₹ {{ number_format($row['net_salary'], 2) }}</td>

                                <td class="text-center">
                                    @if(!$row['has_salary'])
                                        <a href="{{ route('employees.salaries.index', $row['employee_id']) }}" target="_blank" class="badge bg-danger text-white text-decoration-none" title="Click to manage salary for this employee">
                                            Salary Missing <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    @elseif(!$row['has_attendance'])
                                        <a href="{{ route('attendance.create') }}" target="_blank" class="badge bg-danger text-white text-decoration-none" title="Click to mark monthly attendance">
                                            Attendance Missing <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    @else
                                        <span class="badge bg-success">Ready</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-body-tertiary border-0 py-2 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Total Employees: <strong>{{ count($records) }}</strong></span>
            <button type="submit" class="btn btn-success btn-sm fw-semibold px-4" {{ $hasErrors ? 'disabled' : '' }}>
                <i class="bi bi-check-circle me-1"></i> Generate Payroll
            </button>
        </div>
    </div>
</form>
