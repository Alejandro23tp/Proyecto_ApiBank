<?php

namespace App\Rest\Controllers;

use App\Models\PrestamosParticipante;
use App\Rest\Controller as RestController;
use App\Rest\Resources\PrestamosParticipanteResource;
use Illuminate\Http\Request;

class PrestamosParticipanteController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = PrestamosParticipanteResource::class;

    public function ListarxId(Request $request)
    {
        $data = PrestamosParticipante::where('pp_partId', $request->pp_partId)->get();
        return response()->json([
            'mensaje' => 'PrestamosParticipante encontrada',
            'cant' => 1,
            'data' => $data
        ]);
    }

    public function listarAll(Request $request)
    {
        $data = PrestamosParticipante::all();
        return response()->json([
            'mensaje' => 'PrestamosParticipante encontrada',
            'cant' => 1,
            'data' => $data
        ]);
    }

    public function prestamistassincancelar(Request $request)
    {
        $data = PrestamosParticipante::where('estado', $request->estado)
            ->when($request->pp_semana, function ($query, $pp_semana) {
                return $query->where('pp_semana', $pp_semana);
            })
            ->get();
    
        return response()->json([
            'mensaje' => 'PrestamosParticipante encontrada',
            'cant' => $data->count(),
            'data' => $data
        ]);
    }
    
    public function cancelarPrestamo(Request $request)
    {
        $prestamo = PrestamosParticipante::where('pp_partId', $request->pp_partId)
                      ->where('pp_semana', $request->pp_semana)
                      ->update(['estado' => 'Cancelado']);

        if ($prestamo) {
            return response()->json([
                'mensaje' => 'Préstamo actualizado a Cancelado',
                'data' => $prestamo
            ]);
        } else {
            return response()->json([
                'mensaje' => 'No se encontró el préstamo para cancelar',
                'data' => null
            ]);
        }
    }
}
