<?php

use App\Http\Controllers\MatriculaController;
use App\Models\Alumno;

Route::get('/', [MatriculaController::class, 'index'])
    ->name('view');

Route::get('/mas', [MatriculaController::class, 'viewAll'])
    ->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"alumnos","create"']], function () {
    Route::get('/crear', [MatriculaController::class, 'create'])
        ->name('create');

    Route::get('/api/alumnos/{id}/info', [MatriculaController::class, 'getAlumnoInfo']);


    Route::put('/crear', [MatriculaController::class, 'createNewEntry'])
        ->name('createNewEntry');
});

Route::group(['middleware' => ['can:manage-resource,"alumnos","edit"']], function () {
    Route::get('/{id}/editar', [MatriculaController::class, 'edit'])
        ->name('edit');

    Route::patch('/{id}/editar', [MatriculaController::class, 'editEntry'])
        ->name('editEntry');
});

Route::group(['middleware' => ['can:manage-resource,"alumnos","delete"']], function () {
    Route::delete('/', [MatriculaController::class, 'delete'])
        ->name('delete');
});

Route::group(['middleware' => ['can:manage-resource,"alumnos","download"']], function () {
    Route::get('/export', [MatriculaController::class, 'export'])
        ->name('export');

    Route::redirect('/mas/export', '/matriculas/export');
});
