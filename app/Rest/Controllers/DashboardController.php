<?php

namespace App\Rest\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pagos;
use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function obtenerDashboardStats()
    {
        try {
            $totalPrestamos = DB::table('prestamos_participante')
                ->where('estado', 'Pendiente')
                ->sum('monto');

            $totalPagos = DB::table('pagos')->sum('monto');

            $stats = [
                'totalParticipantes' => DB::table('participantes')->count(),
                'totalPrestamos' => floatval($totalPrestamos),
                'totalPagos' => floatval($totalPagos),
                'prestamosPendientes' => DB::table('prestamos_participante')
                    ->where('estado', 'Pendiente')
                    ->count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
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
            $transacciones = DB::table('pagos')
                ->join('prestamos_participante', 'pagos.prestamo_id', '=', 'prestamos_participante.id')
                ->join('participantes', 'prestamos_participante.participante_id', '=', 'participantes.id')
                ->select(
                    'pagos.id',
                    'pagos.created_at',
                    'pagos.monto',
                    'participantes.nombre as participante'
                )
                ->orderBy('pagos.created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($pago) {
                    return [
                        'id' => $pago->id,
                        'fecha' => $pago->created_at,
                        'monto' => number_format($pago->monto, 2),
                        'participante' => $pago->participante,
                        'tipo' => 'Pago'
                    ];
                });

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
            $deudores = DB::table('prestamos_participante')
                ->join('participantes', 'prestamos_participante.participante_id', '=', 'participantes.id')
                ->leftJoin('pagos', 'prestamos_participante.id', '=', 'pagos.prestamo_id')
                ->where('prestamos_participante.estado', 'Pendiente')
                ->select(
                    'prestamos_participante.id',
                    'participantes.nombre',
                    'prestamos_participante.monto as montoTotal',
                    DB::raw('COALESCE(SUM(pagos.monto), 0) as montoPagado')
                )
                ->groupBy('prestamos_participante.id', 'participantes.nombre', 'prestamos_participante.monto')
                ->get()
                ->map(function ($deudor) {
                    $montoTotal = floatval($deudor->montoTotal);
                    $montoPagado = floatval($deudor->montoPagado);
                    return [
                        'id' => $deudor->id,
                        'participante' => $deudor->nombre,
                        'montoTotal' => number_format($montoTotal, 2),
                        'montoPagado' => number_format($montoPagado, 2),
                        'montoRestante' => number_format($montoTotal - $montoPagado, 2)
                    ];
                });

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
}
