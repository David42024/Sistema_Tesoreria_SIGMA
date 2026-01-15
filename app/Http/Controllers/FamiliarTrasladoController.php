<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\SolicitudTraslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\Home\Utils;
use App\Helpers\CRUDTablePage;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;

class FamiliarTrasladoController extends Controller
{
    /**
     * Muestra la vista de traslado regular
     */
    public function indexRegular(Request $request)
    {
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('principal')->with('error', 'Debe seleccionar un alumno primero');
        }

        // Crear header y page para el layout
        $header = Utils::crearHeaderConAlumnos($request);
        $page = CRUDTablePage::new()
            ->title("Traslado Regular")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        // Obtener matrícula activa
        $matriculaActiva = $alumno->matriculas()
            ->where('estado', true)
            ->orderBy('id_periodo_academico', 'desc')
            ->first();

        // Obtener todas las deudas activas del alumno del año completo (marzo a diciembre)
        $yearActual = Carbon::now()->year;
        $deudasQuery = Deuda::where('id_alumno', $alumno->id_alumno)
            ->where('estado', true) // Estado true = deuda activa/vigente
            ->whereYear('fecha_limite', $yearActual)
            ->whereMonth('fecha_limite', '>=', 3) // Desde marzo
            ->whereMonth('fecha_limite', '<=', 12) // Hasta diciembre
            ->with('concepto')
            ->orderBy('fecha_limite', 'asc')
            ->get();

        // Formatear deudas para la vista, filtrando solo las que tienen saldo pendiente
        $deudasFormateadas = collect();
        foreach ($deudasQuery as $deuda) {
            $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;

            // Solo incluir si tiene saldo pendiente
            if ($montoPendiente > 0.01) {
                $deudasFormateadas->push([
                    'id_deuda' => $deuda->id_deuda,
                    'concepto' => $deuda->concepto->nombre ?? 'Sin concepto',
                    'periodo' => $deuda->periodo,
                    'fecha_limite' => $deuda->fecha_limite,
                    'fecha_limite_formatted' => $deuda->fecha_limite ? Carbon::parse($deuda->fecha_limite)->format('d/m/Y') : 'Sin fecha',
                    'monto_total' => $deuda->monto_total,
                    'monto_pendiente' => $montoPendiente,
                    'monto_total_formatted' => number_format($deuda->monto_total, 2),
                    'monto_pendiente_formatted' => number_format($montoPendiente, 2),
                ]);
            }
        }

        $data = [
            'alumno' => $alumno,
            'matriculaActiva' => $matriculaActiva,
            'deudas' => $deudasFormateadas,
            'tieneDeudas' => $deudasFormateadas->count() > 0,
            'page' => $page
        ];

