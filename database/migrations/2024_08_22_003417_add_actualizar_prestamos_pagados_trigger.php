<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddActualizarPrestamosPagadosTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear función para el trigger
        DB::unprepared('
            CREATE OR REPLACE FUNCTION actualizar_prestamos_pagados()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Solo proceder si el estado es \'Cancelado\'
                IF NEW.estado = \'Cancelado\' THEN
                    -- Actualizar la suma de prestamos pagados en la tabla presentar_semanas
                    UPDATE presentar_semanas
                    SET prestamospagado = (
                        SELECT COALESCE(SUM(pp_prestamo), 0)
                        FROM prestamos_participante
                        WHERE pp_semana = NEW.pp_semana
                          AND estado = \'Cancelado\'
                    )
                    WHERE semana = NEW.pp_semana;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear trigger
        DB::unprepared('
            CREATE TRIGGER actualizar_prestamos_pagados
            AFTER UPDATE ON prestamos_participante
            FOR EACH ROW
            EXECUTE FUNCTION actualizar_prestamos_pagados();
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_prestamos_pagados ON prestamos_participante;');
        DB::unprepared('DROP FUNCTION IF EXISTS actualizar_prestamos_pagados();');
    }
}
