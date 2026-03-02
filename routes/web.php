<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend']);
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);
Route::get('/auth/discord/redirect', [App\Http\Controllers\DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [App\Http\Controllers\DiscordAuthController::class, 'callback']);
Route::get('/create-test-user', function () {
    $user = App\Models\User::updateOrCreate(
        ['email' => 'test@mitrad.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('mitrad123'),
            'password_set' => true,
        ]
    );
    return response()->json(['message' => 'User created', 'email' => $user->email]);
});