<?php

use App\Http\Controllers\DepartamentoAcademicoController;

Route::get('/', [DepartamentoAcademicoController::class, 'index'])
    ->name('view');

Route::get('/mas', [DepartamentoAcademicoController::class, 'viewAll'])
    ->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"personal","create"']], function(){
    Route::get('/crear', [DepartamentoAcademicoController::class, 'create'])
        ->name('create');

    Route::put('/crear', [DepartamentoAcademicoController::class, 'createNewEntry'])
        ->name('createNewEntry');
});

Route::group(['middleware' => ['can:manage-resource,"personal","edit"']], function(){
    Route::get('/{id}/editar', [DepartamentoAcademicoController::class, 'edit'])
        ->name('edit');

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
