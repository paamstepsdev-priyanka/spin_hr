<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'logo',
        'email',
        'contact_no',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'pf_applicable',
        'status',
    ];
}
