<div class="row g-3">
    <!-- Earnings / Allowances Section -->
    <div class="col-12">
        <div class="card border-0 bg-body-tertiary">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="fw-bold text-primary mb-0"><i class="bi bi-cash-stack me-1"></i> Earnings / Allowances</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="basic_salary" class="form-label fw-semibold small">Basic Salary <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', (isset($salary) && $salary->basic_salary > 0) ? $salary->basic_salary : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="basic_salary-error"></div>
                    </div>

                    <div class="col-md-3">
                        <label for="hra" class="form-label fw-semibold small">HRA</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="hra" name="hra" value="{{ old('hra', (isset($salary) && $salary->hra > 0) ? $salary->hra : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="hra-error"></div>
                    </div>

                    <div class="col-md-3">
                        <label for="conveyance_allowance" class="form-label fw-semibold small">Conveyance Allowance</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="conveyance_allowance" name="conveyance_allowance" value="{{ old('conveyance_allowance', (isset($salary) && $salary->conveyance_allowance > 0) ? $salary->conveyance_allowance : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="conveyance_allowance-error"></div>
                    </div>

                    <div class="col-md-3">
                        <label for="medical_allowance" class="form-label fw-semibold small">Medical Allowance</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="medical_allowance" name="medical_allowance" value="{{ old('medical_allowance', (isset($salary) && $salary->medical_allowance > 0) ? $salary->medical_allowance : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="medical_allowance-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="variable_allowance" class="form-label fw-semibold small">Variable Allowance</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="variable_allowance" name="variable_allowance" value="{{ old('variable_allowance', (isset($salary) && $salary->variable_allowance > 0) ? $salary->variable_allowance : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="variable_allowance-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="special_allowance" class="form-label fw-semibold small">Special Allowance</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="special_allowance" name="special_allowance" value="{{ old('special_allowance', (isset($salary) && $salary->special_allowance > 0) ? $salary->special_allowance : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="special_allowance-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="other_allowance" class="form-label fw-semibold small">Other Allowance</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="other_allowance" name="other_allowance" value="{{ old('other_allowance', (isset($salary) && $salary->other_allowance > 0) ? $salary->other_allowance : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="other_allowance-error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deductions Section -->
    <div class="col-12">
        <div class="card border-0 bg-body-tertiary">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="fw-bold text-danger mb-0"><i class="bi bi-dash-circle me-1"></i> Deductions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="employee_pf" class="form-label fw-semibold small">Employee PF</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="employee_pf" name="employee_pf" value="{{ old('employee_pf', (isset($salary) && $salary->employee_pf > 0) ? $salary->employee_pf : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="employee_pf-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="esi" class="form-label fw-semibold small">Employee State Insurance</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="esi" name="esi" value="{{ old('esi', (isset($salary) && $salary->esi > 0) ? $salary->esi : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="esi-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="professional_tax" class="form-label fw-semibold small">Professional Tax (PT)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="professional_tax" name="professional_tax" value="{{ old('professional_tax', (isset($salary) && $salary->professional_tax > 0) ? $salary->professional_tax : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="professional_tax-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="tds" class="form-label fw-semibold small">TDS</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="tds" name="tds" value="{{ old('tds', (isset($salary) && $salary->tds > 0) ? $salary->tds : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="tds-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="other_deduction" class="form-label fw-semibold small">Other Deduction</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control salary-calc-input" id="other_deduction" name="other_deduction" value="{{ old('other_deduction', (isset($salary) && $salary->other_deduction > 0) ? $salary->other_deduction : '') }}" placeholder="0.00">
                        </div>
                        <div class="text-danger small" id="other_deduction-error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Summary (Readonly / Auto Calculated) -->
    <div class="col-12">
        <div class="card border border-primary-subtle bg-primary-subtle">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="fw-bold text-body mb-0"><i class="bi bi-calculator me-1"></i> Salary Summary (Auto Calculated)</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="gross_salary" class="form-label fw-bold small text-success">Gross Salary</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white font-weight-bold">₹</span>
                            <input type="text" class="form-control form-control-sm bg-white fw-bold text-success fs-6" id="gross_salary" name="gross_salary" value="{{ old('gross_salary', (isset($salary) && $salary->gross_salary > 0) ? number_format($salary->gross_salary, 2, '.', '') : '') }}" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="total_deduction" class="form-label fw-bold small text-danger">Total Deduction</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white font-weight-bold">₹</span>
                            <input type="text" class="form-control form-control-sm bg-white fw-bold text-danger fs-6" id="total_deduction" name="total_deduction" value="{{ old('total_deduction', (isset($salary) && $salary->total_deduction > 0) ? number_format($salary->total_deduction, 2, '.', '') : '') }}" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="net_salary" class="form-label fw-bold small text-primary">Net Salary</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white font-weight-bold">₹</span>
                            <input type="text" class="form-control form-control-sm bg-white fw-bold text-primary fs-6" id="net_salary" name="net_salary" value="{{ old('net_salary', (isset($salary) && $salary->net_salary > 0) ? number_format($salary->net_salary, 2, '.', '') : '') }}" placeholder="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Effective Dates & Status Section -->
    <div class="col-12">
        <div class="card border-0 bg-body-tertiary">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="fw-bold text-body mb-0"><i class="bi bi-calendar-event me-1"></i> Effective Period & Status</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="effective_from" class="form-label fw-semibold small">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="effective_from" name="effective_from" value="{{ old('effective_from', isset($salary->effective_from) ? date('Y-m-d', strtotime($salary->effective_from)) : '') }}" required>
                        <div class="text-danger small" id="effective_from-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="effective_to" class="form-label fw-semibold small">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="effective_to" name="effective_to" value="{{ old('effective_to', isset($salary->effective_to) ? date('Y-m-d', strtotime($salary->effective_to)) : '') }}" required>
                        <div class="text-danger small" id="effective_to-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="active" {{ (old('status', $salary->status ?? 'active') === 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', $salary->status ?? '') === 'inactive') ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <div class="text-danger small" id="status-error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
