<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCedulaToParticipantesTable extends Migration
{
    public function up()
    {
        Schema::table('participante', function (Blueprint $table) {
            $table->string('part_cedula', 20)->nullable()->unique()->after('part_nombre');
        });
    }

    public function down()
    {
        Schema::table('participante', function (Blueprint $table) {
            $table->dropColumn('part_cedula');
        });
    }
}
