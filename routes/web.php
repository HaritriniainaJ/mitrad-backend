<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return Artisan::output();
});

Route::get('/', function () {
    return view('welcome');
});

// Discord OAuth — doit être dans web.php (besoin de session)
Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);