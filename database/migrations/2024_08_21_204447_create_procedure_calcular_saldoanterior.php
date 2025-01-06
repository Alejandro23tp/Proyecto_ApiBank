<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureCalcularSaldoanterior extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS calcular_saldoanterior;');
        DB::unprepared('
            CREATE OR REPLACE FUNCTION calcular_saldoanterior(IN record_id BIGINT)
            RETURNS VOID AS $$
            DECLARE
                saldoanterior_anterior DECIMAL(10, 2) DEFAULT 0;
                total_semana DECIMAL(10, 2);
                total_interes DECIMAL(10, 2);
                total_prestamos DECIMAL(10, 2);
                nuevo_saldoanterior DECIMAL(10, 2);
            BEGIN
                -- Obtener los valores de la fila actual
                SELECT COALESCE(totalsemana, 0), COALESCE(totalinteres, 0), COALESCE(totalprestamos, 0)
                INTO total_semana, total_interes, total_prestamos
                FROM presentar_semanas
                WHERE id = record_id;

                -- Obtener el saldoanterior de la fila anterior (si existe)
                SELECT COALESCE(saldoanterior, 0) INTO saldoanterior_anterior
                FROM presentar_semanas
                WHERE id < record_id
                ORDER BY id DESC
                LIMIT 1;

                -- Calcular el nuevo saldoanterior
                nuevo_saldoanterior := (total_semana + saldoanterior_anterior + total_interes) - total_prestamos;

                -- Actualizar la fila con el nuevo saldoanterior
                UPDATE presentar_semanas
                SET saldoanterior = nuevo_saldoanterior
                WHERE id = record_id;
            END;
            $$ LANGUAGE plpgsql;
        ');
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS calcular_saldoanterior;');
    }
}
