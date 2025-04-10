<?php

namespace App\Http\Controllers;

use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use Illuminate\Http\Request;

class ParticipanteViewController extends Controller
{
    public function miPerfil()
    {
        $participante = auth('participante')->user()->participante;
        return response()->json([
            'status' => 'success',
            'participante' => [
                'id' => $participante->id,
                'nombre' => $participante->part_nombre,
                'cedula' => $participante->part_cedula,
                'telefono' => $participante->part_telefono,
                'cupos' => $participante->part_cupos
            ]
        ]);
    }
    //SOLO FUNCIONA MIPERFIL
    public function misPrestamos()
    {
        $participante = auth('participante')->user()->participante;
        $prestamos = PrestamosParticipante::where('id_participante', $participante->id)
            ->with(['pagos', 'semana'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'prestamos' => $prestamos
        ]);
    }

    public function misPagos()
    {
        $participante = auth('participante')->user()->participante;
        $pagos = $participante->pagos()
            ->with('prestamo')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'pagos' => $pagos
        ]);
    }

    public function misSemanas()
    {
        $participante = auth('participante')->user()->participante;
        $semanas = $participante->semanas()
            ->with(['presentaciones', 'prestamos'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'semanas' => $semanas
        ]);
    }

    public function miEstadoCuenta()
    {
        $participante = auth('participante')->user()->participante;
        $prestamosActivos = $participante->prestamos()->where('estado', 'activo')->get();
        
        return response()->json([
            'status' => 'success',
            'estado_cuenta' => [
                'cupos_totales' => $participante->part_cupos,
                'cupos_usados' => $prestamosActivos->count(),
                'cupos_disponibles' => $participante->part_cupos - $prestamosActivos->count(),
                'prestamos_activos' => $prestamosActivos,
                'total_prestado' => $participante->prestamos()->sum('monto'),
                'total_pagado' => $participante->pagos()->sum('monto'),
                'saldo_pendiente' => $participante->saldoPendiente()
            ]
        ]);
    }
}
