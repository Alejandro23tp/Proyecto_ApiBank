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
        Schema::create('prestamos_participante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pp_partId')->constrained('participante')->onDelate('cascade');
            $table->string('pp_semana');
            $table->integer('pp_prestamo');
            $table->float('interes');
            $table->string('estado');
            $table->date('fecha_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos_participante');
    }
};
