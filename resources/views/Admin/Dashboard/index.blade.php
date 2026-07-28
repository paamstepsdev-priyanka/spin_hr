@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="h3 fw-bold mb-1">Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="mb-0 text-white-50">Role: <span class="badge bg-light text-primary text-uppercase">{{ Auth::user()->role }}</span> | Status: <span class="badge bg-success text-uppercase">{{ Auth::user()->status }}</span></p>
                </div>
                <div>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light text-primary fw-semibold px-4">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card text-white bg-primary shadow-sm border-0">
            <div class="card-body p-4">
                <div class="fs-2 fw-bold">{{ $totalUsers ?? 0 }}</div>
                <div class="text-white-50 text-uppercase fw-semibold small">Total Users</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card text-white bg-success shadow-sm border-0">
            <div class="card-body p-4">
                <div class="fs-2 fw-bold">{{ $activeUsers ?? 0 }}</div>
                <div class="text-white-50 text-uppercase fw-semibold small">Active Users</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card text-white bg-info shadow-sm border-0">
            <div class="card-body p-4">
                <div class="fs-2 fw-bold">{{ $adminCount ?? 0 }}</div>
                <div class="text-white-50 text-uppercase fw-semibold small">Admins</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Link Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-body-tertiary fw-semibold d-flex justify-content-between align-items-center py-3">
                <span>Quick Actions</span>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary">Manage Users</a>
            </div>
            <div class="card-body p-4">
                <p class="card-text text-muted">Use the navigation bar on the left or the button above to manage system users, add new accounts, or update permissions.</p>
            </div>
        </div>
    </div>
</div>
@endsection
