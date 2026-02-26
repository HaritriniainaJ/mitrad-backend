<?php
// FICHIER : app/Models/Trade.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'trading_account_id',
        'pair', 'direction', 'session', 'setup', 'emotion', 'quality',
        'entry_price', 'stop_loss', 'take_profit', 'lot_size',
        'exit_price', 'result_r', 'result_dollar',
        'status', 'plan_respected',   // ← NOUVEAU
        'date',
        'entry_note', 'exit_note', 'trading_view_link', 'screenshot', 'is_imported',
    ];

    protected $casts = [
        'plan_respected' => 'boolean',
    ];

    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }
}
