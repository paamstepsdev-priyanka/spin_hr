@extends('layouts.admin')

@section('title', 'Attendance Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container--bootstrap-5 .select2-selection {
        font-size: 0.875rem;
        min-height: 31px;
        padding-top: 0.15rem;
        padding-bottom: 0.15rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3">
                <h4 class="mb-0 fw-bold text-body">Attendance Report</h4>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Report Filters</h5>
            </div>
            <div class="card-body p-3">
                <form id="attendance-report-form">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <!-- Company -->
                        <div class="col-md-2">
                            <label for="company_id" class="form-label small fw-semibold">Company <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback small" id="err-company_id">Company is required.</div>
                        </div>

                        <!-- Branch -->
                        <div class="col-md-2">
                            <label for="branch_id" class="form-label small fw-semibold">Branch <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                            </select>
                            <div class="invalid-feedback small" id="err-branch_id">Branch is required.</div>
                        </div>

                        <!-- Employee -->
                        <div class="col-md-3">
                            <label for="employee_id" class="form-label small fw-semibold">Employee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                            </select>
                            <div class="invalid-feedback small" id="err-employee_id">Employee is required.</div>
                        </div>

                        <!-- Month -->
                        <div class="col-md-2">
                            <label for="month" class="form-label small fw-semibold">Month <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="month" name="month" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                            <div class="invalid-feedback small" id="err-month">Month is required.</div>
                        </div>

                        <!-- Year -->
                        <div class="col-md-1">
                            <label for="year" class="form-label small fw-semibold">Year <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="year" name="year" required>
                                @for($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback small" id="err-year">Year is required.</div>
                        </div>

                        <!-- View Report Button -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold" id="btn-view-report">
                                <i class="bi bi-file-earmark-text me-1"></i> View Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="alert-container"></div>

        <!-- Generated Report Output Section -->
        <div id="report-output-container"></div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 on all filter selects
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Cascade 1: Company -> Load Branches
        $('#company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#branch_id');
            let empSelect = $('#employee_id');

            branchSelect.html('<option value="">Loading branches...</option>').trigger('change');
            empSelect.html('<option value="">Select Employee</option>').trigger('change');

            if (companyId) {
                let url = "{{ route('attendance-report.get-branches', ':company') }}".replace(':company', companyId);
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
                branchSelect.html('<option value="">Select Branch</option>').trigger('change');
            }
        });

        // Cascade 2: Branch -> Load Employees
        $('#branch_id').on('change', function() {
            let branchId = $(this).val();
            let empSelect = $('#employee_id');

            empSelect.html('<option value="">Loading employees...</option>').trigger('change');

            if (branchId) {
                let url = "{{ route('attendance-report.get-employees', ':branch') }}".replace(':branch', branchId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(employees) {
                        empSelect.html('<option value="">Select Employee</option>');
                        $.each(employees, function(key, emp) {
                            empSelect.append('<option value="' + emp.id + '">' + emp.employee_code + ' - ' + emp.name + '</option>');
                        });
                        empSelect.trigger('change');
                    },
                    error: function() {
                        empSelect.html('<option value="">Select Employee</option>').trigger('change');
                    }
                });
            } else {
                empSelect.html('<option value="">Select Employee</option>').trigger('change');
            }
        });

        // Submit Form Handler for View Report
        $('#attendance-report-form').on('submit', function(e) {
            e.preventDefault();
            $('.form-select').removeClass('is-invalid');
            $('#alert-container').html('');

            let form = $(this);
            let btn = $('#btn-view-report');
            let origHtml = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Generating...');

            $.ajax({
                url: "{{ route('attendance-report.report') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).html(origHtml);
                    if (response.status) {
                        $('#report-output-container').html(response.html);
                        $('html, body').animate({
                            scrollTop: $("#report-output-container").offset().top - 70
                        }, 300);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#err-' + field).text(messages[0]);
                        });
                    } else {
                        let errorMsg = 'Failed to generate report.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
