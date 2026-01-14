<?php

use App\Http\Controllers\SolicitudPrematriculaController;

// Lista de solicitudes
Route::get('/', [SolicitudPrematriculaController::class, 'index'])
    ->name('index');

// Ver más (lista completa)
Route::get('/all', [SolicitudPrematriculaController::class, 'indexAll'])
    ->name('index_all');

// Ver detalle de solicitud
Route::get('/{id}', [SolicitudPrematriculaController::class, 'show'])
    ->name('show')
    ->where('id', '[0-9]+');

// Aprobar solicitud
Route::post('/{id}/aprobar', [SolicitudPrematriculaController::class, 'aprobar'])
    ->name('aprobar');

// Rechazar solicitud
Route::post('/{id}/rechazar', [SolicitudPrematriculaController::class, 'rechazar'])
    ->name('rechazar');

// Marcar como en revisión
Route::post('/{id}/en-revision', [SolicitudPrematriculaController::class, 'marcarEnRevision'])
    ->name('en_revision');
