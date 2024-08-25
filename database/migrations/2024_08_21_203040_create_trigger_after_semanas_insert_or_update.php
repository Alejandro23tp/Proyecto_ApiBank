<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTriggerAfterSemanasInsertOrUpdate extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER after_semanas_insert_or_update
            AFTER INSERT ON semanas
            FOR EACH ROW
            BEGIN
                DECLARE total_valor DECIMAL(10,2);

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
            END
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
            DROP TRIGGER IF EXISTS after_semanas_insert_or_update;
        ');
    }
}
