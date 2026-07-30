<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceMonthDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attendance_month_id',
        'employee_id',
        'total_days',
        'leave_taken',
        'net_present',
        'leave_not_deducted',
        'payable_days',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the monthly attendance master record.
     */
    public function attendanceMonth(): BelongsTo
    {
        return $this->belongsTo(AttendanceMonth::class, 'attendance_month_id');
    }

    /**
     * Get the employee associated with the attendance detail.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
