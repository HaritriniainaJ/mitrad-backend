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
    'password',
    'password_set',
    'discord_id',
    'avatar',
    'bio',
    'country',
    'experience',
    'trading_style',
    'broker',
    'banner',
    'is_public',
    'is_active',
    'is_admin',
    'favorite_pairs',
    'custom_setups',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'password_set' => 'boolean',
    'is_active' => 'boolean',
    'is_admin' => 'boolean',
    'is_public' => 'boolean',
];

    public function tradingAccounts()
    {
        return $this->hasMany(TradingAccount::class);
    }
}