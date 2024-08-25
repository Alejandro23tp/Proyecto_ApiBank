<?php

namespace App\Rest\Controllers;

use App\Models\Participantes;
use App\Models\Semanas;
use App\Rest\Controller as RestController;
use App\Rest\Resources\SemanaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SemanaComtroller extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = SemanaResource::class;
    
    public function ListarxId(Request $request)
    {
        $data = Semanas::where('part_id', $request->part_id)->get();
        return response()->json([
            'mensaje' => 'Semana encontrada',
            'cant' => 1,
            'data' => $data
        ]);
    }
    //en el post se manda con "semana" : "nombre de la semana"
    public function obtenerParticipantesNoEnSemana(Request $request)
    {
        // Validar que el parámetro 'Semana' esté presente
        $request->validate([
            'Semana' => 'required|string'
        ]);

        $nombreSemana = $request->input('Semana');

        // Obtener todos los IDs de participantes
        $participantesIds = Participantes::pluck('id');

        // Obtener los IDs de participantes que están en la semana especificada
        $participantesEnSemana = Semanas::where('nombre_semana', $nombreSemana)
                                        ->pluck('part_id');

        // Filtrar los participantes que no están en la semana especificada
        $participantesNoEnSemana = Participantes::whereIn('id', $participantesIds)
                                                ->whereNotIn('id', $participantesEnSemana)
                                                ->get();

        return response()->json([
            'mensaje' => 'Participantes no asociados con la semana especificada',
            'data' => $participantesNoEnSemana
        ]);
    }
}
