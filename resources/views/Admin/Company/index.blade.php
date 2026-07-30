@extends('layouts.admin')

@section('title', 'Companies')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Companies</h4>
                <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add Company
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

                <!-- Companies Table formatted dynamically via DataTables & Controller editColumn -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100" id="companies-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                                <th scope="col" class="fw-bold text-center pe-2" style="width: 20px;">Edit</th>
                                <th scope="col" class="fw-bold text-center" style="width: 90px;">Branches</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Status</th>
                                <th scope="col" class="fw-bold text-center" style="width: 60px;">Logo</th>
                                <th scope="col" class="fw-bold">Company Name</th>
                                <th scope="col" class="fw-bold">Email</th>
                                <th scope="col" class="fw-bold">Contact No</th>
                                <th scope="col" class="fw-bold">City</th>
                                <th scope="col" class="fw-bold">State</th>
                                <th scope="col" class="fw-bold text-center" style="width: 90px;">PF Applicable</th>
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
        $('#companies-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('companies.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center pe-2' },
                { data: 'branches', name: 'branches', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'logo', name: 'logo', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name', name: 'name', className: 'fw-semibold' },
                { data: 'email', name: 'email' },
                { data: 'contact_no', name: 'contact_no' },
                { data: 'city', name: 'city' },
                { data: 'state', name: 'state' },
                { data: 'pf_applicable', name: 'pf_applicable', className: 'text-center' },
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
