@extends('layouts.admin')

@section('title', 'Generate Monthly Payroll')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Single Compact Header & Branch Selector Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 fw-bold text-body">
                        Generate Monthly Payroll <span class="text-secondary fs-5 fw-normal me-2">•</span><span class="text-secondary fs-5 fw-semibold">{{ $monthName }} {{ $currentYear }}</span>
                    </h4>
                    <a href="{{ route('payroll-processing.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Payroll Processing
                    </a>
                </div>

                <form id="filter-payroll-form">
                    <input type="hidden" name="company_id" id="company_id" value="{{ $companyId }}">
                    <input type="hidden" name="month" id="month" value="{{ $currentMonth }}">
                    <input type="hidden" name="year" id="year" value="{{ $currentYear }}">

                    <div class="row">
                        <div class="col-md-5 col-lg-4">
                            <label for="branch_id" class="form-label small fw-semibold mb-1">Branch <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm select2" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (isset($selectedBranchId) && (int)$selectedBranchId === (int)$branch->id) ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="alert-container"></div>

        <!-- Payroll Table Container -->
        <div id="payroll-table-container" class="mb-4"></div>
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

        function loadBranchPayrollEmployees() {
            let branchId = $('#branch_id').val();
            let branchSelect = $('#branch_id');
            let container = $('#payroll-table-container');

            if (!branchId) {
                container.empty();
                $('#alert-container').empty();
                return;
            }

            let companyId = $('#company_id').val();
            let month = $('#month').val();
            let year = $('#year').val();

            branchSelect.prop('disabled', true);

            container.html(`
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="spinner-border text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="fw-semibold text-secondary">Loading payroll employees...</span>
                    </div>
                </div>
            `);

            $.ajax({
                url: "{{ route('payrolls.loadEmployees') }}",
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
                        $('#alert-container').empty();
                        container.html(response.html);
                        if (response.has_errors) {
                            $('#alert-container').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                    }
                },
                error: function(xhr) {
                    container.empty();
                    let errorMsg = 'Failed to load employees for payroll generation.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>${errorMsg}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                },
                complete: function() {
                    branchSelect.prop('disabled', false);
                }
            });
        }

        // Auto-load employees when Branch selection changes
        $('#branch_id').on('change', function() {
            loadBranchPayrollEmployees();
        });

        // Trigger on load if branch is pre-selected
        if ($('#branch_id').val()) {
            loadBranchPayrollEmployees();
        }

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
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    let errorMsg = 'An error occurred while generating payroll.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 70 }, 300);
                }
            });
        });
    });
</script>
@endpush
@endsection
