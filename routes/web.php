<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return Artisan::output();
});
Route::get('/test-discord', function () {
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->get('https://discord.com/api/v10/gateway');
        return response()->json(['status' => $response->status(), 'body' => $response->json()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/', function () {
    return view('welcome');
});

// Discord OAuth — doit être dans web.php (besoin de session)
Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);