<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Matricula;
use App\Models\SolicitudPrematricula;
use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\PeriodoAcademico;

class ReporteAcademicoController extends Controller
{
    public function index(Request $request)
    {
        // Obtener filtros
        $periodoId = $request->input('periodo_academico', 'all');
        $nivelId = $request->input('nivel_educativo');
        $gradoId = $request->input('grado');
        $estadoSolicitud = $request->input('estado_solicitud');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Opciones para filtros
        $periodos = PeriodoAcademico::where('estado', 1)
            ->orderBy('nombre', 'desc')
            ->get();
        
        $niveles = NivelEducativo::where('estado', 1)->get();
        $grados = Grado::where('estado', 1)->get();

        // ===== GRÁFICO 1: Distribución por Nivel Educativo =====
        $nivelQuery = Matricula::select('niveles_educativos.nombre_nivel', DB::raw('COUNT(DISTINCT matriculas.id_alumno) as total'))
            ->join('grados', 'matriculas.id_grado', '=', 'grados.id_grado')
            ->join('niveles_educativos', 'grados.id_nivel', '=', 'niveles_educativos.id_nivel')
            ->where('matriculas.estado', 1);

        if ($periodoId !== 'all') {
            $nivelQuery->where('matriculas.id_periodo_academico', $periodoId);
        }        if ($fechaInicio && $fechaFin) {
            $nivelQuery->whereBetween('matriculas.created_at', [$fechaInicio, $fechaFin]);
        }
        $distribucionNivel = $nivelQuery->groupBy('niveles_educativos.nombre_nivel')
            ->orderBy('niveles_educativos.nombre_nivel')
            ->get();

        $nivelesLabels = $distribucionNivel->pluck('nombre_nivel')->toArray();
        $nivelesData = $distribucionNivel->pluck('total')->toArray();

        // ===== GRÁFICO 2: Matrículas por Grado =====
        $gradoQuery = Matricula::select(
                'grados.nombre_grado',
                'niveles_educativos.nombre_nivel',
                DB::raw('COUNT(DISTINCT matriculas.id_alumno) as total')
            )
            ->join('grados', 'matriculas.id_grado', '=', 'grados.id_grado')
            ->join('niveles_educativos', 'grados.id_nivel', '=', 'niveles_educativos.id_nivel')
            ->where('matriculas.estado', 1);

        if ($periodoId !== 'all') {
            $gradoQuery->where('matriculas.id_periodo_academico', $periodoId);
        }
        if ($nivelId) {
            $gradoQuery->where('grados.id_nivel', $nivelId);
        }        if ($fechaInicio && $fechaFin) {
            $gradoQuery->whereBetween('matriculas.created_at', [$fechaInicio, $fechaFin]);
        }
        $matriculasPorGrado = $gradoQuery->groupBy('grados.nombre_grado', 'niveles_educativos.nombre_nivel')
            ->orderBy('niveles_educativos.nombre_nivel')
            ->orderBy('grados.nombre_grado')
            ->get();

        $gradosLabels = $matriculasPorGrado->map(function($item) {
            return $item->nombre_grado . ' (' . substr($item->nombre_nivel, 0, 3) . ')';
        })->toArray();
        $gradosData = $matriculasPorGrado->pluck('total')->toArray();

        // ===== GRÁFICO 3: Distribución por Escala =====
        $escalaQuery = Matricula::select('escala', DB::raw('COUNT(*) as total'))
            ->where('estado', 1);

        if ($periodoId !== 'all') {
            $escalaQuery->where('id_periodo_academico', $periodoId);
        }
        if ($nivelId) {
            $escalaQuery->whereHas('grado', function($q) use ($nivelId) {
                $q->where('id_nivel', $nivelId);
            });
        }
        if ($gradoId) {
            $escalaQuery->where('id_grado', $gradoId);
        }
        if ($fechaInicio && $fechaFin) {
            $escalaQuery->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $distribucionEscala = $escalaQuery->groupBy('escala')
            ->orderBy('escala')
            ->get();

        $escalasLabels = $distribucionEscala->pluck('escala')->map(function($escala) {
            return 'Escala ' . $escala;
        })->toArray();
        $escalasData = $distribucionEscala->pluck('total')->toArray();

        // ===== GRÁFICO 4: Distribución por Sexo =====
        $sexoQuery = Matricula::select('alumnos.sexo', DB::raw('COUNT(DISTINCT matriculas.id_alumno) as total'))
            ->join('alumnos', 'matriculas.id_alumno', '=', 'alumnos.id_alumno')
            ->where('matriculas.estado', 1);

        if ($periodoId !== 'all') {
            $sexoQuery->where('matriculas.id_periodo_academico', $periodoId);
        }        if ($fechaInicio && $fechaFin) {
            $sexoQuery->whereBetween('matriculas.created_at', [$fechaInicio, $fechaFin]);
        }
        $distribucionSexo = $sexoQuery->groupBy('alumnos.sexo')
            ->get();

        $sexoLabels = $distribucionSexo->pluck('sexo')->toArray();
        $sexoData = $distribucionSexo->pluck('total')->toArray();

        // ===== GRÁFICO 5: Estado de Prematrículas =====
        $prematQuery = SolicitudPrematricula::select('estado', DB::raw('COUNT(*) as total'));

        if ($gradoId) {
            $prematQuery->where('id_grado', $gradoId);
        }
        if ($estadoSolicitud) {
            $prematQuery->where('estado', $estadoSolicitud);
        }
        if ($fechaInicio && $fechaFin) {
            $prematQuery->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $estadoPremat = $prematQuery->groupBy('estado')->get();

        $estadosLabels = $estadoPremat->pluck('estado')->map(function($estado) {
            return match($estado) {
                'pendiente' => 'Pendiente',
                'en_revision' => 'En Revisión',
                'aprobado' => 'Aprobada',
                'rechazado' => 'Rechazada',
                default => ucfirst($estado)
            };
        })->toArray();
        $estadosData = $estadoPremat->pluck('total')->toArray();

        // ===== GRÁFICO 6: Solicitudes por Mes =====
        $solicitudesMesQuery = SolicitudPrematricula::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            );
        
        if ($fechaInicio && $fechaFin) {
            $solicitudesMesQuery->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        } else {
            $solicitudesMesQuery->whereYear('created_at', '>=', date('Y') - 1);
        }
        
        $solicitudesMes = $solicitudesMesQuery
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        $mesesLabels = $solicitudesMes->map(function($item) {
            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            return $meses[$item->mes - 1] . ' ' . $item->año;
        })->toArray();
        $mesesData = $solicitudesMes->pluck('total')->toArray();

        // ===== GRÁFICO 7: Tasa de Aprobación =====
        $aprobacionQuery = SolicitudPrematricula::select(
                'grados.nombre_grado',
                DB::raw("SUM(CASE WHEN solicitudes_prematricula.estado = 'aprobado' THEN 1 ELSE 0 END) as aprobadas"),
                DB::raw("SUM(CASE WHEN solicitudes_prematricula.estado = 'rechazado' THEN 1 ELSE 0 END) as rechazadas")
            )
            ->join('grados', 'solicitudes_prematricula.id_grado', '=', 'grados.id_grado');
        
        if ($fechaInicio && $fechaFin) {
            $aprobacionQuery->whereBetween('solicitudes_prematricula.created_at', [$fechaInicio, $fechaFin]);
        }
        
        $aprobacionQuery = $aprobacionQuery
            ->groupBy('grados.nombre_grado')
            ->orderBy('grados.nombre_grado')
            ->get();

        $aprobLabels = $aprobacionQuery->pluck('nombre_grado')->toArray();
        $aprobadas = $aprobacionQuery->pluck('aprobadas')->toArray();
        $rechazadas = $aprobacionQuery->pluck('rechazadas')->toArray();

        // ===== GRÁFICO 8: Capacidad vs Ocupación =====
        $subQueryFechas = '';
        if ($fechaInicio && $fechaFin) {
            $subQueryFechas = ' AND matriculas.created_at BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '"';
        }
        
        $capacidadQuery = DB::table('secciones')
            ->select(
                'secciones.id_grado',
                'secciones.nombreSeccion',
                'grados.nombre_grado',
                'secciones.capacidad_maxima',
                DB::raw('(SELECT COUNT(*) FROM matriculas WHERE matriculas.id_grado = secciones.id_grado AND matriculas.nombreSeccion = secciones.nombreSeccion AND matriculas.estado = 1' . $subQueryFechas . ') as matriculados')
            )
            ->join('grados', 'secciones.id_grado', '=', 'grados.id_grado')
            ->where('secciones.estado', 1)
            ->orderBy('matriculados', 'desc')
            ->limit(10)
            ->get();

        $capacidadLabels = $capacidadQuery->map(function($item) {
            return $item->nombre_grado . ' - ' . $item->nombreSeccion;
        })->toArray();
        $capacidadMaxima = $capacidadQuery->pluck('capacidad_maxima')->toArray();
        $capacidadOcupada = $capacidadQuery->pluck('matriculados')->toArray();

        return view('gestiones.reportes.academicos', compact(
            'periodos',
            'niveles',
            'grados',
            // Gráfico 1
            'nivelesLabels',
            'nivelesData',
            // Gráfico 2
            'gradosLabels',
            'gradosData',
            // Gráfico 3
            'escalasLabels',
            'escalasData',
            // Gráfico 4
            'sexoLabels',
            'sexoData',
            // Gráfico 5
            'estadosLabels',
            'estadosData',
            // Gráfico 6
            'mesesLabels',
            'mesesData',
            // Gráfico 7
            'aprobLabels',
            'aprobadas',
            'rechazadas',
            // Gráfico 8
            'capacidadLabels',
            'capacidadMaxima',
            'capacidadOcupada'
        ));
    }
}
