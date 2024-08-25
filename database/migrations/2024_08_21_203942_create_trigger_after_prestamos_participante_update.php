<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerAfterPrestamosParticipanteUpdate extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER after_prestamos_participante_update
            AFTER UPDATE ON prestamos_participante
            FOR EACH ROW
            BEGIN
                DECLARE total_prestamo DECIMAL(10,2);
                DECLARE total_interes DECIMAL(10,2);

                -- Calcular la suma total de "pp_prestamo" para la "pp_semana" después de la actualización
                SELECT SUM(pp_prestamo) INTO total_prestamo
                FROM prestamos_participante
                WHERE pp_semana = NEW.pp_semana;

                -- Calcular la suma total de "interes" solo para los registros con "estado" = "Cancelado"
                SELECT SUM(interes) INTO total_interes
                FROM prestamos_participante
                WHERE pp_semana = NEW.pp_semana AND estado = "Cancelado";

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
            DROP TRIGGER IF EXISTS after_prestamos_participante_update;
        ');
    }
}
