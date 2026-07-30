<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attendance_batch_id',
        'employee_id',
        'attendance_status',
        'check_in',
        'check_out',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the batch that owns the attendance record.
     */
    public function attendanceBatch(): BelongsTo
    {
        return $this->belongsTo(AttendanceBatch::class);
    }

    /**
     * Get the employee associated with the attendance record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
