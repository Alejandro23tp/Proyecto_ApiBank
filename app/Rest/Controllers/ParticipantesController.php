<?php

namespace App\Rest\Controllers;

use App\Models\Participantes;
use App\Rest\Controller as RestController;
use App\Rest\Resources\ParticipantesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantesController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = ParticipantesResource::class;
    

    public function AllParticipantes(Request $request)
    {
        $participantes = Participantes::all();
        
        return response()->json([
            'message' => 'Listado de participantes',
            'data' => $participantes
        ]);
    }

    public function buscarCupoParticipante($part_id)
    {
        // Obtener el ID del participante desde el request
        $partId = Participantes::findOrFail($part_id);
           
        return response()->json([
            'mensaje' => 'Participante encontrado',
            'cant' => 1,
            'data' => $partId
        ]);
    }

    public function calcularSaldoAnterior($id_tablapresentar_semanas)
    {
        DB::statement('CALL calcular_saldoanterior(?)', [$id_tablapresentar_semanas]);

        return response()->json([
            'mensaje' => 'Saldo anterior calculado',
            'cant' => 1,
            'data' => ''
        ]);
    }
}
