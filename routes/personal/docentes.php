<?php

use App\Http\Controllers\DocenteController;

Route::get('/', [DocenteController::class, 'index'])->name('view');
Route::get('/mas', [DocenteController::class, 'viewAll'])->name('viewAll');

Route::group(['middleware' => ['can:manage-resource,"personal","create"']],
    function(){
        Route::get('/crear', [DocenteController::class,'create'])->name('create'); 
        Route::put('/crear', [DocenteController::class, 'createNewEntry'])->name('createNewEntry');
    }
);

Route::group(
    ['middleware' => ['can:manage-resource,"personal","edit"']],
    function () {
        Route::get('/{id}/editar', [DocenteController::class, 'edit'])->name('edit');
        Route::patch('/{id}/editar', [DocenteController::class, 'editEntry'])->name('editEntry');
    }
);

Route::group(
    ['middleware' => ['can:manage-resource,"personal","delete"']],
    function () {
        Route::delete('/', [DocenteController::class, 'delete'])->name('delete');
    }
);