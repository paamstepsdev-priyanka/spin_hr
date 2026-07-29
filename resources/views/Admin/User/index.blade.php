@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Banner Block -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-body-tertiary rounded p-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-body">Users</h4>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add User
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

                <!-- Users Table with Full Vertical & Horizontal Borders (table-bordered) and Compact Rows (table-sm) -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0" id="users-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                                <th scope="col" class="fw-bold text-center" style="width: 55px;">Show</th>
                                <th scope="col" class="fw-bold text-center" style="width: 80px;">Status</th>
                                <th scope="col" class="fw-bold">Register Date</th>
                                <th scope="col" class="fw-bold">Name</th>
                                <th scope="col" class="fw-bold">E-Mail</th>
                                <th scope="col" class="fw-bold">Role</th>
                                <th scope="col" class="fw-bold text-center pe-2" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm p-0 d-inline-flex align-items-center justify-content-center rounded-1" style="width: 24px; height: 20px;" title="Show Details">
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $user->status === 'active' ? 'bg-warning text-dark' : 'bg-danger' }} px-2 py-1">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none text-body">{{ $user->email }}</a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }} text-uppercase">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-2">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit">
                                                Edit
                                            </a>
                                            @if(Auth::id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-1" title="Delete">
                                                        Del
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-3 text-muted">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-body-tertiary py-2 px-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('table-search')?.addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#users-table tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection
