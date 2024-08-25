<?php

namespace App\Rest\Controllers;

use App\Models\User;
use App\Rest\Controller as RestController;
use App\Rest\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = UserResource::class;

    public function login(Request $request)
    {
        // Buscar al usuario por el correo electrónico proporcionado
        $user = User::where('usr_correo', $request->usr_correo)->first();
        
        // Verificar si se encontró el usuario y si la contraseña coincide
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'mensaje' => 'Usuario encontrado',
                'cant' => 1,
                'data' => $user
            ]);
        } else {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'cant' => 0,
                'data' => null
            ]);
        }


    
    }
}