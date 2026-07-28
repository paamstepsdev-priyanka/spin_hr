@extends('layouts.admin')

@section('title', 'Add Company')

@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. SpinHR Tech Pvt Ltd" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. contact@spinhr.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact No -->
                        <div class="col-md-6">
                            <label for="contact_no" class="form-label fw-semibold">Contact No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('contact_no') is-invalid @enderror" id="contact_no" name="contact_no" value="{{ old('contact_no') }}" placeholder="e.g. +91 9876543210" required>
                            @error('contact_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Company Logo -->
                        <div class="col-md-6">
                            <label for="logo" class="form-label fw-semibold">Company Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-md-6">
                            <label for="address_line1" class="form-label fw-semibold">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" placeholder="e.g. Building 101, Tech Park" required>
                            @error('address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-md-6">
                            <label for="address_line2" class="form-label fw-semibold">Address Line 2</label>
                            <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" name="address_line2" value="{{ old('address_line2') }}" placeholder="e.g. Main Street, Suite 4">
                            @error('address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-md-4">
                            <label for="city" class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" placeholder="e.g. Mumbai" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- State -->
                        <div class="col-md-4">
                            <label for="state" class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state') }}" placeholder="e.g. Maharashtra" required>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Zip Code -->
                        <div class="col-md-4">
                            <label for="zip_code" class="form-label fw-semibold">Zip Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" placeholder="e.g. 400001" required>
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PF Applicable -->
                        <div class="col-md-6">
                            <label for="pf_applicable" class="form-label fw-semibold">PF Applicable <span class="text-danger">*</span></label>
                            <select class="form-select @error('pf_applicable') is-invalid @enderror" id="pf_applicable" name="pf_applicable" required>
                                <option value="No" {{ old('pf_applicable', 'No') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('pf_applicable') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('pf_applicable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary fw-semibold px-4 py-2 d-inline-flex align-items-center gap-2">
                    Save
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2zm12 1v2H2V2h12zm0 4v8H2V6h12z"/>
                    </svg>
                </button>

                <a href="{{ route('companies.index') }}" class="btn btn-secondary fw-semibold px-4 py-2 d-inline-flex align-items-center gap-2">
                    Cancel
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H4.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L4.707 8H12.5A1.5 1.5 0 0 0 14 6.5V2a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
