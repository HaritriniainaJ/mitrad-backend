<?php
// FICHIER : app/Models/TradingPlanRule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingPlanRule extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'images', 'order'];

    protected $casts = [
        'images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
