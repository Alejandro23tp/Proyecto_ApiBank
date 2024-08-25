<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presentar_semanas', function (Blueprint $table) {
            $table->id();
            $table->string('semana')->nullable();
            $table->integer('totalsemana')->nullable();
            $table->integer('totalprestamos')->nullable();
            $table->decimal('saldoanterior', 10, 2)->nullable();
            $table->decimal('totalinteres', 10, 2)->nullable();
            $table->decimal('prestamospagado', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentar_semanas');
    }
};
