<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyAnalysis extends Model {
    protected $fillable = ['user_id', 'date', 'title', 'pairs'];
    protected $casts = ['pairs' => 'array'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}