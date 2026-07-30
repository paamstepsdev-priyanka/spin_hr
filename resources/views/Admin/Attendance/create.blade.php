@extends('layouts.admin')

@section('title', 'Mark Monthly Attendance')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Mark Monthly Attendance</h4>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Attendance
                </a>
            </div>
        </div>

        <!-- Filter Block Card (Step 1) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Step 1: Select Attendance Details</h5>
            </div>
            <div class="card-body p-3">
                <form id="filter-attendance-form">
                    <div class="row g-3 align-items-end">
                        <!-- Company Dropdown -->
                        <div class="col-md-3">
                            <label for="company_id" class="form-label small fw-semibold">Company <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Branch Dropdown -->
                        <div class="col-md-3">
                            <label for="branch_id" class="form-label small fw-semibold">Branch <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Select -->
                        <div class="col-md-2">
                            <label for="month" class="form-label small fw-semibold">Month <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="month" name="month" required>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Select -->
                        <div class="col-md-2">
                            <label for="year" class="form-label small fw-semibold">Year <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="year" name="year" required>
                                @for($y = date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Load Employees Button -->
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary btn-sm w-100 fw-semibold" id="btn-load-employees">
                                <i class="bi bi-people-fill me-1"></i> Load Employees
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="alert-container"></div>

        <!-- Attendance Entry Table Container (Step 2) -->
        <div id="attendance-table-container" class="mb-4"></div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        let allBranches = @json($branches);

        // Future Month Validation Function
        function checkFutureMonthValidation() {
            let month = parseInt($('#month').val()) || 0;
            let year = parseInt($('#year').val()) || 0;
            let currentMonth = parseInt({{ $currentMonth }});
            let currentYear = parseInt({{ $currentYear }});

            if (year > currentYear || (year === currentYear && month > currentMonth)) {
                $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Future month attendance cannot be created.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                $('#btn-load-employees').prop('disabled', true).addClass('disabled');
                $('#btn-save-attendance').prop('disabled', true).addClass('disabled');
                return true;
            } else {
                if ($('#alert-container').text().includes('Future month attendance cannot be created.')) {
                    $('#alert-container').empty();
                }
                $('#btn-load-employees').prop('disabled', false).removeClass('disabled');
                $('#btn-save-attendance').prop('disabled', false).removeClass('disabled');
                return false;
            }
        }

        // Trigger future month validation on month/year dropdown change
        $('#month, #year').on('change', function() {
            checkFutureMonthValidation();
        });

        // Run validation on initial load
        checkFutureMonthValidation();

        // Dynamic Branch dropdown population on Company change
        $('#company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#branch_id');
            branchSelect.html('<option value="">Loading branches...</option>').trigger('change');

            if (companyId) {
                let url = "{{ route('companies.get-branches', ':company') }}".replace(':company', companyId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(branches) {
                        branchSelect.html('<option value="">Select Branch</option>');
                        $.each(branches, function(key, branch) {
                            branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                        });
                        branchSelect.trigger('change');
                    },
                    error: function() {
                        branchSelect.html('<option value="">Select Branch</option>').trigger('change');
                    }
                });
            } else {
                branchSelect.html('<option value="">Select Branch</option>');
                $.each(allBranches, function(key, branch) {
                    branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                });
                branchSelect.trigger('change');
            }
        });

        // Load Employees button click handler
        $('#btn-load-employees').on('click', function() {
            if (checkFutureMonthValidation()) {
                return false;
            }

            let companyId = $('#company_id').val();
            let branchId = $('#branch_id').val();
            let month = $('#month').val();
            let year = $('#year').val();

            if (!companyId) {
                alert('Please select company.');
                $('#company_id').focus();
                return;
            }
            if (!branchId) {
                alert('Please select branch.');
                $('#branch_id').focus();
                return;
            }
            if (!month) {
                alert('Please select month.');
                $('#month').focus();
                return;
            }
            if (!year) {
                alert('Please select year.');
                $('#year').focus();
                return;
            }

            let btn = $(this);
            let origHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...');

            $.ajax({
                url: "{{ route('attendance.loadEmployees') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    company_id: companyId,
                    branch_id: branchId,
                    month: month,
                    year: year
                },
                success: function(response) {
                    btn.prop('disabled', false).html(origHtml);
                    if (response.status) {
                        $('#attendance-table-container').html(response.html);
                        let alertType = response.is_edit ? 'alert-info' : 'alert-success';
                        $('#alert-container').html('<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('html, body').animate({
                            scrollTop: $("#attendance-table-container").offset().top - 70
                        }, 300);
                        checkFutureMonthValidation();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    let errorMsg = 'Failed to load employees.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            });
        });

        // Delegate Save Attendance Form submit
        $(document).on('submit', '#attendance-save-form', function(e) {
            e.preventDefault();
            if (checkFutureMonthValidation()) {
                return false;
            }

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

            let btn = form.find('#btn-save-attendance');
            let origHtml = btn.html();

            btn.prop('disabled', true).addClass('disabled').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                url: "{{ route('attendance.store') }}",
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
                    let errorMsg = 'Failed to save monthly attendance.';
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
