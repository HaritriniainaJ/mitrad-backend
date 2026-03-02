<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->enum('status', ['RUNNING', 'WIN', 'LOSS', 'BE'])->default('RUNNING')->change();
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->enum('status', ['open', 'closed'])->default('open')->change();
        });
    }
};