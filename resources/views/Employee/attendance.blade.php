@extends('layouts.employee')

@section('title', 'My Attendance')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-body"><i class="bi bi-calendar-check me-2 text-primary"></i>My Attendance History</h4>
            <small class="text-muted">View your monthly attendance summary records.</small>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('employee.attendance') }}" class="row g-2 mb-4 bg-body-tertiary p-3 rounded align-items-end">
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label small fw-semibold text-muted">Filter by Month</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-5 col-md-4">
                <label class="form-label small fw-semibold text-muted">Filter by Year</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-sm-2 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="{{ route('employee.attendance') }}" class="btn btn-outline-secondary btn-sm fw-semibold"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
            </div>
        </form>

        <!-- Attendance Records Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th class="text-center">Total Days</th>
                        <th class="text-center text-success fw-bold">Present</th>
                        <th class="text-center text-warning fw-bold">Leave Taken</th>
                        <th class="text-center">Leave Not Deducted</th>
                        <th class="text-center table-primary text-primary fw-bold">Payable Days</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $index => $row)
                        @php
                            $monthName = \Carbon\Carbon::createFromDate($row->attendanceMonth->year, $row->attendanceMonth->month, 1)->format('F');
                        @endphp
                        <tr>
                            <td class="text-center">{{ $attendances->firstItem() + $index }}</td>
                            <td class="fw-semibold text-dark">{{ $monthName }}</td>
                            <td>{{ $row->attendanceMonth->year }}</td>
                            <td class="text-center">{{ $row->total_days }}</td>
                            <td class="text-center text-success fw-bold">{{ $row->net_present }}</td>
                            <td class="text-center text-warning fw-bold">{{ $row->leave_taken }}</td>
                            <td class="text-center">{{ $row->leave_not_deducted }}</td>
                            <td class="text-center fw-bold text-primary fs-6">{{ $row->payable_days }}</td>
                            <td class="text-center">
                                <span class="badge bg-success px-2 py-1">Completed</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x display-6 d-block mb-2 text-secondary"></i>
                                No attendance records found matching selected filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
