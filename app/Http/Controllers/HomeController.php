<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Pago;   
use App\Models\Deuda;  
use Carbon\Carbon;


class HomeController extends Controller
{
    

    public function index() {
        $totalAlumnos = Alumno::where('estado',1)->count();
        $totalMatriculas = Matricula::where('estado',1)->count();

        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;

        $totalPagosMes = Pago::whereMonth('fecha_pago', $mesActual)
                             ->whereYear('fecha_pago', $anioActual)
                             ->sum('monto');      
        $totalDeudasPendientes = Deuda::where('estado', 1)
                                      ->sum('monto_total');

        // Cargar relaciones del usuario autenticado y formatear nombre
        $usuario = auth()->user();
        $nombreCompleto = '';
        $esDirector = false;
        
        if (strtolower($usuario->tipo) === 'administrativo') {
            $usuario->load('administrativo');
            if ($usuario->administrativo) {
                $nombreCompleto = trim($usuario->administrativo->apellido_paterno . ' ' . 
                                      $usuario->administrativo->apellido_materno . ', ' .
                                      $usuario->administrativo->primer_nombre);
                
                // Verificar si es el Director
                $esDirector = strtolower($usuario->administrativo->cargo) === 'director';
            }
        } elseif (strtolower($usuario->tipo) === 'personal') {
            $usuario->load('personal');
            if ($usuario->personal) {
                $nombreCompleto = trim($usuario->personal->apellido_paterno . ' ' . 
                                      $usuario->personal->apellido_materno . ', ' .
                                      $usuario->personal->primer_nombre);
            }
        }
        
        // Si no se encontró nombre, usar username
        if (empty($nombreCompleto)) {
            $nombreCompleto = ucwords(str_replace('_', ' ', $usuario->username));
        }

        return view('administrativo-index', compact('totalAlumnos','totalMatriculas','totalPagosMes','totalDeudasPendientes', 'usuario', 'nombreCompleto', 'esDirector'));
    }
}
