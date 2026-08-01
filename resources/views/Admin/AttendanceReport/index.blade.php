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
        <!-- Single Unified Card -->
        <div class="card border-0 shadow-sm mb-4">
            <!-- Card Header: Title -->
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h4 class="mb-0 fw-bold text-body">Attendance Report</h4>
            </div>

            <!-- Card Body: Report Filters & Output -->
            <div class="card-body p-3">
                <form id="attendance-report-form" class="bg-body-tertiary p-3 rounded mb-3 border border-light-subtle">
                    @csrf
                    <div class="row g-3 align-items-end">
                        @if($showCompanyFilter)
                        <!-- Company (Required) -->
                        <div class="col-md-3">
                            <label for="company_id" class="form-label small fw-semibold">Company <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback small" id="err-company_id">Company is required.</div>
                        </div>
                        @else
                            <input type="hidden" name="company_id" id="company_id" value="{{ $currentCompanyId }}">
                        @endif

                        <!-- Branch (Optional) -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
                            <label for="branch_id" class="form-label small fw-semibold">Branch</label>
                            <select class="form-select form-select-sm select2" id="branch_id" name="branch_id">
                                <option value="">All Branches</option>
                            </select>
                            <div class="invalid-feedback small" id="err-branch_id"></div>
                        </div>

                        <!-- Employee (Optional) -->
                        <div class="{{ $showCompanyFilter ? 'col-md-3' : 'col-md-3' }}">
                            <label for="employee_id" class="form-label small fw-semibold">Employee</label>
                            <select class="form-select form-select-sm select2" id="employee_id" name="employee_id">
                                <option value="">All Employees</option>
                            </select>
                            <div class="invalid-feedback small" id="err-employee_id"></div>
                        </div>

                        <!-- Month (Required) -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
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

                        <!-- Year (Required) -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
                            <label for="year" class="form-label small fw-semibold">Year <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="year" name="year" required>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback small" id="err-year">Year is required.</div>
                        </div>

                        <!-- View Report Button -->
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btn-view-report">
                                <i class="bi bi-file-earmark-text me-1"></i> View Report
                            </button>
                        </div>
                    </div>
                </form>

                <div id="alert-container"></div>

                <!-- Generated Report Output Section -->
                <div id="report-output-container"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 on filter selects
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Load Employees helper
        function loadEmployees(companyId, branchId) {
            let empSelect = $('#employee_id');
            empSelect.html('<option value="">Loading employees...</option>').trigger('change');

            if (companyId) {
                $.ajax({
                    url: "{{ route('attendance-report.get-employees') }}",
                    type: 'GET',
                    data: {
                        company_id: companyId,
                        branch_id: branchId || ''
                    },
                    success: function(employees) {
                        empSelect.html('<option value="">All Employees</option>');
                        $.each(employees, function(key, emp) {
                            empSelect.append('<option value="' + emp.id + '">' + emp.employee_code + ' - ' + emp.name + '</option>');
                        });
                        empSelect.trigger('change');
                    },
                    error: function() {
                        empSelect.html('<option value="">All Employees</option>').trigger('change');
                    }
                });
            } else {
                empSelect.html('<option value="">All Employees</option>').trigger('change');
            }
        }

        // Cascade 1: Company -> Load Branches & Employees
        $('#company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#branch_id');

            branchSelect.html('<option value="">Loading branches...</option>').trigger('change');

            if (companyId) {
                let url = "{{ route('attendance-report.get-branches', ':company') }}".replace(':company', companyId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(branches) {
                        branchSelect.html('<option value="">All Branches</option>');
                        $.each(branches, function(key, branch) {
                            branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                        });
                        branchSelect.trigger('change');
                    },
                    error: function() {
                        branchSelect.html('<option value="">All Branches</option>').trigger('change');
                    }
                });

                loadEmployees(companyId, '');
            } else {
                branchSelect.html('<option value="">All Branches</option>').trigger('change');
                loadEmployees('', '');
            }
        });

        // Cascade 2: Branch -> Load Branch-Specific Employees
        $('#branch_id').on('change', function() {
            let companyId = $('#company_id').val();
            let branchId = $(this).val();
            if (companyId) {
                loadEmployees(companyId, branchId);
            }
        });

        // Trigger initial company load if pre-selected
        if ($('#company_id').val()) {
            $('#company_id').trigger('change');
        }

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
