<?php

use App\Http\Controllers\FamiliarMatriculasController;

Route::get('/', [FamiliarMatriculasController::class, 'index'])->name('view');
Route::get('/prematricula', [FamiliarMatriculasController::class, 'create'])->name('prematricula_create');
Route::post('/prematricula', [FamiliarMatriculasController::class, 'store'])->name('prematricula_store');

// API para información del alumno
Route::get('/api/alumnos/{id_alumno}/info', [FamiliarMatriculasController::class, 'getAlumnoInfo'])->name('alumno_info');