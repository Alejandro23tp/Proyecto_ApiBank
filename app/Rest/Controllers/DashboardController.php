<?php

namespace App\Rest\Controllers;

use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use App\Models\Pagos;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function obtenerDashboardStats(): JsonResponse
    {
        $totalParticipantes = Participantes::count();
        $totalPrestamosActivos = PrestamosParticipante::where('pres_estado', 'Activo')->count();
        $totalPagosRealizados = Pagos::sum('pago_valor');

        return response()->json([
            'totalParticipantes' => $totalParticipantes,
            'totalPrestamosActivos' => $totalPrestamosActivos,
            'totalPagosRealizados' => $totalPagosRealizados,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function obtenerUltimasTransacciones(): JsonResponse
    {
        $ultimasTransacciones = Pagos::with('prestamoParticipante.participante')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($ultimasTransacciones);
    }

    /**
     * @return JsonResponse
     */
    public function obtenerParticipantesDeudores(): JsonResponse
    {
        $participantesDeudores = Participantes::select('participantes.*')
            ->leftJoin('prestamos_participantes', 'participantes.part_id', '=', 'prestamos_participantes.part_id')
            ->where('prestamos_participantes.pres_estado', 'Activo')
            ->whereColumn('prestamos_participantes.pres_valor_pendiente', '>', '0')
            ->groupBy('participantes.part_id')
            ->get();

        return response()->json($participantesDeudores);
    }
}