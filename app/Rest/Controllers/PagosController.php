<?php

namespace App\Rest\Controllers;

use App\Models\Pagos;
use App\Rest\Controller as RestController;
use App\Rest\Resources\PagosResource;
use Illuminate\Http\Request;

class PagosController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = PagosResource::class;

    public function listarAll(Request $request)
    {
        $pagos = Pagos::all();
        return response()->json([
            'mensaje' => 'Pagos encontrada',
            'cant' => 1,
            'data' => $pagos
        ]);
    }

    public function listarxId(Request $request)
    {
        $pagos = Pagos::where('prestpart_id', $request->prestpart_id)->get();
        return response()->json([
            'mensaje' => 'Pagos encontrada',
            'cant' => 1,
            'data' => $pagos
        ]);
    }
}
