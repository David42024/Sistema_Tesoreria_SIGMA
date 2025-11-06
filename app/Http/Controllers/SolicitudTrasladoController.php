<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\SolicitudTraslado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class SolicitudTrasladoController extends Controller
{
    /**
     * Muestra la vista principal para generar solicitud de traslado
     */
    public function index()
    {
        return view('gestiones.solicitud-traslado.index');
    }

    /**
     * Busca un alumno por código de educando y retorna sus deudas
     */
    public function buscarAlumno(Request $request)
    {
        $request->validate([
            'codigo_educando' => 'required|string'
        ]);

        $codigo = trim($request->codigo_educando);

        // Buscar alumno
        $alumno = Alumno::where('codigo_educando', $codigo)->first();

        if (!$alumno) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún alumno con ese código de educando'
            ], 404);
        }

        // Buscar deudas pendientes del alumno
        $deudas = Deuda::where('id_alumno', $alumno->id_alumno)
            ->where('estado', 0) // Estado 0 = deuda pendiente
            ->with('concepto')
            ->get();

        // Calcular monto total pendiente
        $montoTotal = 0;
        $deudasFormateadas = [];

        foreach ($deudas as $deuda) {
            $montoPendiente = $deuda->monto_total - $deuda->monto_a_cuenta - $deuda->monto_adelantado;
            $montoTotal += $montoPendiente;

            $deudasFormateadas[] = [
                'id_deuda' => $deuda->id_deuda,
                'concepto' => $deuda->concepto->nombre ?? 'Sin concepto',
                'periodo' => $deuda->periodo,
                'fecha_limite' => $deuda->fecha_limite ? $deuda->fecha_limite->format('d/m/Y') : 'Sin fecha',
                'monto_total' => number_format($deuda->monto_total, 2),
                'monto_pendiente' => number_format($montoPendiente, 2),
            ];
        }

        return response()->json([
            'success' => true,
            'alumno' => [
                'id_alumno' => $alumno->id_alumno,
                'codigo_educando' => $alumno->codigo_educando,
                'nombre_completo' => trim($alumno->primer_nombre . ' ' . $alumno->otros_nombres . ' ' . $alumno->apellido_paterno . ' ' . $alumno->apellido_materno),
                'dni' => $alumno->dni,
                'grado' => $this->obtenerGradoActual($alumno->id_alumno),
            ],
            'tiene_deudas' => count($deudasFormateadas) > 0,
            'deudas' => $deudasFormateadas,
            'monto_total_pendiente' => number_format($montoTotal, 2)
        ]);
    }

    /**
     * Guarda la solicitud de traslado y genera el PDF
     */
    public function guardarSolicitud(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id_alumno',
            'colegio_destino' => 'required|string|max:255',
            'motivo_traslado' => 'required|string',
            'fecha_traslado' => 'required|date',
            'direccion_nuevo_colegio' => 'nullable|string|max:255',
            'telefono_nuevo_colegio' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
        ]);

        // Verificar que el alumno no tenga deudas pendientes
        $alumno = Alumno::findOrFail($request->id_alumno);
        $deudasPendientes = Deuda::where('id_alumno', $alumno->id_alumno)
            ->where('estado', 0)
            ->count();

        if ($deudasPendientes > 0) {
            return response()->json([
                'success' => false,
                'message' => 'El alumno tiene deudas pendientes. No se puede generar la solicitud de traslado.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Generar código de solicitud único
            $codigoSolicitud = 'ST-' . date('Y') . '-' . str_pad(SolicitudTraslado::count() + 1, 4, '0', STR_PAD_LEFT);

            // Crear la solicitud de traslado
            $solicitud = SolicitudTraslado::create([
                'codigo_solicitud' => $codigoSolicitud,
                'id_alumno' => $request->id_alumno,
                'colegio_destino' => $request->colegio_destino,
                'motivo_traslado' => $request->motivo_traslado,
                'fecha_traslado' => $request->fecha_traslado,
                'direccion_nuevo_colegio' => $request->direccion_nuevo_colegio,
                'telefono_nuevo_colegio' => $request->telefono_nuevo_colegio,
                'observaciones' => $request->observaciones,
                'estado' => 'pendiente',
                'fecha_solicitud' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de traslado generada exitosamente',
                'solicitud' => $solicitud,
                'codigo_solicitud' => $codigoSolicitud
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al generar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera el PDF de la solicitud de traslado
     */
    public function generarPDF($codigoSolicitud)
    {
        $solicitud = SolicitudTraslado::where('codigo_solicitud', $codigoSolicitud)
            ->with('alumno')
            ->firstOrFail();

        $alumno = $solicitud->alumno;
        $nombreCompleto = trim($alumno->primer_nombre . ' ' . $alumno->otros_nombres . ' ' . $alumno->apellido_paterno . ' ' . $alumno->apellido_materno);
        $gradoActual = $this->obtenerGradoActual($alumno->id_alumno);

        $data = [
            'solicitud' => $solicitud,
            'alumno' => $alumno,
            'nombre_completo' => $nombreCompleto,
            'grado_actual' => $gradoActual,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
        ];

        // Renderizar la vista HTML
        $html = view('gestiones.solicitud-traslado.pdf', $data)->render();

        // Configurar opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Generar PDF
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nombre del archivo
        $nombreArchivo = 'Solicitud_Traslado_' . str_replace(' ', '_', $nombreCompleto) . '_' . $codigoSolicitud . '.pdf';

        // Retornar PDF para descarga
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"'
        ]);
    }

    /**
     * Obtiene el grado actual del alumno
     */
    private function obtenerGradoActual($idAlumno)
    {
        try {
            $matricula = DB::table('matriculas')
                ->where('matriculas.id_alumno', $idAlumno)
                ->where('matriculas.estado', 1)
                ->orderBy('matriculas.año_escolar', 'desc')
                ->first();

            if ($matricula) {
                // Obtener el nombre del grado
                $grado = DB::table('grados')
                    ->where('id_grado', $matricula->id_grado)
                    ->first();

                if ($grado) {
                    return $grado->nombre_grado . ' - Sección: ' . $matricula->nombreSeccion . ' (' . $matricula->año_escolar . ')';
                }

                return 'Sección: ' . $matricula->nombreSeccion . ' - Año: ' . $matricula->año_escolar;
            }

            return 'Sin matrícula registrada';
        } catch (\Exception $e) {
            Log::error('Error en obtenerGradoActual: ' . $e->getMessage());
            return 'Información no disponible';
        }
    }

    /**
     * Lista todas las solicitudes de traslado
     */
    public function listarSolicitudes()
    {
        $solicitudes = SolicitudTraslado::with('alumno')
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        return view('gestiones.solicitud-traslado.listar', compact('solicitudes'));
    }
}
