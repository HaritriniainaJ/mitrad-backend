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

Route::get('/test-oauth', function () {
    $code = request()->get('code', 'test');
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(15)
            ->asForm()
            ->post('https://discord.com/api/oauth2/token', [
                'client_id' => env('DISCORD_CLIENT_ID'),
                'client_secret' => env('DISCORD_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => env('DISCORD_REDIRECT_URI'),
            ]);
        return response()->json(['status' => $response->status(), 'body' => $response->json()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'time' => microtime(true)]);
    }
});

Route::get('/', function () {
    return view('welcome');
});

// Discord OAuth — doit être dans web.php (besoin de session)
Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);