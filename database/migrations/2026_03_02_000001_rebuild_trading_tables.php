<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Trading Accounts
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
            $table->decimal('pnl', 15, 2)->nullable();
            $table->string('status')->default('WIN');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
        Schema::dropIfExists('trading_accounts');
    }
};