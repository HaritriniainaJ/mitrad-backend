<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('successes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('date');
            $table->text('note')->nullable();
            $table->json('images')->nullable();
            $table->string('type')->default('manual'); // manual | auto
            $table->string('badge_key')->nullable();   // clé unique pour éviter les doublons auto
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('successes');
    }
};