@extends('layouts.admin')

@section('title', 'Add Salary - ' . $employee->name)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}" class="text-decoration-none">Employees</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.salaries.index', $employee->id) }}" class="text-decoration-none">Salary History</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Salary</li>
            </ol>
        </nav>

        <div id="alert-container"></div>

        <form id="salary-form" action="{{ route('employees.salaries.store', $employee->id) }}" method="POST" novalidate>
            @csrf

            <!-- Form Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-body">Salary Details</h5>
                    <span class="badge bg-primary fs-6">Employee: {{ $employee->name }} ({{ $employee->employee_code }})</span>
                </div>
                <div class="card-body p-4">
                    @include('admin.employee.salary.form')
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="mb-4">
                <button type="submit" id="btn-save" class="btn btn-primary px-4 fw-semibold me-2">Save</button>
                <a href="{{ route('employees.salaries.index', $employee->id) }}" class="btn btn-secondary px-4 fw-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Live Gross, Total Deduction, and Net Salary calculation logic
    function calculateSalary() {
        let getVal = function(id) {
            let val = parseFloat($('#' + id).val());
            return isNaN(val) ? 0 : val;
        };

        let basic = getVal('basic_salary');
        let variable = getVal('variable_allowance');
        let hra = getVal('hra');
        let conveyance = getVal('conveyance_allowance');
        let medical = getVal('medical_allowance');
        let special = getVal('special_allowance');
        let otherAllow = getVal('other_allowance');

        let empPf = getVal('employee_pf');
        let esi = getVal('esi');
        let pt = getVal('professional_tax');
        let tds = getVal('tds');
        let otherDed = getVal('other_deduction');

        let gross = basic + variable + hra + conveyance + medical + special + otherAllow;
        let totalDeduction = empPf + esi + pt + tds + otherDed;
        let net = gross - totalDeduction;

        $('#gross_salary').val(gross > 0 ? gross.toFixed(2) : '');
        $('#total_deduction').val(totalDeduction > 0 ? totalDeduction.toFixed(2) : '');
        $('#net_salary').val(net > 0 ? net.toFixed(2) : '');
    }

    // Trigger calculation on input change
    $(document).on('input change', '.salary-calc-input', function() {
        calculateSalary();
    });

    // Run calculation once on load
    calculateSalary();

    // AJAX Form submission
    $('#salary-form').on('submit', function(e) {
        e.preventDefault();

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
