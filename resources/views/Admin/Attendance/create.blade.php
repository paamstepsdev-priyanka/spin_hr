@extends('layouts.admin')

@section('title', 'Mark Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Mark Attendance</h4>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Attendance
                </a>
            </div>
        </div>

        <!-- Filter Block Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3">
                <h5 class="mb-0 fw-bold text-body">Attendance Details</h5>
            </div>
            <div class="card-body p-3">
                <form id="filter-attendance-form">
                    <div class="row g-3 align-items-end">
                        <!-- Company Dropdown -->
                        <div class="col-md-3">
                            <label for="company_id" class="form-label small fw-semibold">Company <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Branch Dropdown -->
                        <div class="col-md-3">
                            <label for="branch_id" class="form-label small fw-semibold">Branch <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Picker -->
                        <div class="col-md-3">
                            <label for="attendance_date" class="form-label small fw-semibold">Attendance Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="attendance_date" name="attendance_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Load Employees Button -->
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary btn-sm w-100 fw-semibold" id="btn-load-employees">
                                <i class="bi bi-people-fill me-1"></i> Load Employees
                            </button>
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
<script>
    $(document).ready(function() {
        let allBranches = @json($branches);

        // Dynamic Branch dropdown population on Company change
        $('#company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#branch_id');
            branchSelect.html('<option value="">Loading branches...</option>');

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
                    },
                    error: function() {
                        branchSelect.html('<option value="">Select Branch</option>');
                    }
                });
            } else {
                branchSelect.html('<option value="">Select Branch</option>');
                $.each(allBranches, function(key, branch) {
                    branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                });
            }
        });

        // Load Employees button click handler
        $('#btn-load-employees').on('click', function() {
            let companyId = $('#company_id').val();
            let branchId = $('#branch_id').val();
            let attendanceDate = $('#attendance_date').val();

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
            if (!attendanceDate) {
                alert('Please select attendance date.');
                $('#attendance_date').focus();
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
                    attendance_date: attendanceDate
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
            let form = $(this);
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
                    let errorMsg = 'Failed to save attendance.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            });
        });
    });
</script>
@endpush
@endsection
