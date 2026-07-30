<div class="card border-0 shadow-sm">
    <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-body">
                Attendance Entry 
                <span class="text-primary">({{ date('d-m-Y', strtotime($attendanceDate)) }})</span>
            </h5>
            <small class="text-muted">Active Employees: {{ count($records) }}</small>
        </div>
        <div>
            @if($isEditMode)
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i> Editing Existing Batch
                </span>
            @else
                <span class="badge bg-success text-white px-3 py-2 fs-6 fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> New Attendance Batch
                </span>
            @endif
        </div>
    </div>
    <div class="card-body p-3">
        @if(count($records) == 0)
            <div class="text-center py-4 text-muted">
                <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                No active employees found for the selected company and branch.
            </div>
        @else
            <form id="attendance-save-form">
                @csrf
                <input type="hidden" name="company_id" value="{{ $companyId }}">
                <input type="hidden" name="branch_id" value="{{ $branchId }}">
                <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle small mb-0 w-100 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="fw-bold text-center" style="width: 40px;">#</th>
                                <th scope="col" class="fw-bold text-center" style="width: 100px;">Employee Code</th>
                                <th scope="col" class="fw-bold text-start">Employee Name</th>
                                <th scope="col" class="fw-bold text-start">Department</th>
                                <th scope="col" class="fw-bold text-center" style="width: 170px;">Attendance Status</th>
                                <th scope="col" class="fw-bold text-center" style="width: 120px;">Check In</th>
                                <th scope="col" class="fw-bold text-center" style="width: 120px;">Check Out</th>
                                <th scope="col" class="fw-bold text-start">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $index => $rec)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td class="text-center fw-bold text-body">
                                        {{ $rec['employee_code'] }}
                                        <input type="hidden" name="attendances[{{ $index }}][employee_id]" value="{{ $rec['employee_id'] }}">
                                    </td>
                                    <td class="fw-semibold text-body">{{ $rec['name'] }}</td>
                                    <td>{{ $rec['department_name'] }}</td>
                                    <td>
                                        <select class="form-select form-select-sm fw-semibold status-select" name="attendances[{{ $index }}][attendance_status]" required>
                                            <option value="Present" {{ $rec['attendance_status'] == 'Present' ? 'selected' : '' }}>🟢 Present</option>
                                            <option value="Absent" {{ $rec['attendance_status'] == 'Absent' ? 'selected' : '' }}>🔴 Absent</option>
                                            <option value="Half Day" {{ $rec['attendance_status'] == 'Half Day' ? 'selected' : '' }}>🟡 Half Day</option>
                                            <option value="Leave" {{ $rec['attendance_status'] == 'Leave' ? 'selected' : '' }}>🔵 Leave</option>
                                            <option value="Holiday" {{ $rec['attendance_status'] == 'Holiday' ? 'selected' : '' }}>⚫ Holiday</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm text-center" name="attendances[{{ $index }}][check_in]" value="{{ $rec['check_in'] }}">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm text-center" name="attendances[{{ $index }}][check_out]" value="{{ $rec['check_out'] }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="attendances[{{ $index }}][remarks]" value="{{ $rec['remarks'] }}" placeholder="Optional remarks">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="btn-save-attendance">
                        <i class="bi bi-check-circle me-1"></i> Save Attendance
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
