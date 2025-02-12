<?php

namespace App\Rest\Controllers;

use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use App\Models\Pagos;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends BaseController
{
    public function obtenerDashboardStats(): JsonResponse
    {
        try {
            $totalParticipantes = Participantes::count();
            // Corregido: usando estado en lugar de pres_estado
            $totalPrestamosActivos = DB::table('prestamos_participante')
                ->where('estado', 'Activo')
                ->count();
            $totalPagosRealizados = DB::table('pagos')
                ->sum('pago_valor');

            return response()->json([
                'mensaje' => 'Estadísticas del Dashboard obtenidas',
                'cant' => 1,
                'data' => [
                    'totalParticipantes' => $totalParticipantes,
                    'totalPrestamosActivos' => $totalPrestamosActivos,
                    'totalPagosRealizados' => $totalPagosRealizados,
                ]
            ]);
        } catch (Throwable $e) {
            Log::error('Error in obtenerDashboardStats: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener estadísticas del dashboard',
                'cant' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerUltimasTransacciones(): JsonResponse
    {
        try {
            // Corregido: usando una consulta más directa sin relaciones
            $ultimasTransacciones = DB::table('pagos')
                ->join('prestamos_participante', 'pagos.pres_id', '=', 'prestamos_participante.pres_id')
                ->join('participantes', 'prestamos_participante.part_id', '=', 'participantes.part_id')
                ->select('pagos.*', 'participantes.part_nombres', 'participantes.part_apellidos')
                ->orderBy('pagos.created_at', 'desc')
                ->take(5)
                ->get();

            return response()->json([
                'mensaje' => 'Últimas transacciones obtenidas',
                'cant' => 1,
                'data' => $ultimasTransacciones
            ]);
        } catch (Throwable $e) {
            Log::error('Error in obtenerUltimasTransacciones: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener las últimas transacciones',
                'cant' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerParticipantesDeudores(): JsonResponse
    {
        try {
            // Corregido: usando nombres de tabla correctos y consulta simplificada
            $participantesDeudores = DB::table('participantes')
                ->join('prestamos_participante', 'participantes.part_id', '=', 'prestamos_participante.part_id')
                ->where('prestamos_participante.estado', 'Activo')
                ->where('prestamos_participante.valor_pendiente', '>', 0)
                ->select('participantes.*')
                ->distinct()
                ->get();

            return response()->json([
                'mensaje' => 'Participantes deudores obtenidos',
                'cant' => 1,
                'data' => $participantesDeudores
            ]);
        } catch (Throwable $e) {
            Log::error('Error in obtenerParticipantesDeudores: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener los participantes deudores',
                'cant' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}