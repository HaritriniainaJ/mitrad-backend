<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model {
    protected $fillable = ['user_id', 'text', 'description', 'target_date', 'completed', 'image'];
    protected $casts = ['completed' => 'boolean'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}