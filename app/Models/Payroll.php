<?php

namespace App\Models;

use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected static function booted(): void
    {
        static::saved(function ($model) {
            \App\Services\CompanyScope::clearCache($model->company_id);
        });
        static::deleted(function ($model) {
            \App\Services\CompanyScope::clearCache($model->company_id);
        });
    }

    protected $fillable = [
        'company_id',
        'branch_id',
        'month',
        'year',
        'total_gross_salary',
        'total_deduction',
        'total_net_salary',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the company associated with the payroll.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch associated with the payroll.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the payroll.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the detail records for the payroll.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'payroll_id');
    }
}
