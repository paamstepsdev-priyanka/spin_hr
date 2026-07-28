@extends('layouts.admin')

@section('title', 'Add Company')

@section('content')
<div class="row">
    <div class="col-12">
        <div id="alert-container"></div>

        <form id="company-form" action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Form Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-body-tertiary py-3">
                    <h5 class="mb-0 fw-bold text-body">Basic Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Company Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. SpinHR Tech Pvt Ltd">
                            <div class="text-danger small mt-1" id="name-error"></div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. contact@spinhr.com">
                            <div class="text-danger small mt-1" id="email-error"></div>
                        </div>

                        <!-- Contact No -->
                        <div class="col-md-6">
                            <label for="contact_no" class="form-label fw-semibold">Contact No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_no" name="contact_no" value="{{ old('contact_no') }}" placeholder="e.g. 9876543210">
                            <div class="text-danger small mt-1" id="contact_no-error"></div>
                        </div>

                        <!-- Company Logo -->
                        <div class="col-md-6">
                            <label for="logo" class="form-label fw-semibold">Company Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <div class="text-danger small mt-1" id="logo-error"></div>
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-md-6">
                            <label for="address_line1" class="form-label fw-semibold">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" placeholder="e.g. Building 101, Tech Park">
                            <div class="text-danger small mt-1" id="address_line1-error"></div>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-md-6">
                            <label for="address_line2" class="form-label fw-semibold">Address Line 2</label>
                            <input type="text" class="form-control" id="address_line2" name="address_line2" value="{{ old('address_line2') }}" placeholder="e.g. Main Street, Suite 4">
                            <div class="text-danger small mt-1" id="address_line2-error"></div>
                        </div>

                        <!-- City -->
                        <div class="col-md-4">
                            <label for="city" class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" placeholder="e.g. Mumbai">
                            <div class="text-danger small mt-1" id="city-error"></div>
                        </div>

                        <!-- State -->
                        <div class="col-md-4">
                            <label for="state" class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" placeholder="e.g. Maharashtra">
                            <div class="text-danger small mt-1" id="state-error"></div>
                        </div>

                        <!-- Zip Code -->
                        <div class="col-md-4">
                            <label for="zip_code" class="form-label fw-semibold">Zip Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" placeholder="e.g. 400001">
                            <div class="text-danger small mt-1" id="zip_code-error"></div>
                        </div>

                        <!-- PF Applicable -->
                        <div class="col-md-6">
                            <label for="pf_applicable" class="form-label fw-semibold">PF Applicable <span class="text-danger">*</span></label>
                            <select class="form-select" id="pf_applicable" name="pf_applicable">
                                <option value="">Select PF Applicable</option>
                                <option value="No" {{ old('pf_applicable') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('pf_applicable') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                            <div class="text-danger small mt-1" id="pf_applicable-error"></div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
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
                <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#company-form').on('submit', function(e) {
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