        return view('gestiones.familiar.traslado-regular', $data);
    }

    /**
     * Muestra la vista de traslado excepcional con datos del alumno
     */
    public function indexExcepcional(Request $request)
    {
        $alumno = session('alumno');

        if (!$alumno) {
            return redirect()->route('principal')->with('error', 'Debe seleccionar un alumno primero');
        }

        // Crear header y page para el layout
        $header = Utils::crearHeaderConAlumnos($request);
        $page = CRUDTablePage::new()
            ->title("Traslado Excepcional")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        // Obtener matrícula activa
        $matriculaActiva = $alumno->matriculas()
            ->where('estado', true)
            ->orderBy('id_periodo_academico', 'desc')
            ->first();

        // Obtener todas las deudas activas del alumno
        $deudasQuery = Deuda::where('id_alumno', $alumno->id_alumno)
            ->where('estado', true) // Estado true = deuda activa/vigente
            ->with('concepto')
            ->orderBy('fecha_limite', 'asc')
            ->get();

        // Formatear deudas para la vista, filtrando solo las que tienen saldo pendiente
        $deudasFormateadas = collect();
        foreach ($deudasQuery as $deuda) {
            $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;

            // Solo incluir si tiene saldo pendiente
            if ($montoPendiente > 0.01) {
                $deudasFormateadas->push([
                    'id_deuda' => $deuda->id_deuda,
                    'concepto' => $deuda->concepto->nombre ?? 'Sin concepto',
                    'periodo' => $deuda->periodo,
                    'fecha_limite' => $deuda->fecha_limite,
                    'fecha_limite_formatted' => $deuda->fecha_limite ? Carbon::parse($deuda->fecha_limite)->format('d/m/Y') : 'Sin fecha',
                    'monto_total' => $deuda->monto_total,
                    'monto_pendiente' => $montoPendiente,
                    'monto_total_formatted' => number_format($deuda->monto_total, 2),
                    'monto_pendiente_formatted' => number_format($montoPendiente, 2),
                ]);
            }
        }

        $data = [
            'alumno' => $alumno,
            'matriculaActiva' => $matriculaActiva,
            'deudas' => $deudasFormateadas,
            'tieneDeudas' => $deudasFormateadas->count() > 0,
            'page' => $page
        ];

        return view('gestiones.familiar.traslado-excepcional', $data);
    }

    /**
     * Verifica si hay deudas pendientes hasta la fecha de traslado
     */
    public function verificarDeudas(Request $request)
    {
        $request->validate([
            'fecha_traslado' => 'required|date'
        ]);

        $alumno = session('alumno');

        if (!$alumno) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del alumno'
            ], 404);
        }

        try {
            $fechaTraslado = Carbon::parse($request->fecha_traslado);

            // Buscar deudas activas cuya fecha límite sea igual o anterior a la fecha de traslado
            $deudasActivasQuery = Deuda::where('id_alumno', $alumno->id_alumno)
                ->where('estado', true) // Estado true = deuda activa/vigente
                ->whereDate('fecha_limite', '<=', $fechaTraslado)
                ->with('concepto')
                ->orderBy('fecha_limite', 'asc')
                ->get();

            // Filtrar solo las deudas que tienen saldo pendiente
            $deudasPendientes = collect();
            $montoTotal = 0;
            $deudasDetalle = [];

            foreach ($deudasActivasQuery as $deuda) {
                $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;

                // Solo considerar si tiene saldo pendiente (mayor a 0.01 para evitar problemas de redondeo)
                if ($montoPendiente > 0.01) {
                    $deudasPendientes->push($deuda);
                    $montoTotal += $montoPendiente;

                    $deudasDetalle[] = [
                        'concepto' => $deuda->concepto->nombre ?? 'Sin concepto',
                        'periodo' => $deuda->periodo,
                        'fecha_limite' => Carbon::parse($deuda->fecha_limite)->format('d/m/Y'),
                        'monto_pendiente' => number_format($montoPendiente, 2)
                    ];
                }
            }

            $tieneDeudas = $deudasPendientes->count() > 0;

            return response()->json([
                'success' => true,
                'tiene_deudas' => $tieneDeudas,
                'cantidad_deudas' => $deudasPendientes->count(),
                'monto_total_pendiente' => number_format($montoTotal, 2),
                'deudas' => $deudasDetalle,
                'message' => $tieneDeudas
                    ? 'El alumno tiene ' . $deudasPendientes->count() . ' deuda(s) pendiente(s) hasta la fecha de traslado'
                    : '✓ El alumno no tiene deudas pendientes hasta la fecha de traslado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar deudas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar deudas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda la solicitud de traslado excepcional
     */
    public function guardarSolicitud(Request $request)
    {
        $request->validate([
            'colegio_destino' => 'required|string|max:255',
            'fecha_traslado' => 'required|date',
            'direccion_nuevo_colegio' => 'nullable|string|max:255',
            'telefono_nuevo_colegio' => 'nullable|string|max:20',
            'motivo_traslado' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        $alumno = session('alumno');

        if (!$alumno) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del alumno'
            ], 404);
        }

        try {
            $fechaTraslado = Carbon::parse($request->fecha_traslado);

            // Verificar nuevamente que no tenga deudas pendientes hasta la fecha de traslado
            $deudasActivasQuery = Deuda::where('id_alumno', $alumno->id_alumno)
                ->where('estado', true) // Estado true = deuda activa/vigente
                ->whereDate('fecha_limite', '<=', $fechaTraslado)
                ->get();

            // Contar solo deudas con saldo pendiente
            $cantidadDeudasPendientes = 0;
            foreach ($deudasActivasQuery as $deuda) {
                $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;
                if ($montoPendiente > 0.01) {
                    $cantidadDeudasPendientes++;
                }
            }

            if ($cantidadDeudasPendientes > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El alumno tiene deudas pendientes hasta la fecha de traslado. Por favor, regularice los pagos antes de solicitar el traslado.',
                    'tiene_deudas' => true
                ], 400);
            }

            DB::beginTransaction();

            // Generar código de solicitud único
            $ultimaSolicitud = SolicitudTraslado::orderBy('id_solicitud', 'desc')->first();
            $numeroSiguiente = $ultimaSolicitud ? ($ultimaSolicitud->id_solicitud + 1) : 1;
            $codigoSolicitud = 'STE-' . date('Y') . '-' . str_pad($numeroSiguiente, 4, '0', STR_PAD_LEFT);

            // Crear la solicitud de traslado
            $solicitud = SolicitudTraslado::create([
                'codigo_solicitud' => $codigoSolicitud,
                'id_alumno' => $alumno->id_alumno,
                'colegio_destino' => $request->colegio_destino,
                'motivo_traslado' => $request->motivo_traslado,
                'fecha_traslado' => $fechaTraslado,
                'direccion_nuevo_colegio' => $request->direccion_nuevo_colegio,
                'telefono_nuevo_colegio' => $request->telefono_nuevo_colegio,
                'observaciones' => $request->observaciones,
                'estado' => 'pendiente',
                'fecha_solicitud' => now(),
                'tipo_solicitud' => 'excepcional' // Identificar que es solicitud de familiar
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de traslado excepcional enviada exitosamente. Código: ' . $codigoSolicitud,
                'codigo_solicitud' => $codigoSolicitud,
                'solicitud' => $solicitud
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar solicitud de traslado: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica si hay deudas pendientes del año completo (marzo a diciembre)
     */
    public function verificarDeudasRegular(Request $request)
    {
        $alumno = session('alumno');

        if (!$alumno) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del alumno'
            ], 404);
        }

        try {
            $yearActual = Carbon::now()->year;

            // Buscar deudas activas del año completo (marzo a diciembre)
            $deudasActivasQuery = Deuda::where('id_alumno', $alumno->id_alumno)
                ->where('estado', true) // Estado true = deuda activa/vigente
                ->whereYear('fecha_limite', $yearActual)
                ->whereMonth('fecha_limite', '>=', 3) // Desde marzo
                ->whereMonth('fecha_limite', '<=', 12) // Hasta diciembre
                ->with('concepto')
                ->orderBy('fecha_limite', 'asc')
                ->get();

            // Filtrar solo las deudas que tienen saldo pendiente
            $deudasPendientes = collect();
            $montoTotal = 0;
            $deudasDetalle = [];

            foreach ($deudasActivasQuery as $deuda) {
                $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;

                // Solo considerar si tiene saldo pendiente (mayor a 0.01 para evitar problemas de redondeo)
                if ($montoPendiente > 0.01) {
                    $deudasPendientes->push($deuda);
                    $montoTotal += $montoPendiente;

                    $deudasDetalle[] = [
                        'concepto' => $deuda->concepto->nombre ?? 'Sin concepto',
                        'periodo' => $deuda->periodo,
                        'fecha_limite' => Carbon::parse($deuda->fecha_limite)->format('d/m/Y'),
                        'monto_pendiente' => number_format($montoPendiente, 2)
                    ];
                }
            }

            $tieneDeudas = $deudasPendientes->count() > 0;

            return response()->json([
                'success' => true,
                'tiene_deudas' => $tieneDeudas,
                'cantidad_deudas' => $deudasPendientes->count(),
                'monto_total_pendiente' => number_format($montoTotal, 2),
                'deudas' => $deudasDetalle,
                'message' => $tieneDeudas
                    ? 'El alumno tiene ' . $deudasPendientes->count() . ' deuda(s) pendiente(s) del año escolar'
                    : '✓ El alumno no tiene deudas pendientes del año escolar'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar deudas regulares: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar deudas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda la solicitud de traslado regular
     */
    public function guardarSolicitudRegular(Request $request)
    {
        $request->validate([
            'colegio_destino' => 'required|string|max:255',
            'direccion_nuevo_colegio' => 'nullable|string|max:255',
            'telefono_nuevo_colegio' => 'nullable|string|max:20',
            'motivo_traslado' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        $alumno = session('alumno');

        if (!$alumno) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del alumno'
            ], 404);
        }

        try {
            $yearActual = Carbon::now()->year;

            // Verificar nuevamente que no tenga deudas pendientes del año completo (marzo a diciembre)
            $deudasActivasQuery = Deuda::where('id_alumno', $alumno->id_alumno)
                ->where('estado', true) // Estado true = deuda activa/vigente
                ->whereYear('fecha_limite', $yearActual)
                ->whereMonth('fecha_limite', '>=', 3) // Desde marzo
                ->whereMonth('fecha_limite', '<=', 12) // Hasta diciembre
                ->get();

            // Contar solo deudas con saldo pendiente
            $cantidadDeudasPendientes = 0;
            foreach ($deudasActivasQuery as $deuda) {
                $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;
                if ($montoPendiente > 0.01) {
                    $cantidadDeudasPendientes++;
                }
            }

            if ($cantidadDeudasPendientes > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El alumno tiene deudas pendientes del año escolar. Por favor, regularice los pagos antes de solicitar el traslado regular.',
                    'tiene_deudas' => true
                ], 400);
            }

            DB::beginTransaction();

            // Generar código de solicitud único
            $ultimaSolicitud = SolicitudTraslado::orderBy('id_solicitud', 'desc')->first();
            $numeroSiguiente = $ultimaSolicitud ? ($ultimaSolicitud->id_solicitud + 1) : 1;
            $codigoSolicitud = 'STR-' . date('Y') . '-' . str_pad($numeroSiguiente, 4, '0', STR_PAD_LEFT);

            // Crear la solicitud de traslado - fecha automática es hoy
            $solicitud = SolicitudTraslado::create([
                'codigo_solicitud' => $codigoSolicitud,
                'id_alumno' => $alumno->id_alumno,
                'colegio_destino' => $request->colegio_destino,
                'motivo_traslado' => $request->motivo_traslado,
                'fecha_traslado' => now(), // Fecha automática
                'direccion_nuevo_colegio' => $request->direccion_nuevo_colegio,
                'telefono_nuevo_colegio' => $request->telefono_nuevo_colegio,
                'observaciones' => $request->observaciones,
                'estado' => 'pendiente',
                'fecha_solicitud' => now(),
                'tipo_solicitud' => 'regular' // Identificar que es traslado regular
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de traslado regular enviada exitosamente. Código: ' . $codigoSolicitud,
                'codigo_solicitud' => $codigoSolicitud,
                'solicitud' => $solicitud
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar solicitud de traslado regular: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
}
