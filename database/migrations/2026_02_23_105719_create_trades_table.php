<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->string('symbol');
            $table->enum('type', ['buy', 'sell']);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('entry_price', 15, 5);
            $table->decimal('exit_price', 15, 5)->nullable();
            $table->decimal('quantity', 15, 5);
            $table->decimal('pnl', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};