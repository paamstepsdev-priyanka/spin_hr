<?php

namespace App\Models;

use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    use HasFactory, HasCompanyScope;

    protected $table = 'employee_salaries';

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'variable_allowance',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'other_allowance',
        'employee_pf',
        'esi',
        'professional_tax',
        'tds',
        'other_deduction',
        'gross_salary',
        'total_deduction',
        'net_salary',
        'effective_from',
        'effective_to',
        'status',
    ];

    /**
     * Get the employee that owns the salary record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
