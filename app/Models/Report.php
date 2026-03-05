<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['token', 'data', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
