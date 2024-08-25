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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Rest::resource('participantes', ParticipantesController::class);
Route::get('listarParticipantes', [ParticipantesController::class , 'AllParticipantes']);
Route::get('obtenerCupoParticipante/{part_id}', [ParticipantesController::class , 'buscarCupoParticipante']);

//Login
Rest::resource('user', UserController::class);
Route::post('login', [UserController::class, 'login']);

//Semanas
Rest::resource('semanas', SemanaComtroller::class);
Route::post('listarSemId', [SemanaComtroller::class, 'ListarxId']);
Route::post('obtenerParticipantesNoEnSemana', [SemanaComtroller::class, 'obtenerParticipantesNoEnSemana']);

//Prestamos
Rest::resource('registrarPrestamo', PrestamosParticipanteController::class);
Route::post('listarPrestamosId', [PrestamosParticipanteController::class, 'ListarxId']);
Route::get('listarPrestamistas', [PrestamosParticipanteController::class, 'listarAll']);
Route::post('prestamistasCancelar', [PrestamosParticipanteController::class, 'prestamistassincancelar']);

//PagarPrestamo
Rest::resource('pagoprestamo', PagosController::class);

//Presentacion de Semanas
Rest::resource('presentar_semanas', PresentarSemanasController::class);
Route::post('listarxsemana', [PresentarSemanasController::class, 'listarxsemana']);
Route::get('listarAllPresentarSemanas', [PresentarSemanasController::class, 'listarAllPrestamos']);

//Calcular saldo anterior
Route::get('calcularSaldoAnterior/{id_tablapresentar_semanas}', [ParticipantesController::class, 'calcularSaldoAnterior']);

//Listar Pagos de cadad Participante
Route::get('listarpagosall', [PagosController::class, 'listarAll']);
Route::post('listarpagosid', [PagosController::class, 'listarxId']);