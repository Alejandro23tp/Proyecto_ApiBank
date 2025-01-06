<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerAfterPrestamosParticipanteUpdate extends Migration
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
            CREATE OR REPLACE FUNCTION after_prestamos_participante_update()
            RETURNS TRIGGER AS $$
            DECLARE
                total_prestamo DECIMAL(10, 2);
                total_interes DECIMAL(10, 2);
            BEGIN
                -- Calcular la suma total de "pp_prestamo" para la "pp_semana" después de la actualización
                SELECT SUM(pp_prestamo) INTO total_prestamo
                FROM prestamos_participante
                WHERE pp_semana = NEW.pp_semana;

                -- Calcular la suma total de "interes" solo para los registros con "estado" = \'Cancelado\'
                SELECT SUM(interes) INTO total_interes
                FROM prestamos_participante
                WHERE pp_semana = NEW.pp_semana AND estado = \'Cancelado\';

                -- Verificar si la "pp_semana" ya existe en la tabla "presentar_semanas"
                IF EXISTS (SELECT 1 FROM presentar_semanas WHERE semana = NEW.pp_semana) THEN
                    -- Actualizar el total de la semana existente
                    UPDATE presentar_semanas
                    SET totalprestamos = total_prestamo,
                        totalinteres = total_interes
                    WHERE semana = NEW.pp_semana;
                ELSE
                    -- Insertar un nuevo registro
                    INSERT INTO presentar_semanas (semana, totalprestamos, totalinteres)
                    VALUES (NEW.pp_semana, total_prestamo, total_interes);
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear trigger
        DB::unprepared('
            CREATE TRIGGER after_prestamos_participante_update
            AFTER UPDATE ON prestamos_participante
            FOR EACH ROW
            EXECUTE FUNCTION after_prestamos_participante_update();
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
            DROP TRIGGER IF EXISTS after_prestamos_participante_update ON prestamos_participante;
        ');
        DB::unprepared('
            DROP FUNCTION IF EXISTS after_prestamos_participante_update();
        ');
    }
}
