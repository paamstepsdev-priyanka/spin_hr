@extends('layouts.admin')

@section('title', 'Employees')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Single Unified Card -->
        <div class="card border-0 shadow-sm mb-4">
            <!-- Card Header: Title & Action Button -->
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Employees</h4>
                <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Employee
                </a>
            </div>

            <!-- Card Body: Filters + Table -->
            <div class="card-body p-3">
                <div id="alert-container"></div>

                <!-- Filter Section -->
                <form id="employee-filter-form" class="bg-body-tertiary p-3 rounded mb-3 border border-light-subtle">
                    <div class="row g-3 align-items-end">
                        @if($showCompanyFilter)
                        <!-- Company Filter -->
                        <div class="col-md-3">
                            <label for="filter_company_id" class="form-label small fw-semibold">Company</label>
                            <select class="form-select form-select-sm" id="filter_company_id">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" id="filter_company_id" value="{{ $currentCompanyId }}">
                        @endif

                        <!-- Branch Filter -->
                        <div class="{{ $showCompanyFilter ? 'col-md-3' : 'col-md-4' }}">
                            <label for="filter_branch_id" class="form-label small fw-semibold">Branch</label>
                            <select class="form-select form-select-sm" id="filter_branch_id">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee Type Filter -->
                        <div class="{{ $showCompanyFilter ? 'col-md-3' : 'col-md-4' }}">
                            <label for="filter_employment_type" class="form-label small fw-semibold">Employee Type</label>
                            <select class="form-select form-select-sm" id="filter_employment_type">
                                <option value="">All Employee Types</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Consultant">Consultant</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="{{ $showCompanyFilter ? 'col-md-3' : 'col-md-4' }}">
                            <label for="filter_status" class="form-label small fw-semibold">Status</label>
                            <select class="form-select form-select-sm" id="filter_status">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Toolbar: Export / Print Buttons -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Print</button>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="employees-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 30px;">#</th>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">View</th>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">Edit</th>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">Delete</th>
                                <th scope="col" class="fw-bold text-center" style="width: 70px;">Status</th>
                                <th scope="col" class="fw-bold text-center" style="width: 90px;">Salary</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Code</th>
                                <th scope="col" class="fw-bold text-start" style="width: 130px;">Name</th>
                                <th scope="col" class="fw-bold text-start">Company</th>
                                <th scope="col" class="fw-bold text-start">Branch</th>
                                <th scope="col" class="fw-bold text-start">Department</th>
                                <th scope="col" class="fw-bold text-start">Designation</th>
                                <th scope="col" class="fw-bold text-center" style="width: 90px;">Type</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Mobile</th>
                                <th scope="col" class="fw-bold text-start">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Employee Status Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form id="status-update-form">
                @csrf
                <input type="hidden" id="status_employee_url">

                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="statusUpdateModalLabel">
                        <i class="bi bi-person-gear me-2"></i>Update Employee Status
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3 text-body">Updating status for <strong id="status-employee-name" class="text-primary"></strong></p>
                    
                    <div id="status-modal-alert"></div>

                    <!-- Status Selection -->
                    <div class="mb-3">
                        <label for="modal_employee_status" class="form-label fw-semibold text-body">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="modal_employee_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Inactive / Leave Details Container (Shown when Inactive is selected) -->
                    <div id="inactive-details-container" style="display: none;">
                        <div class="mb-3">
                            <label for="modal_leave_date" class="form-label fw-semibold text-body">Effective Leave Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="modal_leave_date" name="leave_date">
                            <div class="invalid-feedback" id="leave-date-error">Effective Leave Date is required when status is set to Inactive.</div>
                        </div>

                        <div class="mb-3">
                            <label for="modal_disable_reason" class="form-label fw-semibold text-body">Remark / Reason <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea class="form-control" id="modal_disable_reason" name="disable_reason" rows="3" placeholder="Enter remark or reason..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-body-tertiary border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btn-save-status">
                        Save Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        let allBranches = @json($branches);

        let table = $('#employees-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('employees.index') }}",
                data: function(d) {
                    d.company_id = $('#filter_company_id').val();
                    d.branch_id = $('#filter_branch_id').val();
                    d.employment_type = $('#filter_employment_type').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'view', name: 'view', orderable: false, searchable: false, className: 'text-center' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center' },
                { data: 'delete', name: 'delete', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'salary', name: 'salary', orderable: false, searchable: false, className: 'text-center' },
                { data: 'employee_code', name: 'employee_code', className: 'text-center fw-bold text-body' },   
                { data: 'name', name: 'name', className: 'fw-semibold' },
                { data: 'company_name', name: 'company.name' },
                { data: 'branch_name', name: 'branch.name' },
                { data: 'department_name', name: 'department.name' },
                { data: 'designation', name: 'designation' },
                { data: 'employment_type', name: 'employment_type', className: 'text-center' },
                { data: 'mobile', name: 'mobile', className: 'text-center' },
                { data: 'email', name: 'email' },
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // Dynamic Branch filtering on Company selection
        $('#filter_company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#filter_branch_id');
            branchSelect.html('<option value="">Loading branches...</option>');

            if (companyId) {
                let url = "{{ route('companies.get-branches', ':company') }}".replace(':company', companyId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(branches) {
                        branchSelect.html('<option value="">All Branches</option>');
                        $.each(branches, function(key, branch) {
                            branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                        });
                        table.draw();
                    },
                    error: function() {
                        branchSelect.html('<option value="">All Branches</option>');
                        table.draw();
                    }
                });
            } else {
                branchSelect.html('<option value="">All Branches</option>');
                $.each(allBranches, function(key, branch) {
                    branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                });
                table.draw();
            }
        });

        // Trigger table redraw when any filter dropdown changes
        $('#filter_branch_id, #filter_employment_type, #filter_status').on('change', function() {
            table.draw();
        });

        // Reset filters
        $('#btn-reset-filter').on('click', function() {
            $('#filter_company_id').val('');
            $('#filter_employment_type').val('');
            $('#filter_status').val('');

            let branchSelect = $('#filter_branch_id');
            branchSelect.html('<option value="">All Branches</option>');
            $.each(allBranches, function(key, branch) {
                branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
            });

            table.draw();
        });

        // Open Status Update Modal when clicking status badge
        $(document).on('click', '.btn-status-modal', function(e) {
            let $btn = $(this);
            let url = $btn.attr('data-url') || $btn.data('url');
            let name = $btn.attr('data-name') || $btn.data('name');
            let currentStatus = $btn.attr('data-status') || $btn.data('status') || 'active';
            let leaveDate = $btn.attr('data-leave-date') || $btn.data('leave-date') || '';
            let reason = $btn.attr('data-reason') || $btn.data('reason') || '';

            $('#status_employee_url').val(url);
            $('#status-employee-name').text(name);
            $('#modal_employee_status').val(currentStatus);
            $('#modal_leave_date').val(leaveDate).removeClass('is-invalid');
            $('#modal_disable_reason').val(reason);
            $('#status-modal-alert').html('');

            toggleLeaveFields(currentStatus);

            let modalEl = document.getElementById('statusUpdateModal');
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                let modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.show();
            } else if ($.fn.modal) {
                $('#statusUpdateModal').modal('show');
            }
        });

        // Toggle Leave Date & Remark fields on status selection change
        $('#modal_employee_status').on('change', function() {
            toggleLeaveFields($(this).val());
        });

        function toggleLeaveFields(statusVal) {
            if (statusVal === 'inactive' || statusVal === 'disabled') {
                $('#inactive-details-container').slideDown(200);
                $('#modal_leave_date').prop('required', true);
            } else {
                $('#inactive-details-container').slideUp(200);
                $('#modal_leave_date').prop('required', false).removeClass('is-invalid');
            }
        }

        // Submit Status Update Form via AJAX
        $('#status-update-form').on('submit', function(e) {
            e.preventDefault();

            let statusVal = $('#modal_employee_status').val();
            let leaveDate = $('#modal_leave_date').val().trim();

            if ((statusVal === 'inactive' || statusVal === 'disabled') && !leaveDate) {
                $('#modal_leave_date').addClass('is-invalid');
                return false;
            } else {
                $('#modal_leave_date').removeClass('is-invalid');
            }

            let url = $('#status_employee_url').val();
            let disableReason = $('#modal_disable_reason').val().trim();
            let $btn = $('#btn-save-status');

            window.showButtonLoader($btn, 'Saving...');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: statusVal,
                    leave_date: leaveDate,
                    disable_reason: disableReason
                },
                success: function(response) {
                    window.resetButtonLoader($btn);
                    if (response.status) {
                        let modalEl = document.getElementById('statusUpdateModal');
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            let modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();
                        } else if ($.fn.modal) {
                            $('#statusUpdateModal').modal('hide');
                        }
                        
                        $('#alert-container').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill me-2"></i>' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        table.ajax.reload(null, false);
                    } else {
                        $('#status-modal-alert').html('<div class="alert alert-danger" role="alert">' + (response.message || 'Failed to update status.') + '</div>');
                    }
                },
                error: function(xhr) {
                    window.resetButtonLoader($btn);
                    let errMessage = 'An error occurred while updating status.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.leave_date) {
                        errMessage = xhr.responseJSON.errors.leave_date[0];
                        $('#modal_leave_date').addClass('is-invalid');
                        $('#leave-date-error').text(errMessage);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMessage = xhr.responseJSON.message;
                    }
                    $('#status-modal-alert').html('<div class="alert alert-danger mb-3" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + errMessage + '</div>');
                }
            });
        });

        // Handle AJAX Delete
        $(document).on('click', '.btn-delete', function() {
            let deleteUrl = $(this).data('url');
            if (confirm('Are you sure you want to delete this employee? This will also remove the login user account.')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#alert-container').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to delete employee.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
