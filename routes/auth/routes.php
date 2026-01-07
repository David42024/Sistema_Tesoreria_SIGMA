<?php

use Illuminate\Http\Request;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route; // Asegúrate de que esta línea esté presente
use Illuminate\Support\Facades\Gate; // Asegúrate de que esta línea esté presente si usas Gate
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth'])->group(function(){
    // Modificamos esta ruta
    Route::get('/', function(Request $request){
        
        $user = Auth::user();

        if ($user->tipo === 'PreApoderado') {
            return redirect()->route('pre_apoderado.estado_solicitud');
        }

        if (Gate::allows('is-admin')){
            // Si es admin, redirigimos a una ruta que maneja el HomeController
            return redirect()->route('admin.dashboard');
        }

        return App\Http\Controllers\Home\HomeController::index($request);
    })->name('principal');

    Route::post('/', [App\Http\Controllers\Home\HomeController::class, 'definirSesion']);

    // **NUEVA RUTA PARA EL ADMINISTRADOR**
    // Esta ruta será la que cargue el dashboard del administrador
    Route::get('/admin/dashboard', [HomeController::class, 'index'])
        ->middleware('can:is-admin') // Opcional: Puedes añadir el middleware 'can' aquí también para mayor seguridad
        ->name('admin.dashboard');

});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'iniciarSesion']);

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');