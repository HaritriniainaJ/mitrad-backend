<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend']);
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);