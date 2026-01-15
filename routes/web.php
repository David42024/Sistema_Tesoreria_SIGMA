<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasarelaPagoController;
use App\Http\Controllers\FamiliarAlumnoController;
use App\Http\Controllers\SolicitudPrematriculaController;

require __DIR__ . '/auth/routes.php';

Route::get('/pagar', [PasarelaPagoController::class, 'index'])->name('pasarela.index');

Route::get('/pagar/{codigo_orden}', [PasarelaPagoController::class, 'mostrarOrden'])->name('pasarela.orden');

Route::get('/pagar/{codigo_orden}/metodo/{metodo}', [PasarelaPagoController::class, 'mostrarMetodoPago'])->name('pasarela.metodo');

Route::post('/pagar/{codigo_orden}/procesar', [PasarelaPagoController::class, 'procesarPago'])->name('pasarela.procesar');

Route::get('/pagar/{codigo_orden}/comprobante/{transaccion_id}', [PasarelaPagoController::class, 'mostrarComprobante'])->name('pasarela.comprobante');

Route::get('/pagar/voucher/{transaccion_id}/pdf', [PasarelaPagoController::class, 'descargarVoucherPDF'])->name('pasarela.voucher.pdf');

Route::get('/pagar/voucher/{transaccion_id}/html', [PasarelaPagoController::class, 'mostrarVoucherHTML'])->name('pasarela.voucher.html');

Route::middleware(['auth'])->group(function(){
    require __DIR__ . '/academica/routes.php';
    require __DIR__ . '/alumnos/routes.php';
    require __DIR__ . '/administrativa/routes.php';
    require __DIR__ . '/financiera/routes.php';
    require __DIR__ . '/personal/routes.php';
    require __DIR__ . '/reportes/routes.php';
    require __DIR__ . '/familiar/routes.php';
    require __DIR__ . '/configuracion/routes.php';

    // Rutas para reportes de gestiones
    require __DIR__ . '/gestiones_reportes.php';

    Route::get('/tests', [\App\Http\Controllers\Tests\CRUDTestController::class, 'index']);
});


Route::get('/familiar/alumno/actualizar-datos', [FamiliarAlumnoController::class, 'actualizarDatos'])->name('familiar_alumno_actualizar_datos');
Route::post('/familiar/alumno/actualizar-datos', [FamiliarAlumnoController::class, 'guardarDatos'])->name('familiar_alumno_guardar_datos');
Route::post('/familiar/alumno/solicitar-reubicacion', [FamiliarAlumnoController::class, 'solicitarReubicacionEscala'])->name('familiar_alumno_solicitar_reubicacion');


Route::prefix('solicitud-prematricula')->group(function () {
    Route::get('/', [SolicitudPrematriculaController::class, 'create'])
        ->name('solicitud_prematricula.create');
    Route::post('/', [SolicitudPrematriculaController::class, 'store'])
        ->name('solicitud_prematricula.store');
    Route::get('/exito', [SolicitudPrematriculaController::class, 'exito'])
        ->name('solicitud_prematricula.exito');
});

Route::middleware(['auth'])->prefix('preapoderado')->group(function () {
    Route::get('/estado', [SolicitudPrematriculaController::class, 'estadoSolicitud'])
        ->name('pre_apoderado.estado_solicitud');
    Route::get('/nueva-solicitud', [SolicitudPrematriculaController::class, 'nuevaSolicitud'])
        ->name('pre_apoderado.nueva_solicitud');
    Route::post('/nueva-solicitud', [SolicitudPrematriculaController::class, 'guardarNuevaSolicitud'])
        ->name('pre_apoderado.guardar_nueva_solicitud');
});