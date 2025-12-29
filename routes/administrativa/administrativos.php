<?php

use App\Http\Controllers\AdministrativoController;

Route::get('/', [AdministrativoController::class, 'index'])
        ->name('view');

Route::get('/mas', [AdministrativoController::class, 'viewAll'])
    ->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"administrativa","create"']], function(){
    Route::get('/crear', [AdministrativoController::class, 'create'])
        ->name('create');

    Route::put('/crear', [AdministrativoController::class, 'createNewEntry'])
        ->name('createNewEntry');
});

Route::group(['middleware' => ['can:manage-resource,"administrativa","edit"']], function(){
    Route::get('/{id}/editar', [AdministrativoController::class, 'edit'])
        ->name('edit');

    Route::patch('/{id}/editar', [AdministrativoController::class, 'editEntry'])
        ->name('editEntry');
});

Route::group(['middleware' => ['can:manage-resource,"administrativa","delete"']], function(){
    Route::delete('/', [AdministrativoController::class, 'delete'])
        ->name('delete');
});

Route::group(['middleware' => ['can:manage-resource,"administrativa","download"']], function(){
    Route::get('/export', [AdministrativoController::class, 'export'])
        ->name('export');

    Route::redirect('/mas/export', '/administrativos/export');
});