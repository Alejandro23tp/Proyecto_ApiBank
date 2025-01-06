<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerAfterSemanasInsertOrUpdate extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        // Crear función para el trigger
        DB::unprepared('
            CREATE OR REPLACE FUNCTION after_semanas_insert_or_update()
            RETURNS TRIGGER AS $$
            DECLARE
                total_valor DECIMAL(10, 2);
            BEGIN
                -- Calcular la suma total de "valor" para el "nombre_semana" de la fila agregada o editada
                SELECT SUM(valor) INTO total_valor
                FROM semanas
                WHERE nombre_semana = NEW.nombre_semana;

                -- Verificar si el "nombre_semana" ya existe en la tabla "presentar_semanas"
                IF EXISTS (SELECT 1 FROM presentar_semanas WHERE semana = NEW.nombre_semana) THEN
                    -- Actualizar el total de la semana existente
                    UPDATE presentar_semanas
                    SET totalsemana = total_valor
                    WHERE semana = NEW.nombre_semana;
                ELSE
                    -- Insertar un nuevo registro
                    INSERT INTO presentar_semanas (semana, totalsemana)
                    VALUES (NEW.nombre_semana, total_valor);
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear trigger
        DB::unprepared('
            CREATE TRIGGER after_semanas_insert_or_update
            AFTER INSERT ON semanas
            FOR EACH ROW
            EXECUTE FUNCTION after_semanas_insert_or_update();
        ');
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS after_semanas_insert_or_update ON semanas;
        ');
        DB::unprepared('
            DROP FUNCTION IF EXISTS after_semanas_insert_or_update();
        ');
    }
}
