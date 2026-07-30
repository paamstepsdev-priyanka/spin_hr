<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user associated with this mapping.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company associated with this mapping.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
