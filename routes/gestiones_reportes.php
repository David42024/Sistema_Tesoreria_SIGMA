<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/gestiones/reportes/academicos', [\App\Http\Controllers\ReporteAcademicoController::class, 'index'])
        ->name('gestiones.reportes.academicos');

    Route::get('/gestiones/reportes/financieros', [\App\Http\Controllers\ReporteFinancieroController::class, 'pagosPorMes'])
        ->name('gestiones.reportes.financieros');

    Route::get('/gestiones/reportes/estadisticas', function () {
        return view('gestiones.reportes.estadisticas');
    })->name('gestiones.reportes.estadisticas');
});
