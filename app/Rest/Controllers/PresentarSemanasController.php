<?php

namespace App\Rest\Controllers;

use App\Models\PresentarSemanas;
use App\Rest\Controller as RestController;
use App\Rest\Resources\PresentarSemanasResource;
use Illuminate\Http\Request;

class PresentarSemanasController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = PresentarSemanasResource::class;

    public function listarxsemana(Request $request)
    {
        $data = PresentarSemanas::where('semana', $request->semana)->get();
        return response()->json([
            'mensaje' => 'PresentarSemanas encontrada',
            'cant' => 1,
            'data' => $data
        ]);
    }

    public function listarAllPrestamos(Request $request)
    {
        $data = PresentarSemanas::all();
        return response()->json([
            'mensaje' => 'Listado de presentar_semanas',
            'data' => $data
        ]);
    }
}
