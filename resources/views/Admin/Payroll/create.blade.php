@extends('layouts.admin')

@section('title', 'Generate Monthly Payroll')

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
                <h4 class="mb-0 fw-bold text-body">Generate Monthly Payroll</h4>
                <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to History
                </a>
            </div>
        </div>

        <div id="alert-container"></div>

        <!-- Filter / Load Form Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Select Payroll Parameters</h5>
            </div>
            <div class="card-body p-3">
                <form id="form-load-payroll">
                    @csrf
                    <div class="row g-3 align-items-end">
                        @if($showCompanyFilter)
                        <!-- Company Dropdown -->
                        <div class="col-md-3">
                            <label for="company_id" class="form-label small fw-semibold">Company <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ (isset($selectedCompanyId) && $selectedCompanyId == $company->id) ? 'selected' : '' }}>{{ $company->name }}</option>
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
                                    <option value="{{ $branch->id }}" {{ (isset($selectedBranchId) && $selectedBranchId == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Select -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
                            <label for="month" class="form-label small fw-semibold">Month <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="month" name="month" required>
                                <option value="">Select Month</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Select -->
                        <div class="{{ $showCompanyFilter ? 'col-md-2' : 'col-md-3' }}">
                            <label for="year" class="form-label small fw-semibold">Year <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="year" name="year" required>
                                <option value="">Select Year</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
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

        <!-- Payroll Table Container -->
        <div id="payroll-table-container"></div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        let allBranches = @json($branches);

        // Filter branches based on selected company
        $('#company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#branch_id');
            branchSelect.html('<option value="">Loading branches...</option>').trigger('change.select2');

            if (companyId) {
                let url = "{{ route('companies.get-branches', ':company') }}".replace(':company', companyId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(branches) {
                        branchSelect.html('<option value="">Select Branch</option>');
                        $.each(branches, function(key, branch) {
                            let selected = (branch.id == "{{ $selectedBranchId ?? '' }}") ? 'selected' : '';
                            branchSelect.append('<option value="' + branch.id + '" ' + selected + '>' + branch.name + '</option>');
                        });
                        branchSelect.trigger('change.select2');
                    },
                    error: function() {
                        branchSelect.html('<option value="">Select Branch</option>').trigger('change.select2');
                    }
                });
            } else {
                branchSelect.html('<option value="">Select Branch</option>');
                $.each(allBranches, function(key, branch) {
                    branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                });
                branchSelect.trigger('change.select2');
            }
        });

        // Auto-load employees if redirected from Attendance module
        @if(!empty($selectedCompanyId) && !empty($selectedBranchId))
            $('#btn-load-employees').click();
        @endif

        // Load Employees for Payroll Generation
        $('#btn-load-employees').on('click', function() {
            let form = $('#form-load-payroll');
            let companyId = $('#company_id').val();
            let branchId = $('#branch_id').val();
            let month = $('#month').val();
            let year = $('#year').val();

            if (!companyId || !branchId || !month || !year) {
                $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Please select Company, Branch, Month, and Year.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                return;
            }

            let btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...');

            $.ajax({
                url: "{{ route('payrolls.loadEmployees') }}",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    company_id: companyId,
                    branch_id: branchId,
                    month: month,
                    year: year
                },
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="bi bi-people-fill me-1"></i> Load Employees');
                    if (response.status) {
                        $('#alert-container').empty();
                        $('#payroll-table-container').html(response.html);
                        if (response.has_errors) {
                            $('#alert-container').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-people-fill me-1"></i> Load Employees');
                    $('#payroll-table-container').empty();
                    let errorMsg = 'Failed to load employees.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            });
        });

        // Submit Generate Payroll Form via AJAX
        $(document).on('submit', '#form-generate-payroll', function(e) {
            e.preventDefault();

            let form = $(this);
            let btn = form.find('#btn-generate-payroll');
            let origHtml = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Generating Payroll...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    let errorMsg = 'An error occurred while generating payroll.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    });
</script>
@endpush
@section('endscript')
@endsection
@endsection
