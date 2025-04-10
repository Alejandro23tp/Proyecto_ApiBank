<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameToAuthParticipantesTable extends Migration
{
    public function up()
    {
        Schema::table('auth_participantes', function (Blueprint $table) {
            $table->string('username')->unique()->after('email');
        });
    }

    public function down()
    {
        Schema::table('auth_participantes', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
