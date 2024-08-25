<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateActualizarEstadoPrestamoTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_prestamo
            AFTER INSERT ON pagos
            FOR EACH ROW
            BEGIN
                DECLARE total_pagado DECIMAL(10, 2);
                DECLARE monto_total DECIMAL(10, 2);  -- Corregido: nombre de la variable

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
                    -- Actualizar el estado a "Cancelado"
                    UPDATE prestamos_participante
                    SET estado = "Cancelado"
                    WHERE id = NEW.prestpart_id;
                ELSE
                    -- Actualizar el estado a "Pendiente"
                    UPDATE prestamos_participante
                    SET estado = "Pendiente"
                    WHERE id = NEW.prestpart_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_prestamo');
    }
}
