@extends('layouts.admin')

@section('title', 'Add Employee')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}" class="text-decoration-none">Employees</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Employee</li>
            </ol>
        </nav>

        <div id="alert-container"></div>

        <form id="employee-form" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            @include('Admin.Employee.form')

            <!-- Form Action Buttons -->
            <div class="mb-4">
                <button type="submit" id="btn-save" class="btn btn-primary px-4 fw-semibold">Save Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Dynamic Accommodation Type fields toggling
    function toggleAccommodationFields() {
        let type = $('#accommodation_type').val();

        if (type === 'Own Accommodation') {
            $('#wrapper_national_rent').hide();
            $('#wrapper_rent_paid_by_company').hide();
            $('#wrapper_property_owner_name').hide();
            $('#wrapper_property_owner_contact').hide();
        } else if (type === 'Company Accommodation') {
            $('#wrapper_national_rent').show();
            $('#wrapper_rent_paid_by_company').hide();
            $('#wrapper_property_owner_name').hide();
            $('#wrapper_property_owner_contact').hide();
        } else if (type === 'Rented Accommodation') {
            $('#wrapper_national_rent').hide();
            $('#wrapper_rent_paid_by_company').show();
            $('#wrapper_property_owner_name').show();
            $('#wrapper_property_owner_contact').show();
        } else {
            $('#wrapper_national_rent').hide();
            $('#wrapper_rent_paid_by_company').hide();
            $('#wrapper_property_owner_name').hide();
            $('#wrapper_property_owner_contact').hide();
        }
    }

    $('#accommodation_type').on('change', toggleAccommodationFields);
    toggleAccommodationFields();

    // Dynamic Branch filtering on Company selection
    $('#company_id').on('change', function() {
        let companyId = $(this).val();
        let branchSelect = $('#branch_id');
        branchSelect.html('<option value="">Loading branches...</option>');

        if (companyId) {
            let url = "{{ route('companies.get-branches', ':company') }}".replace(':company', companyId);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(branches) {
                    branchSelect.html('<option value="">Select Branch</option>');
                    $.each(branches, function(key, branch) {
                        branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    });
                },
                error: function() {
                    branchSelect.html('<option value="">Select Branch</option>');
                }
            });
        } else {
            branchSelect.html('<option value="">Select Branch</option>');
        }
    });

    // AJAX Form Submit
    $('#employee-form').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation styling & errors
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
                        let field = key.replace('.', '_');
                        $('#' + field).addClass('is-invalid');
                        $('#' + field + '-error').html(messages[0]);
                    });

                    // Scroll to first error
                    let firstError = $('.is-invalid:first');
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 200);
                    }
                } else {
                    let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred while saving. Please try again.';
                    $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            }
        });
    });
});
</script>
@endpush
@endsection
