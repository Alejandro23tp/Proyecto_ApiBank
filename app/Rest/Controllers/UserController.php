<?php

namespace App\Rest\Controllers;

use App\Models\User;
use App\Rest\Controller as RestController;
use App\Rest\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

    public function register(Request $request)
{
    // Validar los datos proporcionados en la solicitud
    $validatedData = $request->validate([
        'usr_usuario' => 'required|string|max:255',
        'usr_correo' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|minimum:8|confirmed',
    ]);

    try {
        // Crear un nuevo usuario
        $user = User::create([
            'usr_usuario' => $validatedData['usr_usuario'],
            'usr_correo' => $validatedData['usr_correo'],
            'password' => Hash::make($validatedData['password']), // Encriptar la contraseña
        ]);

        return response()->json([
            'mensaje' => 'Usuario creado exitosamente',
            'cant' => 1,
            'data' => $user
        ], 201);
    } catch (\Exception $e) {
        Log::error('Error al crear usuario: ' . $e->getMessage());
        return response()->json([
            'mensaje' => 'Error interno del servidor',
            'cant' => 0,
            'data' => null
        ], 500);
    }
}

}