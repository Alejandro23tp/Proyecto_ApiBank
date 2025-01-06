<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActualizarEstadoPrestamoTrigger extends Migration
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
            CREATE OR REPLACE FUNCTION actualizar_estado_prestamo()
            RETURNS TRIGGER AS $$
            DECLARE
                total_pagado DECIMAL(10, 2);
                monto_total DECIMAL(10, 2);
            BEGIN
                -- Sumar todos los pagos relacionados con el prestpart_id actual
                SELECT SUM(valor) INTO total_pagado
                FROM pagos
                WHERE prestpart_id = NEW.prestpart_id;

                -- Obtener el monto del préstamo original más el interés
                SELECT (pp_prestamo + interes) INTO monto_total
                FROM prestamos_participante
                WHERE id = NEW.prestpart_id;

                -- Verificar si el total pagado es igual o superior al monto del préstamo más el interés
                IF total_pagado >= monto_total THEN
                    -- Actualizar el estado a \'Cancelado\'
                    UPDATE prestamos_participante
                    SET estado = \'Cancelado\'
                    WHERE id = NEW.prestpart_id;
                ELSE
                    -- Actualizar el estado a \'Pendiente\'
                    UPDATE prestamos_participante
                    SET estado = \'Pendiente\'
                    WHERE id = NEW.prestpart_id;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear trigger
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_prestamo
            AFTER INSERT ON pagos
            FOR EACH ROW
            EXECUTE FUNCTION actualizar_estado_prestamo();
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_prestamo ON pagos;');
        DB::unprepared('DROP FUNCTION IF EXISTS actualizar_estado_prestamo();');
    }
}
