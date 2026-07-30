<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'basic_salary',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'other_allowance',
        'variable_allowance',
        'gross_salary',
        'total_days',
        'leave_taken',
        'net_present',
        'leave_not_deducted',
        'payable_days',
        'per_day_salary',
        'earned_salary',
        'employee_pf',
        'esi',
        'professional_tax',
        'tds',
        'other_deduction',
        'total_deduction',
        'net_salary',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the payroll master record.
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    /**
     * Get the employee associated with the detail.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
