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

        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold text-body">Branch Management</h4>
                    <p class="mb-0 text-muted small">Managing branches for <strong>{{ $company->name }}</strong></p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        Back to Companies
                    </a>
                    <a href="{{ route('admin.company.branches.create', $company->id) }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        Add Branch
                    </a>
                </div>
            </div>
        </div>

        <div id="alert-container"></div>

        <!-- Content Layout: Left col-lg-9 (Branches Table), Right col-lg-3 (Company Card) -->
        <div class="row g-3">

             <!-- Right Side: Company UI Card (col-lg-3) -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-body-tertiary border-0 py-2 px-3">
                        <h6 class="mb-0 fw-bold text-body small">Company Info</h6>
                    </div>
                    <div class="card-body p-3 text-center">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="rounded mb-2" style="max-height: 50px; max-width: 100%; object-fit: contain;">
                        @endif
                        <h6 class="fw-bold mb-1 text-body">{{ $company->name }}</h6>
                        <span class="badge {{ strtolower($company->status) === 'active' ? 'bg-warning text-dark' : 'bg-danger' }} px-2 py-1 mb-2">
                            {{ ucfirst($company->status) }}
                        </span>

                        <hr class="my-2 text-muted opacity-25">

                        <div class="text-start small">
                            <div class="mb-2">
                                <span class="text-muted d-block small fw-semibold">Email</span>
                                <span class="text-body text-break">{{ $company->email }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted d-block small fw-semibold">Contact No</span>
                                <span class="text-body">{{ $company->contact_no }}</span>
                            </div>
                            @if($company->city || $company->state)
                            <div class="mb-1">
                                <span class="text-muted d-block small fw-semibold">Location</span>
                                <span class="text-body">{{ implode(', ', array_filter([$company->city, $company->state])) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Left Side: Branches Table (col-lg-9) -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
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
            ajax: "{{ route('admin.company.branches.index', $company->id) }}",
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
