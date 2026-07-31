@extends('layouts.admin')

@section('title', 'Edit Monthly Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Edit Monthly Attendance</h4>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Attendance
                </a>
            </div>
        </div>

        <div id="alert-container"></div>

        <!-- Master Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Attendance Information</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Company</label>
                        <div class="fw-bold text-body fs-6">{{ $attendanceMonth->company->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Branch</label>
                        <div class="fw-bold text-body fs-6">{{ $attendanceMonth->branch->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Month & Year</label>
                        <div class="fw-bold text-body fs-6">{{ $monthName }} {{ $attendanceMonth->year }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                        <div><span class="badge bg-success px-2 py-1">{{ ucfirst($attendanceMonth->status) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Edit Table Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-body">Employee Attendance Records</h5>
            </div>
            <div class="card-body p-3">
                <form id="attendance-update-form">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle small mb-0 w-100 text-nowrap" id="employee-attendance-edit-grid">
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
                                            <input type="number" step="0.5" min="0" max="{{ $rec['total_days'] }}" class="form-control form-control-sm text-center input-leave-taken" name="details[{{ $index }}][leave_taken]" value="{{ (isset($rec['leave_taken']) && $rec['leave_taken'] !== null && (float)$rec['leave_taken'] > 0) ? ((float)$rec['leave_taken'] == (int)$rec['leave_taken'] ? (int)$rec['leave_taken'] : (float)$rec['leave_taken']) : '' }}">
                                        </td>
                                        
                                        <td class="table-info text-center">
                                            <input type="number" step="0.5" class="form-control form-control-sm text-center input-net-present fw-bold text-primary bg-light" value="{{ (float)$rec['net_present'] == (int)$rec['net_present'] ? (int)$rec['net_present'] : (float)$rec['net_present'] }}" readonly style="width: 90px; margin: 0 auto;">
                                        </td>

                                        <td>
                                            <input type="number" step="0.5" min="0" class="form-control form-control-sm text-center input-leave-not-deducted" name="details[{{ $index }}][leave_not_deducted]" value="{{ (isset($rec['leave_not_deducted']) && $rec['leave_not_deducted'] !== null && (float)$rec['leave_not_deducted'] > 0) ? ((float)$rec['leave_not_deducted'] == (int)$rec['leave_not_deducted'] ? (int)$rec['leave_not_deducted'] : (float)$rec['leave_not_deducted']) : '' }}">
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
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="btn-update-attendance">
                            <i class="bi bi-check-circle me-1"></i> Update Monthly Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        function calculateRow(row) {
            let totalDays = parseFloat(row.find('.total-days').val()) || 0;
            let leaveTakenVal = row.find('.input-leave-taken').val();
            let leaveNotDeductedVal = row.find('.input-leave-not-deducted').val();

            let leaveTaken = (leaveTakenVal !== '' && !isNaN(leaveTakenVal)) ? parseFloat(leaveTakenVal) : 0;
            let leaveNotDeducted = (leaveNotDeductedVal !== '' && !isNaN(leaveNotDeductedVal)) ? parseFloat(leaveNotDeductedVal) : 0;

            let netPresent = Math.max(0, totalDays - leaveTaken);
            let netPresentStr = (netPresent % 1 === 0) ? Math.round(netPresent) : parseFloat(netPresent.toFixed(2));
            row.find('.input-net-present').val(netPresentStr);

            let payableDays = netPresent + leaveNotDeducted;
            let payableDaysStr = (payableDays % 1 === 0) ? Math.round(payableDays) : parseFloat(payableDays.toFixed(2));
            row.find('.input-payable-days').val(payableDaysStr);

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

        // Form Submit Handler
        $('#attendance-update-form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);

            let hasValidationError = false;
            let validationErrorMessage = '';

            $('.employee-row').each(function() {
                let row = $(this);
                let empName = row.data('emp-name');
                let totalDays = parseFloat(row.find('.total-days').val()) || 0;
                let leaveTakenVal = row.find('.input-leave-taken').val();
                let leaveNotDeductedVal = row.find('.input-leave-not-deducted').val();

                let leaveTaken = (leaveTakenVal !== '' && !isNaN(leaveTakenVal)) ? parseFloat(leaveTakenVal) : 0;
                let leaveNotDeducted = (leaveNotDeductedVal !== '' && !isNaN(leaveNotDeductedVal)) ? parseFloat(leaveNotDeductedVal) : 0;

                if (leaveTaken < 0) {
                    hasValidationError = true;
                    validationErrorMessage = 'Leave Taken for ' + empName + ' cannot be negative.';
                    row.addClass('table-danger');
                    return false;
                }
                if (leaveTaken > totalDays) {
                    hasValidationError = true;
                    validationErrorMessage = 'Leave Taken for ' + empName + ' (' + leaveTaken + ' days) cannot be greater than No. of Days in Month (' + totalDays + ').';
                    row.addClass('table-danger');
                    return false;
                }
                if (leaveNotDeducted < 0) {
                    hasValidationError = true;
                    validationErrorMessage = 'Leave Not Deducted for ' + empName + ' cannot be negative.';
                    row.addClass('table-danger');
                    return false;
                }
                if (leaveNotDeducted > leaveTaken) {
                    hasValidationError = true;
                    validationErrorMessage = 'Leave Not Deducted for ' + empName + ' (' + leaveNotDeducted + ' days) cannot be greater than Leave Taken (' + leaveTaken + ' days).';
                    row.addClass('table-danger');
                    return false;
                }
                row.removeClass('table-danger');
            });

            if (hasValidationError) {
                $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + validationErrorMessage + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 70 }, 300);
                return false;
            }

            let btn = form.find('#btn-update-attendance');
            let origHtml = btn.html();

            btn.prop('disabled', true).addClass('disabled').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...');

            $.ajax({
                url: "{{ route('attendance.update', $attendanceMonth->id) }}",
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).removeClass('disabled').html(origHtml);
                    if (response.status) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).removeClass('disabled').html(origHtml);
                    let errorMsg = 'Failed to update monthly attendance.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 70 }, 300);
                }
            });
        });
    });
</script>
@endpush
@endsection
