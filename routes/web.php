<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend']);
});

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return response()->json(['output' => Artisan::output()]);
});

Route::get('/create-test-user', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $user = App\Models\User::updateOrCreate(
            ['email' => 'test@mitrad.com'],
            ['name' => 'Test User', 'password' => bcrypt('mitrad123')]
        );
        return response()->json(['message' => 'OK', 'email' => $user->email, 'id' => $user->id]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);