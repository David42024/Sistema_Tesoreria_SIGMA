<?php

use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])
    ->name('view');

Route::get('/mas', [UserController::class, 'viewAll'])
    ->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"usuarios","create"']], function(){
    Route::get('/crear', [UserController::class, 'create'])
        ->name('create');

    Route::put('/crear', [UserController::class, 'createNewEntry'])
        ->name('createNewEntry');
});

Route::group(['middleware' => ['can:manage-resource,"usuarios","edit"']], function(){
    Route::get('/{id}/editar', [UserController::class, 'edit'])
        ->name('edit');

    Route::patch('/{id}/editar', [UserController::class, 'editEntry'])
        ->name('editEntry');

    Route::get('/{id}/cambiar-password', [UserController::class, 'showChangePassword'])
        ->name('change_password');

    Route::patch('/{id}/cambiar-password', [UserController::class, 'changePassword'])
        ->name('update_password');
});

Route::group(['middleware' => ['can:manage-resource,"usuarios","delete"']], function(){
    Route::delete('/', [UserController::class, 'delete'])
        ->name('delete');
});

Route::group(['middleware' => ['can:manage-resource,"usuarios","download"']], function(){
    Route::get('/export', [UserController::class, 'export'])
        ->name('export');

    Route::redirect('/mas/export', '/usuarios/export');
});
