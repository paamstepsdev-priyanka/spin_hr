<div class="card border-0 shadow-sm">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-body">
            Step 2: Monthly Attendance Summary ({{ $monthName }} {{ $year }}) - Days in Month: {{ $totalDays }}
        </h5>
    </div>
    <div class="card-body p-3">
        <form id="attendance-save-form">
            @csrf
            <input type="hidden" name="company_id" value="{{ $companyId }}">
            <input type="hidden" name="branch_id" value="{{ $branchId }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle small mb-0 w-100 text-nowrap" id="employee-attendance-grid">
                    <thead class="table-light align-middle text-center fw-bold">
                        <tr>
                            <th scope="col" style="min-width: 180px;" class="text-start">Employee Name</th>
                            <th scope="col" style="width: 110px;" class="text-center">Salary</th>
                            <th scope="col" style="min-width: 140px;" class="text-start">Branch</th>
                            <th scope="col" style="width: 150px;" class="bg-light">No. of Days in Month</th>
                            <th scope="col" style="width: 130px;">Leave Taken <span class="text-danger">*</span></th>
                            <th scope="col" style="width: 130px;" class="table-info fw-bold">Net Present</th>
                            <th scope="col" style="width: 150px;">Leave Not Deducted <span class="text-danger">*</span></th>
                            <th scope="col" style="width: 160px;" class="table-success fw-bold">No. of Days Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $index => $rec)
                            <tr class="employee-row" data-index="{{ $index }}" data-emp-name="{{ $rec['name'] }}">
                                <input type="hidden" name="details[{{ $index }}][employee_id]" value="{{ $rec['employee_id'] }}">
                                
                                <td class="fw-semibold text-body text-start">{{ $rec['name'] }}</td>
                                <td class="text-center">
                                    @if(!empty($rec['salary_exists']))
                                        <button type="button" class="btn btn-success btn-sm py-0 px-2 small text-nowrap" disabled style="font-size: 0.75rem;">
                                            Salary Set
                                        </button>
                                    @else
                                        <a href="{{ route('employees.salaries.index', $rec['employee_id']) }}" 
                                           class="btn btn-danger btn-sm py-0 px-2 small text-nowrap" 
                                           target="_blank" 
                                           title="Salary is not configured for this employee."
                                           data-bs-toggle="tooltip">
                                            Set Salary
                                        </a>
                                    @endif
                                </td>
                                <td class="text-muted text-start">{{ $rec['branch_name'] }}</td>
                                
                                <td class="text-center bg-light">
                                    <input type="number" class="form-control form-control-sm text-center total-days bg-light fw-bold" value="{{ $rec['total_days'] }}" readonly style="width: 90px; margin: 0 auto;">
                                </td>
                                
                                <td>
                                    <input type="number" step="0.5" min="0" max="{{ $rec['total_days'] }}" class="form-control form-control-sm text-center input-leave-taken" name="details[{{ $index }}][leave_taken]" value="{{ (isset($rec['leave_taken']) && $rec['leave_taken'] !== null && $rec['leave_taken'] !== '') ? ((float)$rec['leave_taken'] == (int)$rec['leave_taken'] ? (int)$rec['leave_taken'] : (float)$rec['leave_taken']) : '' }}" placeholder="Required">
                                </td>
                                
                                <td class="table-info text-center">
                                    <input type="number" step="0.5" class="form-control form-control-sm text-center input-net-present fw-bold text-primary bg-light" value="{{ (float)$rec['net_present'] == (int)$rec['net_present'] ? (int)$rec['net_present'] : (float)$rec['net_present'] }}" readonly style="width: 90px; margin: 0 auto;">
                                </td>

                                <td>
                                    <input type="number" step="0.5" min="0" class="form-control form-control-sm text-center input-leave-not-deducted" name="details[{{ $index }}][leave_not_deducted]" value="{{ (isset($rec['leave_not_deducted']) && $rec['leave_not_deducted'] !== null && $rec['leave_not_deducted'] !== '') ? ((float)$rec['leave_not_deducted'] == (int)$rec['leave_not_deducted'] ? (int)$rec['leave_not_deducted'] : (float)$rec['leave_not_deducted']) : '' }}" placeholder="Required">
                                </td>

                                <td class="table-success text-center">
                                    <input type="number" step="0.5" class="form-control form-control-sm text-center input-payable-days fw-bold text-success bg-light" value="{{ (float)$rec['payable_days'] == (int)$rec['payable_days'] ? (int)$rec['payable_days'] : (float)$rec['payable_days'] }}" readonly style="width: 90px; margin: 0 auto;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold text-body small me-1"><i class="bi bi-bar-chart-fill me-1"></i>Attendance Progress:</span>
                    <span class="badge bg-success px-2 py-1 fw-semibold" id="badge-completed">Completed: 0 Employees</span>
                    <span class="badge bg-danger px-2 py-1 fw-semibold" id="badge-pending">Pending: 0 Employees</span>
                    <span class="badge bg-secondary px-2 py-1 fw-semibold" id="badge-total">Total: 0 Employees</span>
                </div>
                <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="btn-save-attendance">
                    <i class="bi bi-check-circle me-1"></i> Save Monthly Attendance
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        function validateAndCalculateGrid() {
            let totalRows = $('.employee-row').length;
            let completedCount = 0;
            let pendingCount = 0;

            $('.employee-row').each(function() {
                let row = $(this);
                let totalDays = parseFloat(row.find('.total-days').val()) || 0;
                let leaveTakenInput = row.find('.input-leave-taken');
                let leaveNotDeductedInput = row.find('.input-leave-not-deducted');

                let leaveTakenVal = leaveTakenInput.val() !== undefined && leaveTakenInput.val() !== null ? leaveTakenInput.val().toString().trim() : '';
                let leaveNotDeductedVal = leaveNotDeductedInput.val() !== undefined && leaveNotDeductedInput.val() !== null ? leaveNotDeductedInput.val().toString().trim() : '';

                let isLeaveTakenBlank = (leaveTakenVal === '');
                let isLeaveNotDeductedBlank = (leaveNotDeductedVal === '');

                let leaveTaken = !isLeaveTakenBlank && !isNaN(leaveTakenVal) ? parseFloat(leaveTakenVal) : 0;
                let leaveNotDeducted = !isLeaveNotDeductedBlank && !isNaN(leaveNotDeductedVal) ? parseFloat(leaveNotDeductedVal) : 0;

                // 1. Calculations
                let netPresent = Math.max(0, totalDays - leaveTaken);
                let netPresentStr = (netPresent % 1 === 0) ? Math.round(netPresent) : parseFloat(netPresent.toFixed(2));
                row.find('.input-net-present').val(netPresentStr);

                let payableDays = netPresent + leaveNotDeducted;
                let payableDaysStr = (payableDays % 1 === 0) ? Math.round(payableDays) : parseFloat(payableDays.toFixed(2));
                row.find('.input-payable-days').val(payableDaysStr);

                // 2. Individual Input Validation
                let leaveTakenInvalid = isLeaveTakenBlank || leaveTaken < 0 || leaveTaken > totalDays;
                let leaveNotDeductedInvalid = isLeaveNotDeductedBlank || leaveNotDeducted < 0 || leaveNotDeducted > leaveTaken;

                if (leaveTakenInvalid) {
                    leaveTakenInput.addClass('is-invalid');
                } else {
                    leaveTakenInput.removeClass('is-invalid');
                }

                if (leaveNotDeductedInvalid) {
                    leaveNotDeductedInput.addClass('is-invalid');
                } else {
                    leaveNotDeductedInput.removeClass('is-invalid');
                }

                // 3. Row Highlighting & Counter Tracking
                if (leaveTakenInvalid || leaveNotDeductedInvalid) {
                    row.addClass('table-danger');
                    pendingCount++;
                } else {
                    row.removeClass('table-danger');
                    completedCount++;
                }
            });

            // 4. Update Summary Badges
            $('#badge-completed').text('Completed: ' + completedCount + ' Employees');
            $('#badge-pending').text('Pending: ' + pendingCount + ' Employees');
            $('#badge-total').text('Total: ' + totalRows + ' Employees');

            // 5. Button Protection
            let saveBtn = $('#btn-save-attendance');
            if (saveBtn.length) {
                if (pendingCount > 0) {
                    saveBtn.prop('disabled', true).addClass('disabled');
                } else {
                    saveBtn.prop('disabled', false).removeClass('disabled');
                }
            }

            return pendingCount === 0;
        }

        validateAndCalculateGrid();

        $(document).off('input blur change', '.input-leave-taken, .input-leave-not-deducted')
                   .on('input blur change', '.input-leave-taken, .input-leave-not-deducted', function() {
            validateAndCalculateGrid();
        });

        window.validateAttendanceGrid = validateAndCalculateGrid;
    })();
</script>
