<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
    protected $fillable = ['user_id', 'name', 'capital', 'broker', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }
}