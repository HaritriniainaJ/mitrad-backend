<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('avatar');
            $table->string('country')->nullable()->after('bio');
            $table->string('experience')->nullable()->after('country');
            $table->string('trading_style')->nullable()->after('experience');
            $table->string('broker')->nullable()->after('trading_style');
            $table->text('banner')->nullable()->after('broker');
            $table->boolean('is_public')->default(false)->after('banner');
            $table->json('favorite_pairs')->nullable()->after('is_public');
            $table->json('custom_setups')->nullable()->after('favorite_pairs');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio', 'country', 'experience', 'trading_style',
                'broker', 'banner', 'is_public', 'favorite_pairs', 'custom_setups'
            ]);
        });
    }
};