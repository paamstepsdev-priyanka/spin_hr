@extends('layouts.admin')

@section('title', 'Employees')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Employees</h4>
               
                  <div class="row g-3">
                    <!-- Company Filter -->
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filter_company_id">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Branch Filter -->
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filter_branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee Type Filter -->
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filter_employment_type">
                            <option value="">All Employee Types</option>
                            <option value="Permanent">Permanent</option>
                            <option value="Contract">Contract</option>
                            <option value="Probation">Probation</option>
                            <option value="Intern">Intern</option>
                            <option value="Part-time">Part-time</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filter_status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                 <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add Employee
                </a>
                {{-- <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" id="btn-reset-filter" class="btn btn-sm btn-outline-secondary px-3">Reset Filters</button>
                </div> --}}
            </div>
        </div>

        <div id="alert-container"></div>

        {{-- <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            
            <div class="card-body p-3">
              
            </div>
        </div> --}}

        <!-- Main Content Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <!-- Toolbar: Export / Print Buttons -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Print</button>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100" id="employees-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 10px;">#</th>
                                <th scope="col" class="fw-bold text-center" style="width: 20px;">Edit</th>
                                <th scope="col" class="fw-bold text-center" style="width: 20px;">Delete</th>
                                <th scope="col" class="fw-bold text-center" style="width: 20px;">Salary</th>
                                <th scope="col" class="fw-bold text-center" style="width: 20px;">Status</th>
                                <th scope="col" class="fw-bold pe-2" style="width: 110px;">Employee Code</th>
                                <th scope="col" class="fw-bold">Name</th>
                                <th scope="col" class="fw-bold">Company</th>
                                <th scope="col" class="fw-bold">Branch</th>
                                <th scope="col" class="fw-bold">Department</th>
                                <th scope="col" class="fw-bold">Designation</th>
                                <th scope="col" class="fw-bold">Mobile</th>
                                <th scope="col" class="fw-bold">Email</th>
                               
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
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center' },
                { data: 'delete', name: 'delete', orderable: false, searchable: false, className: 'text-center' },
                { data: 'salary', name: 'salary', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'employee_code', name: 'employee_code', className: 'fw-bold text-body' },   
                { data: 'name', name: 'name', className: 'fw-semibold' },
                { data: 'company_name', name: 'company.name' },
                { data: 'branch_name', name: 'branch.name' },
                { data: 'department_name', name: 'department.name' },
                { data: 'designation', name: 'designation' },
                { data: 'mobile', name: 'mobile' },
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
