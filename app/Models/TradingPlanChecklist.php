<?php
// FICHIER : app/Models/TradingPlanChecklist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingPlanChecklist extends Model
{
    protected $fillable = ['user_id', 'date', 'checked_ids'];

    protected $casts = [
        'checked_ids' => 'array',
        'date'        => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
