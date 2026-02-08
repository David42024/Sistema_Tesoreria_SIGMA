<?php

use App\Http\Controllers\FamiliarPagosController;
use App\Http\Controllers\FamiliarPagoRealizarController;

Route::get('/', [FamiliarPagosController::class, 'indexPagos'])
    ->name('view_pagos');

Route::get('/deudas', [FamiliarPagosController::class, 'indexDeudas'])
    ->name('view_deudas');

// Rutas para realizar pago
Route::get('/realizar-pago', [FamiliarPagoRealizarController::class, 'index'])
    ->name('realizar_index');

Route::post('/realizar-pago/procesar-seleccion', [FamiliarPagoRealizarController::class, 'procesarSeleccion'])
    ->name('realizar_procesar_seleccion');

Route::get('/realizar-pago/metodo', [FamiliarPagoRealizarController::class, 'mostrarMetodos'])
    ->name('realizar_metodo');

Route::get('/realizar-pago/metodo/{metodo}', [FamiliarPagoRealizarController::class, 'mostrarFormularioMetodo'])
    ->name('realizar_formulario');

Route::post('/realizar-pago/procesar', [FamiliarPagoRealizarController::class, 'procesarPago'])
    ->name('realizar_procesar');

Route::get('/realizar-pago/exito/{transaccion_id}', [FamiliarPagoRealizarController::class, 'mostrarExito'])
    ->name('realizar_exito');
