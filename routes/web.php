<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\DiscordAuthController;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend']);
});

Route::get('/run-migrations', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        return response()->json(['status' => 'ok', 'output' => $output]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/debug-db', function () {
    try {
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public'");
        return response()->json(['status' => 'connected', 'tables' => array_column($tables, 'tablename')]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);