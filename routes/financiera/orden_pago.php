<?php

use App\Http\Controllers\OrdenPagoController;

Route::get('/', [OrdenPagoController::class, 'index'])
    ->name('view');

Route::group(['middleware' => ['can:manage-resource,"financiera","create"']], function(){
    Route::get('/crear', [OrdenPagoController::class, 'create'])
        ->name('create');

    Route::post('/crear', [OrdenPagoController::class, 'store'])
        ->name('store');

    Route::get('/buscarAlumno/{codigo}', [OrdenPagoController::class,'buscarAlumno'])
        ->name('buscarAlumno');

    // Nuevas rutas para búsqueda en tiempo real
    Route::get('/buscar-alumnos-nombre', [OrdenPagoController::class, 'buscarAlumnosPorNombre'])
        ->name('buscarAlumnosPorNombre');

    Route::get('/obtener-grados', [OrdenPagoController::class, 'obtenerGrados'])
        ->name('obtenerGrados');

    Route::get('/obtener-secciones', [OrdenPagoController::class, 'obtenerSecciones'])
        ->name('obtenerSecciones');
});

Route::group(['middleware' => ['can:manage-resource,"financiera","view_details"']], function(){
    Route::get('/{id}/pdf', [OrdenPagoController::class, 'generarPDF'])
        ->name('pdf');

    Route::get('/{id}/detalles', [OrdenPagoController::class, 'viewDetalles'])
        ->name('detalles');
});

Route::group(['middleware' => ['can:manage-resource,"financiera","edit"']], function(){
    Route::patch('/{id}/anular', [OrdenPagoController::class, 'anular'])
        ->name('anular');
});

Route::group(['middleware' => ['can:manage-resource,"financiera","delete"']], function(){
    Route::delete('/', [OrdenPagoController::class, 'delete'])
        ->name('delete');
});
