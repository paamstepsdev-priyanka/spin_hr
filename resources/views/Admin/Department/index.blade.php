@extends('layouts.admin')

@section('title', 'Departments')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Departments</h4>
                <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add Department
                </a>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <!-- Toolbar: Export Buttons -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Copy</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="alert('Export to CSV')">CSV</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Print</button>
                    </div>
                </div>

                <!-- Departments Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100" id="departments-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Status</th>
                                <th scope="col" class="fw-bold text-center pe-2" style="width: 80px;">Edit</th>
                                <th scope="col" class="fw-bold">Department Name</th>
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
        $('#departments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('departments.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center pe-2' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'name', name: 'name', className: 'fw-semibold text-body' },
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });
</script>
@endpush
@endsection
