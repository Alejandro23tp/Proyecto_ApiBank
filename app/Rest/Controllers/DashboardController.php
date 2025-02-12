<?php

namespace App\Rest\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pagos;
use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function obtenerDashboardStats()
    {
        try {
            $stats = [
                'totalParticipantes' => Participantes::count(),
                'totalPrestamos' => PrestamosParticipante::where('estado', 'Pendiente')->sum('monto') ?? 0,
                'totalPagos' => Pagos::sum('monto') ?? 0,
                'prestamosPendientes' => PrestamosParticipante::where('estado', 1)->count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }

    public function obtenerUltimasTransacciones()
    {
        try {
            $transacciones = Pagos::with(['prestamo.participante'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($pago) {
                    return [
                        'id' => $pago->id,
                        'fecha' => $pago->created_at->format('Y-m-d H:i:s'),
                        'monto' => number_format($pago->monto, 2),
                        'participante' => $pago->prestamo->participante->nombre ?? 'Sin nombre',
                        'tipo' => 'Pago'
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $transacciones
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener transacciones'
            ], 500);
        }
    }

    public function obtenerParticipantesDeudores()
    {
        try {
            $deudores = PrestamosParticipante::where('estado', 'Pendiente')
                ->with('participante')
                ->get()
                ->map(function ($prestamo) {
                    $totalPagado = Pagos::where('prestamo_id', $prestamo->id)->sum('monto') ?? 0;
                    $montoRestante = $prestamo->monto - $totalPagado;
                    
                    return [
                        'id' => $prestamo->id,
                        'participante' => $prestamo->participante->nombre ?? 'Sin nombre',
                        'montoTotal' => number_format($prestamo->monto, 2),
                        'montoPagado' => number_format($totalPagado, 2),
                        'montoRestante' => number_format($montoRestante, 2)
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $deudores
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener deudores'
            ], 500);
        }
    }
}
