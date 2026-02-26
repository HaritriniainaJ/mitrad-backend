<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            // Supprimer les anciens champs
            $table->dropColumn(['symbol', 'type', 'quantity', 'pnl', 'notes', 'opened_at', 'closed_at']);

            // Ajouter les nouveaux champs
            $table->string('pair')->after('trading_account_id');
            $table->enum('direction', ['BUY', 'SELL'])->after('pair');
            $table->string('session')->nullable()->after('direction');
            $table->string('setup')->nullable()->after('session');
            $table->string('emotion')->nullable()->after('setup');
            $table->integer('quality')->nullable()->after('emotion');
            $table->decimal('stop_loss', 15, 5)->after('entry_price');
            $table->decimal('take_profit', 15, 5)->nullable()->after('stop_loss');
            $table->decimal('lot_size', 10, 4)->nullable()->after('take_profit');
            $table->decimal('result_r', 8, 2)->nullable()->after('exit_price');
            $table->decimal('result_dollar', 10, 2)->nullable()->after('result_r');
            $table->string('date')->nullable()->after('result_dollar');
            $table->text('entry_note')->nullable()->after('date');
            $table->text('exit_note')->nullable()->after('entry_note');
            $table->string('trading_view_link')->nullable()->after('exit_note');
            $table->longText('screenshot')->nullable()->after('trading_view_link');
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn([
                'pair', 'direction', 'session', 'setup', 'emotion', 'quality',
                'stop_loss', 'take_profit', 'lot_size', 'result_r', 'result_dollar',
                'date', 'entry_note', 'exit_note', 'trading_view_link', 'screenshot',
            ]);
        });
    }
};