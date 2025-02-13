<?php

namespace App\Rest\Controllers;

use App\Models\Pagos;
use App\Rest\Controller as RestController;
use App\Rest\Resources\PagosResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function pagarPrestamo(Request $request)
    {
        $prestpartId = $request->input('prestpart_id');
        $prestamoExiste = DB::table('prestamos_participante')->where('pp_partId', $prestpartId)->exists();

        if (!$prestamoExiste) {
            return response()->json([
                'status' => 'error',
                'message' => 'El prestpart_id proporcionado no existe.'
            ], 400);
        }

        try {
            $pagoId = DB::table('pagos')->insertGetId([
                'prestpart_id' => $prestpartId,
                'semana' => $request->input('semana'),
                'valor' => $request->input('valor'),
                'fecha' => $request->input('fecha'),
                'observaciones' => $request->input('observaciones'),
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $pagoId
            ]);

        } catch (\Exception $e) {
            Log::error('Error al registrar pago: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
