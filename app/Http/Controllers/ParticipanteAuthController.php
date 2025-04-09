<?php

namespace App\Http\Controllers;

use App\Models\AuthParticipante;
use App\Models\Participantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ParticipanteAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:participante', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth('participante')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:auth_participantes',
            'password' => 'required|min:6',
            'part_cedula' => 'required|unique:participante',
            'part_nombre' => 'required',
            'part_telefono' => 'required'
        ]);

        $participante = Participantes::create([
            'part_nombre' => $request->part_nombre,
            'part_cedula' => $request->part_cedula,
            'part_telefono' => $request->part_telefono,
            'part_cupos' => 0
        ]);

        AuthParticipante::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'participante_id' => $participante->id
        ]);

        return response()->json(['message' => 'Registro exitoso'], 201);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            return response()->json([
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo refrescar el token'], 401);
        }
    }

    public function logout()
    {
        auth('participante')->logout();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}
