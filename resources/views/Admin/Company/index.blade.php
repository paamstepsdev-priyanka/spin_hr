@extends('layouts.admin')

@section('title', 'Companies')

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
                <!-- Toolbar: Export Buttons & Search Filter -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Copy</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="alert('Export to CSV')">CSV</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 text-muted" onclick="window.print()">Print</button>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label for="table-search" class="mb-0 text-muted small">Search:</label>
                        <input type="text" id="table-search" class="form-control form-control-sm" style="width: 180px;" placeholder="">
                    </div>
                </div>

                <!-- Companies Table with Full Vertical & Horizontal Borders (table-bordered) and Compact Rows (table-sm) -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0" id="companies-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                                <th scope="col" class="fw-bold text-center pe-2" style="width: 100px;">Edit</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Status</th>
                                <th scope="col" class="fw-bold" style="width: 60px;">Logo</th>
                                <th scope="col" class="fw-bold">Company Name</th>
                                <th scope="col" class="fw-bold">Email</th>
                                <th scope="col" class="fw-bold">Contact No</th>
                                <th scope="col" class="fw-bold">City</th>
                                <th scope="col" class="fw-bold">State</th>
                                <th scope="col" class="fw-bold text-center" style="width: 90px;">PF Applicable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr>
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                   <td class="text-center pe-2">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit">
                                                Edit
                                            </a>

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $company->status === 'active' ? 'bg-warning text-dark' : 'bg-secondary' }} px-2 py-1">
                                            {{ ucfirst($company->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="rounded" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <span class="badge bg-light text-muted border">N/A</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $company->name }}</td>
                                    <td><a href="mailto:{{ $company->email }}" class="text-decoration-none text-body">{{ $company->email }}</a></td>
                                    <td>{{ $company->contact_no }}</td>
                                    <td>{{ $company->city }}</td>
                                    <td>{{ $company->state }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $company->pf_applicable === 'Yes' ? 'bg-success' : 'bg-light text-muted border' }}">
                                            {{ $company->pf_applicable }}
                                        </span>
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-3 text-muted">
                                        No companies found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($companies->hasPages())
                <div class="card-footer bg-body-tertiary py-2 px-3">
                    {{ $companies->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('table-search')?.addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#companies-table tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection
