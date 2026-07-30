<?php

namespace App\Traits;

use App\Services\CompanyScope;
use Illuminate\Database\Eloquent\Builder;

trait HasCompanyScope
{
    /**
     * Scope a query to only include records of the active company from session.
     */
    public function scopeForCurrentCompany(Builder $query): Builder
    {
        $companyId = CompanyScope::id();

        if ($companyId === null) {
            return $query; // Super Admin "All Companies" mode
        }

        $table = $this->getTable();

        if (in_array($table, ['employees', 'branches', 'payrolls', 'attendance_months'])) {
            return $query->where($table . '.company_id', $companyId);
        }

        if ($table === 'employee_salaries') {
            return $query->whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        if ($table === 'departments') {
            return $query->whereHas('employees', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        return $query->where($table . '.company_id', $companyId);
    }

    /**
     * Alias for forCurrentCompany.
     */
    public function scopeCompanyScope(Builder $query): Builder
    {
        return $this->scopeForCurrentCompany($query);
    }
}
