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
                        @if($showCompanyFilter)
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
                        @else
                            <input type="hidden" name="company_id" id="company_id" value="{{ $currentCompanyId }}">
                        @endif

                        <!-- Branch Dropdown -->
                        <div class="{{ $showCompanyFilter ? 'col-md-3' : 'col-md-4' }}">
                            <label for="branch_id" class="form-label small fw-semibold">Branch <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Select -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
                            <label for="month" class="form-label small fw-semibold">Month <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="month" name="month" required>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Select -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
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
        $('#btn-load-employees').on('click', function(e) {
            e.preventDefault();
            let btn = $(this);

            if (btn.data('is-loading')) {
                return false;
            }

            try {
                if (checkFutureMonthValidation()) {
                    window.resetButtonLoader(btn);
                    window.hideGlobalLoader();
                    return false;
                }

                let companyId = $('#company_id').val();
                let branchId = $('#branch_id').val();
                let month = $('#month').val();
                let year = $('#year').val();

                if (!companyId) {
                    alert('Please select company.');
                    $('#company_id').focus();
                    window.resetButtonLoader(btn);
                    window.hideGlobalLoader();
                    return;
                }
                if (!branchId) {
                    alert('Please select branch.');
                    $('#branch_id').focus();
                    window.resetButtonLoader(btn);
                    window.hideGlobalLoader();
                    return;
                }
                if (!month) {
                    alert('Please select month.');
                    $('#month').focus();
                    window.resetButtonLoader(btn);
                    window.hideGlobalLoader();
                    return;
                }
                if (!year) {
                    alert('Please select year.');
                    $('#year').focus();
                    window.resetButtonLoader(btn);
                    window.hideGlobalLoader();
                    return;
                }

                window.showButtonLoader(btn, 'Loading...');

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
                        let errorMsg = 'Failed to load employees.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    },
                    complete: function() {
                        window.resetButtonLoader(btn);
                        window.hideGlobalLoader();
                    }
                });
            } catch (err) {
                console.error(err);
                window.resetButtonLoader(btn);
                window.hideGlobalLoader();
            }
        });

        // Delegate Save Attendance Form submit
        $(document).on('submit', '#attendance-save-form', function(e) {
            e.preventDefault();
            let form = $(this);

            if (checkFutureMonthValidation()) {
                return false;
            }

            if (typeof window.validateAttendanceGrid === 'function') {
                let isValid = window.validateAttendanceGrid();
                if (!isValid) {
                    let firstInvalidRow = $('.employee-row.table-danger').first();
                    let firstInvalidInput = firstInvalidRow.find('.is-invalid').first();

                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>Attendance cannot be saved. Please complete attendance for all highlighted employees before continuing.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                    if (firstInvalidRow.length) {
                        $('html, body').animate({
                            scrollTop: firstInvalidRow.offset().top - 100
                        }, 300, function() {
                            if (firstInvalidInput.length) {
                                firstInvalidInput.focus();
                            }
                        });
                    }
                    return false;
                }
            }

            $.ajax({
                url: "{{ route('attendance.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to save monthly attendance.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 70 }, 300);
                    if (typeof window.validateAttendanceGrid === 'function') {
                        window.validateAttendanceGrid();
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
