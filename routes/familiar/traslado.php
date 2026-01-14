<?php

use App\Http\Controllers\FamiliarTrasladoController;

// Vista para Traslado Regular
Route::get('/regular', [FamiliarTrasladoController::class, 'indexRegular'])
    ->name('regular');

// Vista para Traslado Excepcional
Route::get('/excepcional', [FamiliarTrasladoController::class, 'indexExcepcional'])
    ->name('excepcional');

// Verificar deudas pendientes hasta una fecha (para traslado excepcional)
Route::post('/verificar-deudas', [FamiliarTrasladoController::class, 'verificarDeudas'])
    ->name('verificar_deudas');

// Guardar solicitud de traslado excepcional
Route::post('/guardar-solicitud', [FamiliarTrasladoController::class, 'guardarSolicitud'])
    ->name('guardar_solicitud');

// Verificar deudas pendientes del año completo (para traslado regular)
Route::post('/verificar-deudas-regular', [FamiliarTrasladoController::class, 'verificarDeudasRegular'])
    ->name('verificar_deudas_regular');

// Guardar solicitud de traslado regular
Route::post('/guardar-solicitud-regular', [FamiliarTrasladoController::class, 'guardarSolicitudRegular'])
    ->name('guardar_solicitud_regular');

