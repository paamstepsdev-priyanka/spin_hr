@extends('layouts.admin')

@section('title', 'Manage Salary - ' . $employee->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}" class="text-decoration-none">Employees</a></li>
                <li class="breadcrumb-item active" aria-current="page">Salary History</li>
            </ol>
        </nav>

        <!-- Single Unified Card -->
        <div class="card border-0 shadow-sm mb-4">
            <!-- Card Header: Title, Employee Summary & Action Buttons -->
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-body">Salary Management</h4>
                    <div class="text-muted small">
                        <strong>Employee:</strong> {{ $employee->name }} ({{ $employee->employee_code }}) | 
                        <strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }} | 
                        <strong>Company:</strong> {{ $employee->company->name ?? 'N/A' }}
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Employees
                    </a>

                    <a href="{{ route('employees.salaries.create', $employee->id) }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Salary
                    </a>
                </div>
            </div>

            <!-- Card Body: Salary Listing Table -->
            <div class="card-body p-3">
                <div id="alert-container"></div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100" id="salaries-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 10px;">#</th>
                                <th scope="col" class="fw-bold text-center" style="width: 20px;">Edit</th>
                                <th scope="col" class="fw-bold text-center" style="width: 20px;">Delete</th>
                                <th scope="col" class="fw-bold text-center">Status</th>
                                <th scope="col" class="fw-bold pe-2">Start Date</th>
                                <th scope="col" class="fw-bold pe-2">End Date</th>
                                <th scope="col" class="fw-bold">Basic Salary</th>
                                <th scope="col" class="fw-bold">Gross Salary</th>
                                <th scope="col" class="fw-bold">Total Deduction</th>
                                <th scope="col" class="fw-bold">Net Salary</th>
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
        let table = $('#salaries-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('employees.salaries.index', $employee->id) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center' },
                { data: 'delete', name: 'delete', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'effective_from', name: 'effective_from' },
                { data: 'effective_to', name: 'effective_to' },
                { data: 'basic_salary', name: 'basic_salary' },
                { data: 'gross_salary', name: 'gross_salary' },
                { data: 'total_deduction', name: 'total_deduction' },
                { data: 'net_salary', name: 'net_salary' },
               
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // Handle AJAX Delete
        $(document).on('click', '.btn-delete', function() {
            let deleteUrl = $(this).data('url');
            if (confirm('Are you sure you want to delete this salary record?')) {
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
                        $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to delete salary record.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
