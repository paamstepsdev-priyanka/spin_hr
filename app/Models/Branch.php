<?php

namespace App\Models;

use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'contact_no',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the company that owns the branch.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employees for the branch.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the monthly attendance records for the branch.
     */
    public function attendanceMonths()
    {
        return $this->hasMany(AttendanceMonth::class);
    }
}

