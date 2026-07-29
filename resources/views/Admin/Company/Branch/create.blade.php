@extends('layouts.admin')

@section('title', 'Add Branch')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('companies.index') }}" class="text-decoration-none">Company</a></li>
                <li class="breadcrumb-item"><a href="{{ route('company.branches.index', $company->id) }}" class="text-decoration-none">Branch</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Branch</li>
            </ol>
        </nav>

        <div id="alert-container"></div>

        <form id="branch-form" action="{{ route('company.branches.store', $company->id) }}" method="POST" novalidate>
            @csrf

            <!-- Form Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-body">Add Branch Details</h5>
                    <span class="badge bg-primary fs-6">Company: {{ $company->name }}</span>
                </div>
                <div class="card-body p-4">
                    @include('Admin.Company.branch.form')
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="mt-3">
                <button type="submit" id="btn-save" class="btn btn-primary">Save Branch</button>
                <a href="{{ route('company.branches.index', $company->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#branch-form').on('submit', function(e) {
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
