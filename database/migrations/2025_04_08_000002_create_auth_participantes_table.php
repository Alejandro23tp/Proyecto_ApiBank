<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthParticipantesTable extends Migration
{
    public function up()
    {
        Schema::create('auth_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participante_id')->constrained('participante')->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auth_participantes');
    }
}
