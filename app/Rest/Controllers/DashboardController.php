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
    /**
     * @return JsonResponse
     */
    public function obtenerDashboardStats(): JsonResponse
    {
        try {
            $totalParticipantes = Participantes::count();
            $totalPrestamosActivos = PrestamosParticipante::where('pres_estado', 'Activo')->count();
            $totalPagosRealizados = Pagos::sum('pago_valor');

            return response()->json([
                'totalParticipantes' => $totalParticipantes,
                'totalPrestamosActivos' => $totalPrestamosActivos,
                'totalPagosRealizados' => $totalPagosRealizados,
            ]);
        } catch (Throwable $e) {
            Log::error('Error in obtenerDashboardStats: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function obtenerUltimasTransacciones(): JsonResponse
    {
        try {
            $ultimasTransacciones = Pagos::with('prestamoParticipante.participante')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return response()->json($ultimasTransacciones);
        } catch (Throwable $e) {
            Log::error('Error in obtenerUltimasTransacciones: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function obtenerParticipantesDeudores(): JsonResponse
    {
        try {
            $participantesDeudores = Participantes::select('participantes.*')
                ->leftJoin('prestamos_participantes', 'participantes.part_id', '=', 'prestamos_participantes.part_id')
                ->where('prestamos_participantes.pres_estado', 'Activo')
                ->whereColumn('prestamos_participantes.pres_valor_pendiente', '>', '0')
                ->groupBy('participantes.part_id')
                ->get();

            return response()->json($participantesDeudores);
        } catch (Throwable $e) {
            Log::error('Error in obtenerParticipantesDeudores: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}