<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend']);
});

Route::get('/create-test-user', function () {
    try {
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_set boolean DEFAULT false");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS discord_id varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS bio text DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS country varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS experience varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS trading_style varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS broker varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS banner varchar(255) DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_public boolean DEFAULT false");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS favorite_pairs text DEFAULT NULL");
        \DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS custom_setups text DEFAULT NULL");
        $user = App\Models\User::updateOrCreate(
            ['email' => 'test@mitrad.com'],
            ['name' => 'Test User', 'password' => bcrypt('mitrad123'), 'password_set' => true]
        );
        return response()->json(['message' => 'OK', 'email' => $user->email, 'id' => $user->id]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);