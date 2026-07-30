@extends('layouts.admin')

@section('title', 'Edit Attendance Batch')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body-tertiary border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-body">
                        Edit Attendance Batch
                    </h5>
                    <div class="small text-muted mt-1">
                        <strong>Company:</strong> {{ $batch->company ? $batch->company->name : 'N/A' }} | 
                        <strong>Branch:</strong> {{ $batch->branch ? $batch->branch->name : 'N/A' }} | 
                        <strong>Date:</strong> {{ date('d-m-Y', strtotime($batch->attendance_date)) }}
                    </div>
                </div>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Attendance
                </a>
            </div>
            <div class="card-body p-3">
                <form id="attendance-update-form" action="{{ route('attendance.update', $batch->id) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                                            <select class="form-select form-select-sm fw-bold status-select" name="attendances[{{ $index }}][attendance_status]" required>
                                                <option value="Present" class="text-success fw-bold" {{ $rec['attendance_status'] == 'Present' ? 'selected' : '' }}>● Present</option>
                                                <option value="Absent" class="text-danger fw-bold" {{ $rec['attendance_status'] == 'Absent' ? 'selected' : '' }}>● Absent</option>
                                                <option value="Half Day" class="text-warning fw-bold" {{ $rec['attendance_status'] == 'Half Day' ? 'selected' : '' }}>● Half Day</option>
                                                <option value="Leave" class="text-info fw-bold" {{ $rec['attendance_status'] == 'Leave' ? 'selected' : '' }}>● Leave</option>
                                                <option value="Holiday" class="text-dark fw-bold" {{ $rec['attendance_status'] == 'Holiday' ? 'selected' : '' }}>● Holiday</option>
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

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary btn-sm px-3">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="btn-update-attendance">
                            <i class="bi bi-check-circle me-1"></i> Update Attendance Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        function updateStatusSelectStyle(el) {
            let val = $(el).val();
            $(el).removeClass('bg-success-subtle text-success border-success bg-danger-subtle text-danger border-danger bg-warning-subtle text-dark border-warning bg-info-subtle text-info border-info bg-secondary-subtle text-dark border-secondary');
            if (val === 'Present') {
                $(el).addClass('bg-success-subtle text-success border-success');
            } else if (val === 'Absent') {
                $(el).addClass('bg-danger-subtle text-danger border-danger');
            } else if (val === 'Half Day') {
                $(el).addClass('bg-warning-subtle text-dark border-warning');
            } else if (val === 'Leave') {
                $(el).addClass('bg-info-subtle text-info border-info');
            } else if (val === 'Holiday') {
                $(el).addClass('bg-secondary-subtle text-dark border-secondary');
            }
        }

        $('.status-select').each(function() {
            updateStatusSelectStyle(this);
        });

        $(document).on('change', '.status-select', function() {
            updateStatusSelectStyle(this);
        });

        $('#attendance-update-form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#btn-update-attendance');
            let origHtml = btn.html();

            btn.prop('disabled', true).addClass('disabled').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).removeClass('disabled').html(origHtml);
                    if (response.status) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).removeClass('disabled').html(origHtml);
                    let errorMsg = 'Failed to update attendance batch.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        });
    });
</script>
@endpush
@endsection
