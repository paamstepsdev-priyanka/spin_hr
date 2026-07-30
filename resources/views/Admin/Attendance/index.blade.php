@extends('layouts.admin')

@section('title', 'Monthly Attendance')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Single Unified Card -->
        <div class="card border-0 shadow-sm mb-4">
            <!-- Card Header: Title & Action Button -->
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Monthly Attendance Management</h4>
                <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg me-1"></i>
                    Mark Monthly Attendance
                </a>
            </div>

            <!-- Card Body: Filters + Attendance Data Table -->
            <div class="card-body p-3">
                <!-- Filter Section -->
                <form id="attendance-filter-form" class="bg-body-tertiary p-3 rounded mb-3 border border-light-subtle">
                    <div class="row g-3 align-items-end">
                        <!-- Company Dropdown -->
                        <div class="col-md-3">
                            <label for="filter_company_id" class="form-label small fw-semibold">Company</label>
                            <select class="form-select form-select-sm select2" id="filter_company_id" name="company_id">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Branch Dropdown -->
                        <div class="col-md-3">
                            <label for="filter_branch_id" class="form-label small fw-semibold">Branch</label>
                            <select class="form-select form-select-sm select2" id="filter_branch_id" name="branch_id">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Select -->
                        <div class="col-md-3">
                            <label for="filter_month" class="form-label small fw-semibold">Month</label>
                            <select class="form-select form-select-sm select2" id="filter_month" name="month">
                                <option value="">All Months</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Select -->
                        <div class="col-md-3">
                            <label for="filter_year" class="form-label small fw-semibold">Year</label>
                            <select class="form-select form-select-sm select2" id="filter_year" name="year">
                                <option value="">All Years</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Data List Section -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="attendance-months-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 30px;">#</th>
                                <th scope="col" class="fw-bold text-start">Company</th>
                                <th scope="col" class="fw-bold text-start">Branch</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Month</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Year</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Employees Count</th>
                                <th scope="col" class="fw-bold text-center" style="width: 90px;">Status</th>
                                <th scope="col" class="fw-bold text-start" style="width: 130px;">Created By</th>
                                <th scope="col" class="fw-bold text-center" style="width: 140px;">Created Date</th>
                                <th scope="col" class="fw-bold text-center" style="width: 120px;">Action</th>
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        let allBranches = @json($branches);

        let table = $('#attendance-months-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('attendance.index') }}",
                data: function(d) {
                    d.company_id = $('#filter_company_id').val();
                    d.branch_id = $('#filter_branch_id').val();
                    d.month = $('#filter_month').val();
                    d.year = $('#filter_year').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'company_name', name: 'company.name' },
                { data: 'branch_name', name: 'branch.name' },
                { data: 'month', name: 'month', className: 'text-center fw-semibold' },
                { data: 'year', name: 'year', className: 'text-center' },
                { data: 'employees_count', name: 'employees_count', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                { data: 'created_by', name: 'creator.name' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "Search History:",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // Dynamic Branch dropdown population on Company change
        $('#filter_company_id').on('change', function() {
            let companyId = $(this).val();
            let branchSelect = $('#filter_branch_id');
            branchSelect.html('<option value="">Loading branches...</option>').trigger('change.select2');

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
                        branchSelect.trigger('change.select2');
                        table.draw();
                    },
                    error: function() {
                        branchSelect.html('<option value="">All Branches</option>').trigger('change.select2');
                        table.draw();
                    }
                });
            } else {
                branchSelect.html('<option value="">All Branches</option>');
                $.each(allBranches, function(key, branch) {
                    branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                });
                branchSelect.trigger('change.select2');
                table.draw();
            }
        });

        $('#filter_branch_id, #filter_month, #filter_year').on('change', function() {
            table.draw();
        });

        $('#btn-reset-filters').on('click', function() {
            $('#filter_company_id').val('').trigger('change.select2');
            $('#filter_branch_id').val('').trigger('change.select2');
            $('#filter_month').val('').trigger('change.select2');
            $('#filter_year').val('').trigger('change.select2');
            table.draw();
        });

        // AJAX Delete Attendance Month
        $(document).on('click', '.btn-delete', function() {
            let deleteUrl = $(this).data('url');
            if (confirm('Are you sure you want to delete this monthly attendance record?')) {
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
                            table.draw(false);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete monthly attendance record.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
