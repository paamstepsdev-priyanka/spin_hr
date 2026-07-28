<div class="row g-4">
    <!-- Branch Name -->
    <div class="col-md-6">
        <label for="name" class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $branch->name ?? '') }}" placeholder="e.g. Head Office / Downtown Branch">
        <div class="text-danger small mt-1" id="name-error"></div>
    </div>

    <!-- Email -->
    <div class="col-md-6">
        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $branch->email ?? '') }}" placeholder="e.g. branch@company.com">
        <div class="text-danger small mt-1" id="email-error"></div>
    </div>

    <!-- Contact No -->
    <div class="col-md-6">
        <label for="contact_no" class="form-label fw-semibold">Contact No <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="contact_no" name="contact_no" value="{{ old('contact_no', $branch->contact_no ?? '') }}" placeholder="e.g. 9876543210">
        <div class="text-danger small mt-1" id="contact_no-error"></div>
    </div>

    <!-- Status -->
    <div class="col-md-6">
        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
        <select class="form-select" id="status" name="status">
            <option value="">Select Status</option>
            <option value="active" {{ old('status', $branch->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $branch->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <div class="text-danger small mt-1" id="status-error"></div>
    </div>

    <!-- Address Line 1 -->
    <div class="col-md-6">
        <label for="address_line1" class="form-label fw-semibold">Address Line 1 <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="address_line1" name="address_line1" value="{{ old('address_line1', $branch->address_line1 ?? '') }}" placeholder="e.g. 123 Business Bay">
        <div class="text-danger small mt-1" id="address_line1-error"></div>
    </div>

    <!-- Address Line 2 -->
    <div class="col-md-6">
        <label for="address_line2" class="form-label fw-semibold">Address Line 2</label>
        <input type="text" class="form-control" id="address_line2" name="address_line2" value="{{ old('address_line2', $branch->address_line2 ?? '') }}" placeholder="e.g. Floor 4, Suite B">
        <div class="text-danger small mt-1" id="address_line2-error"></div>
    </div>

    <!-- City -->
    <div class="col-md-4">
        <label for="city" class="form-label fw-semibold">City <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $branch->city ?? '') }}" placeholder="e.g. Mumbai">
        <div class="text-danger small mt-1" id="city-error"></div>
    </div>

    <!-- State -->
    <div class="col-md-4">
        <label for="state" class="form-label fw-semibold">State <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $branch->state ?? '') }}" placeholder="e.g. Maharashtra">
        <div class="text-danger small mt-1" id="state-error"></div>
    </div>

    <!-- Zip Code -->
    <div class="col-md-4">
        <label for="zip_code" class="form-label fw-semibold">Zip Code <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code', $branch->zip_code ?? '') }}" placeholder="e.g. 400001">
        <div class="text-danger small mt-1" id="zip_code-error"></div>
    </div>
</div>
