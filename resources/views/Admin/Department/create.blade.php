@extends('layouts.admin')

@section('title', 'Add Department')

@section('content')
<div class="row">
    <div class="col-12">
        <div id="alert-container"></div>

        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}" class="text-decoration-none">Departments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Department</li>
            </ol>
        </nav>

        <form id="department-form" action="{{ route('departments.store') }}" method="POST" novalidate>
            @csrf

            <!-- Form Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-body-tertiary py-3">
                    <h5 class="mb-0 fw-bold text-body">Add Department</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Department Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Human Resources">
                            <div class="text-danger small mt-1" id="name-error"></div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="text-danger small mt-1" id="status-error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="mt-3">
                <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#department-form').on('submit', function(e) {
        e.preventDefault();

        // Clear all previous errors and validation styling
        $('.text-danger.small').html('');
        $('.form-control, .form-select').removeClass('is-invalid');
        $('#alert-container').html('');

        let formData = new FormData(this);
        let saveBtn = $('#btn-save');
        saveBtn.prop('disabled', true).addClass('disabled');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr) {
                saveBtn.prop('disabled', false).removeClass('disabled');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, messages) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').html(messages[0]);
                    });
                } else {
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">An error occurred while saving. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
