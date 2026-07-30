<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'branch_id',
        'department_id',
        'employment_type',
        'designation',
        'employee_code',
        'reporting_to',
        'joining_date',
        'work_phone1',
        'work_phone2',
        'cell_phone',
        'name',
        'father_name',
        'email',
        'mobile',
        'dob',
        'gender',
        'marital_status',
        'accommodation_type',
        'rent_paid_by_company',
        'property_owner_name',
        'property_owner_contact',
        'national_rent',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'pan_no',
        'aadhar_no',
        'photo',
        'pan_card',
        'aadhar_card',
        'cancelled_cheque',
        'resume',
        'relationship',
        'contact_person_name',
        'primary_phone',
        'alternative_phone',
        'account_holder_name',
        'account_no',
        'ifsc_code',
        'bank_name',
        'bank_branch_name',
        'status',
    ];

    /**
     * Get the user record associated with the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the employee.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch that owns the employee.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the department that owns the employee.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the salaries for the employee.
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    /**
     * Get the attendance records for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the monthly attendance details for the employee.
     */
    public function monthlyAttendanceDetails(): HasMany
    {
        return $this->hasMany(AttendanceMonthDetail::class);
    }
}

