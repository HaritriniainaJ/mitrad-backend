<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── USERS ──────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'discord_id'))    $table->string('discord_id')->nullable()->after('email');
            if (!Schema::hasColumn('users', 'avatar'))        $table->text('avatar')->nullable();
            if (!Schema::hasColumn('users', 'password_set'))  $table->boolean('password_set')->default(false);
            if (!Schema::hasColumn('users', 'bio'))           $table->text('bio')->nullable();
            if (!Schema::hasColumn('users', 'country'))       $table->string('country')->nullable();
            if (!Schema::hasColumn('users', 'experience'))    $table->string('experience')->nullable();
            if (!Schema::hasColumn('users', 'trading_style')) $table->string('trading_style')->nullable();
            if (!Schema::hasColumn('users', 'broker'))        $table->string('broker')->nullable();
            if (!Schema::hasColumn('users', 'banner'))        $table->text('banner')->nullable();
            if (!Schema::hasColumn('users', 'is_public'))     $table->boolean('is_public')->default(false);
            if (!Schema::hasColumn('users', 'favorite_pairs'))$table->json('favorite_pairs')->nullable();
            if (!Schema::hasColumn('users', 'custom_setups')) $table->json('custom_setups')->nullable();
        });

        // ── TRADING ACCOUNTS ───────────────────────────────────────────────
        Schema::dropIfExists('trading_plan_checklists');
        Schema::dropIfExists('trades');
        Schema::dropIfExists('trading_accounts');

        Schema::create('trading_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('capital', 15, 2)->default(10000);
            $table->string('broker')->default('');
            $table->string('type')->default('Personnel');
            $table->string('broker_type')->default('');
            $table->timestamps();
        });

        // ── TRADES ─────────────────────────────────────────────────────────
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->string('pair')->nullable();
            $table->string('direction')->nullable();
            $table->string('session')->nullable();
            $table->string('setup')->nullable();
            $table->string('emotion')->nullable();
            $table->integer('quality')->nullable();
            $table->decimal('entry_price', 15, 5)->nullable();
            $table->decimal('stop_loss', 15, 5)->nullable();
            $table->decimal('take_profit', 15, 5)->nullable();
            $table->decimal('exit_price', 15, 5)->nullable();
            $table->decimal('lot_size', 15, 5)->nullable();
            $table->decimal('result_r', 8, 2)->nullable();
            $table->decimal('result_dollar', 10, 2)->nullable();
            $table->decimal('pnl', 15, 2)->nullable();
            $table->string('status')->default('WIN');
            $table->boolean('plan_respected')->nullable()->default(null);
            $table->string('date')->nullable();
            $table->date('trade_date')->nullable();
            $table->decimal('rr', 15, 2)->nullable();
            $table->text('note')->nullable();
            $table->text('entry_note')->nullable();
            $table->text('exit_note')->nullable();
            $table->string('trading_view_link')->nullable();
            $table->longText('screenshot')->nullable();
            $table->boolean('is_imported')->default(false);
            $table->timestamps();
        });

        // ── DAILY ANALYSES ─────────────────────────────────────────────────
        Schema::dropIfExists('daily_analyses');
        Schema::create('daily_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('title')->nullable();
            $table->json('pairs');
            $table->timestamps();
        });

        // ── TRADING PLAN ───────────────────────────────────────────────────
        Schema::dropIfExists('trading_plan_rules');
        Schema::create('trading_plan_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('trading_plan_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->json('checked_ids')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });

        // ── OBJECTIVES ─────────────────────────────────────────────────────
        Schema::dropIfExists('objectives');
        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('text');
            $table->text('description')->nullable();
            $table->date('target_date')->nullable();
            $table->boolean('completed')->default(false);
            $table->text('image')->nullable();
            $table->timestamps();
        });

        // ── SUCCESSES ──────────────────────────────────────────────────────
        Schema::dropIfExists('successes');
        Schema::create('successes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('date');
            $table->text('note')->nullable();
            $table->json('images')->nullable();
            $table->string('type')->default('manual');
            $table->string('badge_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_plan_checklists');
        Schema::dropIfExists('trading_plan_rules');
        Schema::dropIfExists('trades');
        Schema::dropIfExists('trading_accounts');
        Schema::dropIfExists('daily_analyses');
        Schema::dropIfExists('objectives');
        Schema::dropIfExists('successes');
    }
};