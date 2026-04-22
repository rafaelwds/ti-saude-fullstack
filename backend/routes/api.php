<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\EspecialidadeController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PlanoSaudeController;
use App\Http\Controllers\ProcedimentoController;
use App\Http\Controllers\VinculoController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Domínio principal
    Route::apiResource('pacientes', PacienteController::class);

    // Fase 1 - Especialidades, Médicos, Consultas
    Route::apiResource('especialidades', EspecialidadeController::class);
    Route::apiResource('medicos', MedicoController::class);
    Route::apiResource('consultas', ConsultaController::class);

    // Fase 2 - Procedimentos
    Route::apiResource('procedimentos', ProcedimentoController::class);

    // Fase 3 - Planos de saúde e vínculos
    Route::apiResource('planos-saude', PlanoSaudeController::class);
    Route::apiResource('vinculos', VinculoController::class);
});
