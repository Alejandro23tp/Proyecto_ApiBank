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
    
    
}
