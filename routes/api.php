<?php

use App\Models\Participantes;
use App\Models\PrestamosParticipante;
use App\Rest\Controllers\PagosController;
use App\Rest\Controllers\ParticipantesController;
use App\Rest\Controllers\PresentarSemanasController;
use App\Rest\Controllers\PrestamosParticipanteController;
use App\Rest\Controllers\SemanaComtroller;
use App\Rest\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lomkit\Rest\Facades\Rest;
use App\Rest\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Rest::resource('participantes', ParticipantesController::class);
Route::get('listarParticipantes', [ParticipantesController::class , 'AllParticipantes']);
Route::get('obtenerCupoParticipante/{part_id}', [ParticipantesController::class , 'buscarCupoParticipante']);

// Los recursos de usuario se manejan ahora a través de la autenticación JWT

//Semanas
Rest::resource('semanas', SemanaComtroller::class);
Route::post('listarSemId', [SemanaComtroller::class, 'ListarxId']);
Route::post('obtenerParticipantesNoEnSemana', [SemanaComtroller::class, 'obtenerParticipantesNoEnSemana']);

//Prestamos
Rest::resource('registrarPrestamo', PrestamosParticipanteController::class);
Route::post('listarPrestamosId', [PrestamosParticipanteController::class, 'ListarxId']);
Route::get('listarPrestamistas', [PrestamosParticipanteController::class, 'listarAll']);
Route::post('prestamistasCancelar', [PrestamosParticipanteController::class, 'prestamistassincancelar']);
Route::post('cancelarPrestamo', [PrestamosParticipanteController::class, 'cancelarPrestamo']);

//PagarPrestamo
Rest::resource('pagoprestamo', PagosController::class);

Route::post('pagoprestamo', [PagosController::class, 'pagarPrestamo']);

//Presentacion de Semanas
Rest::resource('presentar_semanas', PresentarSemanasController::class);
Route::post('listarxsemana', [PresentarSemanasController::class, 'listarxsemana']);
Route::get('listarAllPresentarSemanas', [PresentarSemanasController::class, 'listarAllPrestamos']);

//Calcular saldo anterior
Route::get('calcularSaldoAnterior/{id_tablapresentar_semanas}', [ParticipantesController::class, 'calcularSaldoAnterior']);

//Listar Pagos de cadad Participante
Route::get('listarpagosall', [PagosController::class, 'listarAll']);
Route::post('listarpagosid', [PagosController::class, 'listarxId']);

//Dashboard
Route::prefix('dashboard')->group(function () {
    Route::get('/stats', [DashboardController::class, 'obtenerDashboardStats']);
    Route::get('/transacciones', [DashboardController::class, 'obtenerUltimasTransacciones']);
    Route::get('/deudores', [DashboardController::class, 'obtenerParticipantesDeudores']);
    Route::get('/intereses', [DashboardController::class, 'obtenerIntereses']);
});

// JWT Authentication routes
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
