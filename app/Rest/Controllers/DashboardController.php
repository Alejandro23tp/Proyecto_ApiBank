<?php

namespace App\Rest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function obtenerDashboardStats()
    {
        try {
            $stats = DB::select("
                SELECT
                    (SELECT COUNT(*) FROM participante p) AS total_participantes,
                    (SELECT COALESCE(SUM(pp_prestamo), 0) FROM prestamos_participante WHERE estado = 'Pendiente') AS total_prestamos,
                    (SELECT COALESCE(SUM(valor), 0) FROM pagos) AS total_pagos,
                    (SELECT COUNT(*) FROM prestamos_participante WHERE estado = 'Pendiente') AS prestamos_pendientes
            ");

            return response()->json([
                'status' => 'success',
                'data' => $stats[0]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerDashboardStats: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerUltimasTransacciones()
    {
        try {
            $transacciones = DB::select("
                SELECT
                    pagos.id,
                    pagos.created_at,
                    pagos.valor AS monto,
                    participante.part_nombre AS participante
                FROM pagos
                JOIN prestamos_participante ON pagos.prestpart_id = prestamos_participante.id
                JOIN participante ON prestamos_participante.pp_partId = participante.id
                ORDER BY pagos.created_at DESC
                LIMIT 10
            ");

            return response()->json([
                'status' => 'success',
                'data' => $transacciones
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerUltimasTransacciones: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener transacciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerParticipantesDeudores()
    {
        try {
            $deudores = DB::select("
                SELECT
                    pp.id,
                    p.part_nombre AS nombre,
                    pp.pp_prestamo AS monto_total,
                    COALESCE(SUM(pg.valor), 0) AS monto_pagado,
                    (pp.pp_prestamo - COALESCE(SUM(pg.valor), 0)) AS monto_restante
                FROM prestamos_participante pp
                JOIN participante p ON pp.pp_partId = p.id
                LEFT JOIN pagos pg ON pp.id = pg.prestpart_id AND pp.pp_semana = pg.semana
                WHERE pp.estado = 'Pendiente'
                GROUP BY pp.id, p.part_nombre, pp.pp_prestamo
                ORDER BY pp.id
            ");

            return response()->json([
                'status' => 'success',
                'data' => $deudores
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerParticipantesDeudores: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener deudores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerIntereses()
    {
        try {
            $intereses = DB::select("
                SELECT
                    pp.id,
                    p.part_nombre AS nombre,
                    pp.pp_prestamo AS prestamo,
                    pp.interes,
                    TO_CHAR(pp.fecha_pago + INTERVAL '1 month', 'Month') AS mes_de_pago,
                    SUM(pp.interes) OVER() AS total_interes,
                    (SUM(pp.interes) OVER()) / (SELECT SUM(part_cupos) FROM participante) AS interes_por_accion
                FROM prestamos_participante pp
                JOIN participante p ON pp.pp_partId = p.id
                GROUP BY pp.id, p.part_nombre, pp.pp_prestamo, pp.interes, pp.fecha_pago
                ORDER BY pp.id
            ");

            return response()->json([
                'status' => 'success',
                'data' => $intereses
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerIntereses: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener los intereses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
