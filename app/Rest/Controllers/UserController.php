<?php

namespace App\Rest\Controllers;

use App\Models\User;
use App\Rest\Controller as RestController;
use App\Rest\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;

class UserController extends RestController
{
    public static $resource = UserResource::class;

    public function login(Request $request)
    {
        try {
            $user = User::where('usr_correo', $request->usr_correo)->first();

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
        } catch (QueryException $e) {
            Log::error('Database query error in login: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error interno del servidor',
                'cant' => 0,
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Log::error('General error in login: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error interno del servidor',
                'cant' => 0,
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'usr_usuario' => 'required|string|max:255',
                'usr_correo' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'usr_usuario' => $validatedData['usr_usuario'],
                'usr_correo' => $validatedData['usr_correo'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json([
                'mensaje' => 'Usuario creado exitosamente',
                'cant' => 1,
                'data' => $user
            ], 201);
        } catch (QueryException $e) {
            Log::error('Database query error in register: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error interno del servidor',
                'cant' => 0,
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Log::error('General error in register: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error interno del servidor',
                'cant' => 0,
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
