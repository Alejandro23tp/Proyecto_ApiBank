<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddActualizarPrestamosPagadosTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER actualizar_prestamos_pagados
            AFTER UPDATE ON prestamos_participante
            FOR EACH ROW
            BEGIN
                -- Solo proceder si el estado es "Cancelado"
                IF NEW.estado = "Cancelado" THEN
                    -- Actualizar la suma de prestamos pagados en la tabla presentar_semanas
                    UPDATE presentar_semanas
                    SET prestamospagado = (
                        SELECT COALESCE(SUM(pp_prestamo), 0)
                        FROM prestamos_participante
                        WHERE pp_semana = NEW.pp_semana
                          AND estado = "Cancelado"
                    )
                    WHERE semana = NEW.pp_semana;
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_prestamos_pagados');
    }
}

