<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user company mapping records.
     */
    public function userCompanies()
    {
        return $this->hasMany(UserCompany::class);
    }

    /**
     * Get companies accessible by the user.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_companies', 'user_id', 'company_id')
            ->withPivot('is_default', 'status')
            ->withTimestamps();
    }

    /**
     * Get the employee profile linked to this user account.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Check if the user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']) || $this->email === 'admin@gmail.com';
    }
}
