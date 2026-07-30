@extends('layouts.admin')

@section('title', 'Attendance Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Attendance Management</h4>
                <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg me-1"></i>
                    Mark Attendance
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="alert-container"></div>

        <!-- Saved Attendance History Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-body">Saved Attendance History</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap" id="attendance-batches-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 30px;">#</th>
                                <th scope="col" class="fw-bold text-start">Company</th>
                                <th scope="col" class="fw-bold text-start">Branch</th>
                                <th scope="col" class="fw-bold text-center" style="width: 110px;">Date</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Employees</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Status</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Action</th>
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
        let table = $('#attendance-batches-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('attendance.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'company_name', name: 'company.name' },
                { data: 'branch_name', name: 'branch.name' },
                { data: 'attendance_date', name: 'attendance_date', className: 'text-center fw-bold' },
                { data: 'employees_count', name: 'employees_count', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<div class="d-flex justify-content-center gap-1">' + row.edit + row.delete + '</div>';
                    }
                }
            ],
            language: {
                search: "Search History:",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // AJAX Delete Attendance Batch
        $(document).on('click', '.btn-delete', function() {
            let deleteUrl = $(this).data('url');
            if (confirm('Are you sure you want to delete this attendance batch?')) {
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
                    error: function() {
                        $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to delete attendance batch.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
