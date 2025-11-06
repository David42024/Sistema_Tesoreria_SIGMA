<?php

use App\Http\Controllers\AlumnoController;

Route::get('/', [AlumnoController::class, 'index'])
    ->name('view');

Route::get('/mas', [AlumnoController::class, 'viewAll'])
    ->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"alumnos","create"']], function(){
    Route::get('/crear', [AlumnoController::class, 'create'])
        ->name('create');

    Route::put('/crear', [AlumnoController::class, 'createNewEntry'])
        ->name('createNewEntry');

    Route::get('/add/{id}/familiares', [AlumnoController::class, 'add_familiares'])
        ->name('add_familiares');

   Route::post('/add/{id}/familiares', [AlumnoController::class, 'guardarFamiliares'])
   ->name('guardar_familiares');

   Route::post('/clear-session', function() {
        session()->forget('temp_student_data');
        return response()->json(['success' => true]);
    })->name('clear_session');

    Route::get('/add-familiares-session', [AlumnoController::class, 'add_familiares_session'])
    ->name('add_familiares_session');

    Route::post('/guardar-familiares-session', [AlumnoController::class, 'guardarFamiliaresSession'])
        ->name('guardar_familiares_session');


});

Route::group(['middleware' => ['can:manage-resource,"alumnos","edit"']], function(){
    Route::get('/{id}/editar', [AlumnoController::class, 'edit'])
        ->name('edit');

    Route::patch('/{id}/editar', [AlumnoController::class, 'editEntry'])
        ->name('editEntry');
});

Route::group(['middleware' => ['can:manage-resource,"alumnos","delete"']], function(){
    Route::delete('/', [AlumnoController::class, 'delete'])
        ->name('delete');
});

Route::group(['middleware' => ['can:manage-resource,"alumnos","download"']], function(){
    Route::get('/export', [AlumnoController::class, 'export'])
        ->name('export');

    Route::redirect('/mas/export', '/alumnos/export');
});
