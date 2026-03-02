<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;

Route::get('/', function () {
    return response()->json(['status' => 'ok', 'app' => 'MITrad Backend']);
});

Route::get('/setup', function () {
    try {
        $results = [];
        $tables = [
            'trading_accounts' => "CREATE TABLE IF NOT EXISTS trading_accounts (id bigserial primary key, user_id bigint not null, name varchar(255) not null, capital numeric(15,2) default 10000, broker varchar(255) default '', type varchar(255) default 'Personnel', created_at timestamp, updated_at timestamp)",
            'trades' => "CREATE TABLE IF NOT EXISTS trades (id bigserial primary key, trading_account_id bigint not null, pair varchar(255), direction varchar(255), entry_price numeric(15,5), exit_price numeric(15,5), lot_size numeric(15,5), pnl numeric(15,2), rr numeric(15,2), status varchar(255) default 'WIN', note text, trade_date date, created_at timestamp, updated_at timestamp, is_imported boolean default false)",
            'daily_analyses' => "CREATE TABLE IF NOT EXISTS daily_analyses (id bigserial primary key, user_id bigint not null, title varchar(255), content text, date date, created_at timestamp, updated_at timestamp)",
            'objectives' => "CREATE TABLE IF NOT EXISTS objectives (id bigserial primary key, user_id bigint not null, title varchar(255), description text, target numeric(15,2), current numeric(15,2) default 0, completed boolean default false, created_at timestamp, updated_at timestamp)",
            'successes' => "CREATE TABLE IF NOT EXISTS successes (id bigserial primary key, user_id bigint not null, title varchar(255), description text, date date, created_at timestamp, updated_at timestamp)",
        ];
        foreach ($tables as $name => $sql) {
            \DB::statement($sql);
            $results[$name] = 'OK';
        }
        return response()->json(['message' => 'Setup done', 'tables' => $results]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/create-test-user', function () {
    try {
        $user = App\Models\User::updateOrCreate(
            ['email' => 'test@mitrad.com'],
            ['name' => 'Test User', 'password' => bcrypt('mitrad123'), 'password_set' => true]
        );
        return response()->json(['message' => 'OK', 'email' => $user->email, 'id' => $user->id]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/create-user/{name}/{email}/{password}', function ($name, $email, $password) {
    try {
        $user = App\Models\User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt($password), 'password_set' => true]
        );
        return response()->json(['message' => 'OK', 'email' => $user->email, 'id' => $user->id]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect']);
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);
Route::get('/debug-db', function () {
    try {
        $conn = DB::connection()->getPdo();
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public'");
        return response()->json(['status' => 'connected', 'tables' => array_column($tables, 'tablename')]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
Route::get('/fix-tables', function () {
    try {
        $results = [];
        $alterations = [
            "ALTER TABLE trading_accounts ADD COLUMN IF NOT EXISTS capital numeric(15,2) default 10000",
            "ALTER TABLE trading_accounts ADD COLUMN IF NOT EXISTS broker varchar(255) default ''",
            "ALTER TABLE trading_accounts ADD COLUMN IF NOT EXISTS type varchar(255) default 'Personnel'",
            "ALTER TABLE trading_accounts ADD COLUMN IF NOT EXISTS broker_type varchar(255) default ''",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS pair varchar(255)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS direction varchar(255)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS entry_price numeric(15,5)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS exit_price numeric(15,5)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS lot_size numeric(15,5)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS pnl numeric(15,2)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS rr numeric(15,2)",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS status varchar(255) default 'WIN'",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS note text",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS trade_date date",
            "ALTER TABLE trades ADD COLUMN IF NOT EXISTS is_imported boolean default false",
        ];
        foreach ($alterations as $sql) {
            DB::statement($sql);
            $results[] = $sql;
        }
        return response()->json(['message' => 'Done', 'applied' => count($results)]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});