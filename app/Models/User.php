<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'phone',
        'is_active',
        'current_session_id',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'current_session_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApoteker(): bool
    {
        return $this->role === 'apoteker';
    }

    public function isAsisten(): bool
    {
        return $this->role === 'asisten_apoteker';
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }
}
