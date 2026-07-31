@extends('layouts.admin')

@section('title', 'Branch Management')

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
                <li class="breadcrumb-item"><a href="{{ route('companies.index') }}" class="text-decoration-none">Company</a></li>
                <li class="breadcrumb-item active" aria-current="page">Branch</li>
            </ol>
        </nav>

        <div id="alert-container"></div>

        <!-- Single Unified Card -->
        <div class="card border-0 shadow-sm mb-4">
            <!-- Card Header: Title & Actions -->
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold text-body">Branch Management</h4>
                    <span class="text-muted small">Managing branches for <strong>{{ $company->name }}</strong></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        Back to Companies
                    </a>
                    <a href="{{ route('company.branches.create', $company->id) }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Branch
                    </a>
                </div>
            </div>

            <!-- Card Body: Company Summary Banner + Branches Table -->
            <div class="card-body p-3">
                <!-- Company Info Banner -->
                <div class="bg-body-tertiary p-3 rounded mb-3 border border-light-subtle">
                    <div class="row align-items-center g-3 small">
                        <div class="col-md-3 d-flex align-items-center gap-2">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="rounded" style="max-height: 40px; max-width: 80px; object-fit: contain;">
                            @endif
                            <div>
                                <h6 class="fw-bold mb-1 text-body">{{ $company->name }}</h6>
                                <span class="badge {{ strtolower($company->status) === 'active' ? 'bg-warning text-dark' : 'bg-danger' }} px-2 py-1">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted d-block small fw-semibold">Email</span>
                            <span class="text-body text-break">{{ $company->email }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted d-block small fw-semibold">Contact No</span>
                            <span class="text-body">{{ $company->contact_no }}</span>
                        </div>
                        <div class="col-md-3">
                            @if($company->city || $company->state)
                                <span class="text-muted d-block small fw-semibold">Location</span>
                                <span class="text-body">{{ implode(', ', array_filter([$company->city, $company->state])) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Print</button>
                    </div>
                </div>

                <!-- Branches Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100" id="branches-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                                <th scope="col" class="fw-bold text-center pe-2" style="width: 80px;">Edit</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Status</th>
                                <th scope="col" class="fw-bold">Branch Name</th>
                                <th scope="col" class="fw-bold">Email</th>
                                <th scope="col" class="fw-bold">Contact No</th>
                                <th scope="col" class="fw-bold">City</th>
                                <th scope="col" class="fw-bold">State</th>
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
        let table = $('#branches-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('company.branches.index', $company->id) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center pe-2' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'name', name: 'name', className: 'fw-semibold' },
                { data: 'email', name: 'email' },
                { data: 'contact_no', name: 'contact_no' },
                { data: 'city', name: 'city' },
                { data: 'state', name: 'state' },
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
