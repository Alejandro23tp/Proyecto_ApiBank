<?php

namespace App\Http\Controllers;

use App\Models\AuthParticipante;
use App\Models\Participantes;
use App\Services\SriService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ParticipanteAuthController extends Controller
{
    protected $sriService;

    public function __construct(SriService $sriService)
    {
        $this->sriService = $sriService;
        $this->middleware('auth:participante', ['except' => ['login', 'register', 'verificarCedula']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (!$token = auth('participante')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:auth_participantes',
            'username' => 'required|string|unique:auth_participantes',
            'password' => 'required|min:6',
            'part_cedula' => 'required|exists:participante,part_cedula'
        ]);

        $participante = Participantes::where('part_cedula', $request->part_cedula)->first();
        
        // Verificar si ya tiene cuenta
        if (AuthParticipante::where('participante_id', $participante->id)->exists()) {
            return response()->json(['error' => 'Este participante ya tiene una cuenta'], 400);
        }

        try {
            $auth = AuthParticipante::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'participante_id' => $participante->id
            ]);

            return response()->json([
                'message' => 'Registro exitoso',
                'participante' => $participante,
                'auth' => $auth
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al registrar: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function respondWithToken($token)
    {
        $user = auth('participante')->user();
        $participante = $user->participante;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'participante' => [
                    'id' => $participante->id,
                    'nombre' => $participante->part_nombre,
                    'cedula' => $participante->part_cedula,
                    'cupos' => $participante->part_cupos
                ]
            ]
        ]);
    }

    public function verificarCedula(Request $request)
    {
        $request->validate(['cedula' => 'required|string|size:10']);

        // Consultar al SRI
        $sriResponse = $this->sriService->consultarCedula($request->cedula);
        
        // Verificar si ya existe en el sistema
        $participante = Participantes::where('part_cedula', $request->cedula)->first();
        $tieneAuth = $participante ? AuthParticipante::where('participante_id', $participante->id)->exists() : false;

        return response()->json([
            'success' => $sriResponse['success'],
            'nombre' => $sriResponse['nombre'],
            'existe_en_sistema' => !is_null($participante),
            'tiene_cuenta' => $tieneAuth,
            'participante' => $participante
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
