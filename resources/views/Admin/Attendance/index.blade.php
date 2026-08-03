@extends('layouts.admin')

@section('title', 'Monthly Attendance')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Single Compact Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 fw-bold text-body">
                        Monthly Attendance <span class="text-secondary fs-5 fw-normal me-2">•</span><span class="text-secondary fs-5 fw-semibold">{{ $monthName }} {{ $currentYear }}</span>
                    </h4>
                    <a href="{{ route('payroll-processing.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Payroll Processing
                    </a>
                </div>

                <form id="filter-attendance-form">
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

        <!-- Attendance Entry Table Container -->
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

        function loadBranchEmployees() {
            let branchId = $('#branch_id').val();
            let branchSelect = $('#branch_id');
            let container = $('#attendance-table-container');

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
                        <span class="fw-semibold text-secondary">Loading employee list...</span>
                    </div>
                </div>
            `);

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
                        container.html(response.html);
                        let alertType = response.is_edit ? 'alert-info' : 'alert-success';
                        $('#alert-container').html('<div class="alert ' + alertType + ' alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to load employees.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    container.html(`
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
            loadBranchEmployees();
        });

        // Trigger on load if branch is pre-selected
        if ($('#branch_id').val()) {
            loadBranchEmployees();
        }

        // Delegate Save / Update Attendance Form submit
        $(document).on('submit', '#attendance-save-form', function(e) {
            e.preventDefault();
            let form = $(this);

            let unsetEmployees = [];
            $('.employee-row[data-has-salary="false"]').each(function() {
                unsetEmployees.push($(this).attr('data-emp-name'));
            });

            if (unsetEmployees.length > 0) {
                let errorMsg = 'Cannot save attendance. Salary is not set for the following employee(s): <strong>' + unsetEmployees.join(', ') + '</strong>. Please click <span class="badge bg-danger">Set Salary</span> to configure salary in a new tab first.';
                $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 70 }, 300);
                return false;
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
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('html, body').animate({ scrollTop: $('#alert-container').offset().top - 70 }, 300);
                }
            });
        });
    });
</script>
@endpush
@endsection
