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
        Schema::create('semanas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_semana');
            $table->foreignId('part_id')->constrained('participante')->onDelete('cascade');
            $table->date('fecha_pago');
            $table->integer('valor');
            $table->string('responsable');
            $table->date('inicioSemana');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semanas');
    }
};
