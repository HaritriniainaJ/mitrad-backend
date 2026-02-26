<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Success extends Model {
    protected $fillable = ['user_id', 'title', 'date', 'note', 'images', 'type', 'badge_key'];
    protected $casts = ['images' => 'array'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}