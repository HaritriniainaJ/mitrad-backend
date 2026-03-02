<?php
// FICHIER : database/migrations/xxxx_xx_xx_create_trading_plan_tables.php
// Commande pour créer : php artisan make:migration create_trading_plan_tables

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des règles du plan de trading
        Schema::create('trading_plan_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('images')->nullable();  // tableau base64
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Table de la checklist quotidienne
        Schema::create('trading_plan_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->json('checked_ids')->nullable(); // tableau d'IDs de règles cochées
            $table->timestamps();

            $table->unique(['user_id', 'date']); // une seule checklist par user par jour
        });

        // Ajout colonne plan_respected sur trades
        Schema::table('trades', function (Blueprint $table) {
            $table->boolean('plan_respected')->nullable()->default(null)->after('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_plan_checklists');
        Schema::dropIfExists('trading_plan_rules');
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn('plan_respected');
        });
    }
};
