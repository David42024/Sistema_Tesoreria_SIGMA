<?php

use App\Http\Controllers\DepartamentoAcademicoController;

// Rutas de vista (permiso view implícito por el grupo padre)
Route::get('/', [DepartamentoAcademicoController::class, 'index'])
    ->name('view');

Route::get('/mas', [DepartamentoAcademicoController::class, 'viewAll'])
    ->name('viewAll');

Route::get('/{id}/docentes', [DepartamentoAcademicoController::class, 'docentes'])
    ->name('docentes'); 

Route::group(['middleware' => ['can:manage-resource,"personal","create"']], function(){
    Route::get('/crear', [DepartamentoAcademicoController::class, 'create'])
        ->name('create');

    Route::put('/crear', [DepartamentoAcademicoController::class, 'createNewEntry'])
        ->name('createNewEntry');
});

Route::group(['middleware' => ['can:manage-resource,"personal","edit"']], function(){
    Route::get('/{id}/editar', [DepartamentoAcademicoController::class, 'edit'])
        ->name('edit');

    Route::get('/{id}/agregar-docente', [DepartamentoAcademicoController::class, 'agregarDocente'])
        ->name('agregar_docente');
    
    Route::post('/{id}/agregar-docente', [DepartamentoAcademicoController::class, 'guardarDocente'])
        ->name('guardar_docente');
    
    Route::post('/quitar-docente', [DepartamentoAcademicoController::class, 'quitarDocente'])
        ->name('quitar_docente');

    Route::patch('/{id}/editar', [DepartamentoAcademicoController::class, 'editEntry'])
        ->name('editEntry');
});

Route::group(['middleware' => ['can:manage-resource,"personal","delete"']], function(){
    Route::delete('/', [DepartamentoAcademicoController::class, 'delete'])
        ->name('delete');
});

Route::group(['middleware' => ['can:manage-resource,"personal","download"']], function(){
    Route::get('/export', [DepartamentoAcademicoController::class, 'export'])
        ->name('export');

    Route::redirect('/mas/export', '/departamentos_academicos/export');
});