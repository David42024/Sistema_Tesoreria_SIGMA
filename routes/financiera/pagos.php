<?php

use App\Http\Controllers\PagoController;

Route::get('/', [PagoController::class, 'index'])
    ->name('view');

Route::get('/mas', [PagoController::class, 'viewAll'])
    ->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"financiera","create"']], function(){
    Route::get('/crear', [PagoController::class, 'create'])
        ->name('create');

    Route::put('/crear', [PagoController::class, 'createNewEntry'])
        ->name('createNewEntry');
    
    // Vista para registrar pago de orden
    Route::get('/registrar-pago-orden', [PagoController::class, 'crearPagoOrden'])
        ->name('crear_pago_orden');
    
    // Nueva ruta para registrar pagos a nivel de orden
    Route::post('/registrar-pago-orden', [PagoController::class, 'registrarPagoOrden'])
        ->name('registrar_pago_orden');
    
    // Obtener información de una orden para registrar pago
    Route::get('/info-orden/{id_orden}', [PagoController::class, 'obtenerInfoOrden'])
        ->name('info_orden');
    
    // Buscar orden por código
    Route::get('/buscar-orden/{codigo_orden}', [PagoController::class, 'buscarOrdenPorCodigo'])
        ->name('buscar_orden')
        ->where('codigo_orden', '.*');

    Route::get('/buscarAlumno/{codigo}', [PagoController::class,'buscarAlumno'])->name('buscarAlumno');

    Route::get('/buscarPagosDeuda/{id_deuda}', [PagoController::class, 'buscarPagosDeuda']);
});

Route::group(['middleware' => ['can:manage-resource,"financiera","edit"']], function(){
    Route::get('/{id}/editar', [PagoController::class, 'edit'])
        ->name('edit');

    Route::patch('/{id}/editar', [PagoController::class, 'editEntry'])
        ->name('editEntry');

    Route::get('/{id}/completar', [PagoController::class, 'completar'])
        ->name('completar');

    Route::put('/{id}/completar', [PagoController::class, 'completarPago'])
        ->name('completarPago');
});

Route::group(['middleware' => ['can:manage-resource,"financiera","delete"']], function(){
    Route::delete('/', [PagoController::class, 'delete'])
        ->name('delete');
});

Route::group(['middleware' => ['can:manage-resource,"financiera","view_details"']], function(){
    Route::get('/{id}/detalles', [PagoController::class, 'viewDetalles'])
        ->name('detalles');
    
    Route::patch('/detalle/{idDetalle}', [PagoController::class, 'actualizarDetalle'])
        ->name('actualizar_detalle');
});


Route::group(['middleware' => ['can:manage-resource,"financiera","download"']], function(){
    Route::get('/export', [PagoController::class, 'export'])
        ->name('export');

    Route::redirect('/mas/export', '/financiera/pagos/export');
});
