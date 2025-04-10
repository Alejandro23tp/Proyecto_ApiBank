<?php

namespace App\Http\Controllers;

use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use App\Models\Pagos;
use App\Models\Semanas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParticipanteViewController extends Controller
{
    public function miPerfil()
    {
        try {
            $participante = auth('participante')->user()->participante;
            return response()->json([
                'mensaje' => 'Perfil encontrado',
                'cant' => 1,
                'data' => [
                    'id' => $participante->id,
                    'nombre' => $participante->part_nombre,
                    'cedula' => $participante->part_cedula,
                    'telefono' => $participante->part_telefono,
                    'cupos' => $participante->part_cupos
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en miPerfil: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener el perfil',
                'cant' => 0,
                'data' => null
            ], 500);
        }
    }

    public function misPrestamos()
    {
        try {
            $participante = auth('participante')->user()->participante;
            $prestamos = PrestamosParticipante::where('pp_partId', $participante->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'mensaje' => 'Préstamos encontrados',
                'cant' => $prestamos->count(),
                'data' => $prestamos
            ]);
        } catch (\Exception $e) {
            Log::error('Error en misPrestamos: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener los préstamos',
                'cant' => 0,
                'data' => null
            ], 500);
        }
    }

    public function misPagos()
    {
        try {
            $participante = auth('participante')->user()->participante;
            // Obtener los IDs de los préstamos del participante
            $prestamosIds = PrestamosParticipante::where('pp_partId', $participante->id)
                ->pluck('id');
            
            // Obtener los pagos relacionados
            $pagos = Pagos::whereIn('prestpart_id', $prestamosIds)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'mensaje' => 'Pagos encontrados',
                'cant' => $pagos->count(),
                'data' => $pagos
            ]);
        } catch (\Exception $e) {
            Log::error('Error en misPagos: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener los pagos',
                'cant' => 0,
                'data' => null
            ], 500);
        }
    }

    public function misSemanas()
    {
        try {
            $participante = auth('participante')->user()->participante;
            $semanas = Semanas::where('part_id', $participante->id)
                ->orderBy('inicioSemana', 'desc')
                ->get();
            
            return response()->json([
                'mensaje' => 'Semanas encontradas',
                'cant' => $semanas->count(),
                'data' => $semanas
            ]);
        } catch (\Exception $e) {
            Log::error('Error en misSemanas: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener las semanas',
                'cant' => 0,
                'data' => null
            ], 500);
        }
    }

    public function miEstadoCuenta()
    {
        try {
            $participante = auth('participante')->user()->participante;
            
            $prestamos = PrestamosParticipante::where('pp_partId', $participante->id)->get();
            $prestamosActivos = $prestamos->where('estado', 'Pendiente');
            
            // Obtener los IDs de los préstamos del participante
            $prestamosIds = $prestamos->pluck('id');
            
            // Calcular el total pagado
            $totalPagado = Pagos::whereIn('prestpart_id', $prestamosIds)
                ->sum('valor');
            
            // Calcular el total prestado
            $totalPrestado = $prestamos->sum('pp_prestamo');
            
            return response()->json([
                'mensaje' => 'Estado de cuenta encontrado',
                'cant' => 1,
                'data' => [
                    'cupos_totales' => $participante->part_cupos,
                    'prestamos_activos' => $prestamosActivos->values(),
                    'total_prestado' => $totalPrestado,
                    'total_pagado' => $totalPagado,
                    'saldo_pendiente' => $totalPrestado - $totalPagado
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en miEstadoCuenta: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al obtener el estado de cuenta',
                'cant' => 0,
                'data' => null
            ], 500);
        }
    }
}
