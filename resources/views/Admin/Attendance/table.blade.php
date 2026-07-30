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
                                <td class="text-muted text-start">{{ $rec['branch_name'] }}</td>
                                
                                <td class="text-center bg-light">
                                    <input type="number" class="form-control form-control-sm text-center total-days bg-light fw-bold" value="{{ $rec['total_days'] }}" readonly style="width: 90px; margin: 0 auto;">
                                </td>
                                
                                <td>
                                    <input type="number" step="0.5" min="0" max="{{ $rec['total_days'] }}" class="form-control form-control-sm text-center input-leave-taken" name="details[{{ $index }}][leave_taken]" placeholder="0" value="{{ !empty($rec['leave_taken']) && $rec['leave_taken'] > 0 ? ((float)$rec['leave_taken'] == (int)$rec['leave_taken'] ? (int)$rec['leave_taken'] : (float)$rec['leave_taken']) : '' }}">
                                </td>
                                
                                <td class="table-info text-center">
                                    <input type="number" step="0.5" class="form-control form-control-sm text-center input-net-present fw-bold text-primary bg-light" value="{{ (float)$rec['net_present'] == (int)$rec['net_present'] ? (int)$rec['net_present'] : (float)$rec['net_present'] }}" readonly style="width: 90px; margin: 0 auto;">
                                </td>

                                <td>
                                    <input type="number" step="0.5" min="0" class="form-control form-control-sm text-center input-leave-not-deducted" name="details[{{ $index }}][leave_not_deducted]" placeholder="0" value="{{ !empty($rec['leave_not_deducted']) && $rec['leave_not_deducted'] > 0 ? ((float)$rec['leave_not_deducted'] == (int)$rec['leave_not_deducted'] ? (int)$rec['leave_not_deducted'] : (float)$rec['leave_not_deducted']) : '' }}">
                                </td>

                                <td class="table-success text-center">
                                    <input type="number" step="0.5" class="form-control form-control-sm text-center input-payable-days fw-bold text-success bg-light" value="{{ (float)$rec['payable_days'] == (int)$rec['payable_days'] ? (int)$rec['payable_days'] : (float)$rec['payable_days'] }}" readonly style="width: 90px; margin: 0 auto;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                <div class="small text-muted">
                    <span class="badge bg-light text-dark border me-2">Formulas:</span>
                    <strong>Net Present</strong> = No. of Days in Month - Leave Taken | 
                    <strong>No. of Days Payable</strong> = Net Present + Leave Not Deducted
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
        // Instant real-time calculation & client validation
        function calculateRow(row) {
            let totalDays = parseFloat(row.find('.total-days').val()) || 0;
            let leaveTakenVal = row.find('.input-leave-taken').val();
            let leaveNotDeductedVal = row.find('.input-leave-not-deducted').val();

            let leaveTaken = (leaveTakenVal !== '' && !isNaN(leaveTakenVal)) ? parseFloat(leaveTakenVal) : 0;
            let leaveNotDeducted = (leaveNotDeductedVal !== '' && !isNaN(leaveNotDeductedVal)) ? parseFloat(leaveNotDeductedVal) : 0;

            // 1. Net Present = No. of Days in Month - Leave Taken
            let netPresent = Math.max(0, totalDays - leaveTaken);
            let netPresentStr = (netPresent % 1 === 0) ? Math.round(netPresent) : parseFloat(netPresent.toFixed(2));
            row.find('.input-net-present').val(netPresentStr);

            // 2. No. of Days Payable = Net Present + Leave Not Deducted
            let payableDays = netPresent + leaveNotDeducted;
            let payableDaysStr = (payableDays % 1 === 0) ? Math.round(payableDays) : parseFloat(payableDays.toFixed(2));
            row.find('.input-payable-days').val(payableDaysStr);

            // Client Validation Checks:
            if (leaveTaken < 0 || leaveTaken > totalDays || leaveNotDeducted < 0 || leaveNotDeducted > leaveTaken) {
                row.addClass('table-danger');
            } else {
                row.removeClass('table-danger');
            }
        }

        $('.employee-row').each(function() {
            calculateRow($(this));
        });

        $(document).on('input change', '.input-leave-taken, .input-leave-not-deducted', function() {
            let row = $(this).closest('.employee-row');
            calculateRow(row);
        });
    })();
</script>
