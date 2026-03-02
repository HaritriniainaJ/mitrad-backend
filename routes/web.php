<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\DiscordAuthController;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend v2']);
});

Route::get('/run-migrations', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json(['status' => 'ok', 'output' => Artisan::output()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/debug-db', function () {
    try {
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public'");
        return response()->json(['tables' => array_column($tables, 'tablename')]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);