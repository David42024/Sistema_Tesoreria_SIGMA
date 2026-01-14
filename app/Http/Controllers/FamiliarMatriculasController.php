<?php

namespace App\Http\Controllers;

use App\Helpers\Home\Familiar\FamiliarHeaderComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;

use App\Http\Controllers\Home\Utils;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\SolicitudPrematricula;
use App\Models\Seccion;
use Illuminate\Http\Request;
use App\Helpers\CRUDTablePage;
use App\Helpers\FilteredSearchQuery;
use App\Helpers\RequestHelper;
use App\Helpers\Tables\CRUDTableComponent;
use App\Helpers\Tables\FilterConfig;
use App\Helpers\Tables\PaginatorRowsSelectorComponent;
use App\Helpers\Tables\SearchBoxComponent;
use App\Helpers\Tables\TableButtonComponent;
use App\Helpers\Tables\TableComponent;
use App\Helpers\Tables\TablePaginator;
use Carbon\Carbon;
use App\Helpers\PreMatricula\PromocionHelper;
use App\Helpers\Tables\ViewBasedComponent;

class FamiliarMatriculasController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [], Request $request)
    {
        $columnMap = [
            'ID' => 'id_matricula',
            'Año Escolar' => 'año_escolar',
            'Escala' => 'escala',
            'Grado' => 'grado.nombre_grado',
            'Sección' => 'nombreSeccion',
        ];

        $query = Matricula::where('estado', '=', true);

        $requested = $request->session()->get('alumno');
        $query->where('id_alumno', '=', $requested->getKey());

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        $query->orderBy('id_periodo_academico', 'desc');

        if ($maxEntriesShow == null) return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function index(Request $request, $long = false)
    {
        $requested = $request->session()->get('alumno');

        if ($requested == null) {
            return redirect(route('principal'));
        }

        $sqlColumns = ['id_matricula', 'año_escolar', 'fecha_matricula', 'escala'];
        $resource = 'pagos';

        $params = RequestHelper::extractSearchParams($request);

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Matrículas")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        $content = CRUDTableComponent::new()
            ->title("Matrículas de tu alumno");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        // Verificar si puede prematricular
        $infoPrematricula = PromocionHelper::obtenerInfoPrematricula($requested->getKey());

        if ($infoPrematricula['puede_prematricular']) {
            $prematriculaButton = new TableButtonComponent("tablesv2.buttons.prematricula", [
                "redirect" => "familiar_matricula_prematricula_create"
            ]);
            $content->addButton($prematriculaButton);
        }

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        if ($long) $paginatorRowsSelector = new PaginatorRowsSelectorComponent([100]);
        $paginatorRowsSelector->valueSelected = $params->showing;
        $content->paginatorRowsSelector($paginatorRowsSelector);

        /* Searchbox */
        $searchBox = new SearchBoxComponent();
        $searchBox->placeholder = "Buscar...";
        $searchBox->value = $params->search;
        $content->searchBox($searchBox);

        /* Lógica del controller */
        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters, $request);

        if ($query->lastPage() > 0 && $params->page > $query->lastPage()) {
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters, $request);
        }

        $gradosExistentes = Grado::select("nombre_grado")
            ->distinct()
            ->where("estado", "=", 1)
            ->pluck("nombre_grado");

        $seccionesExistentes = Seccion::select("nombreSeccion")
            ->distinct()
            ->where("estado", "=", 1)
            ->pluck("nombreSeccion");

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID", "Año Escolar", "Escala", "Grado", "Sección"
        ];
        $filterConfig->filterOptions = [
            "Escala" => ["A", "B", "C", "D", "E"],
            "Grado" => $gradosExistentes,
            "Sección" => $seccionesExistentes,
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Año Escolar", "Fecha", "Escala", "Grado", "Sección", "Tipo", "Observaciones"];
        $table->rows = [];

        foreach ($query as $matricula) {
            $tipo = ($matricula->tipo ?? 'matricula') === 'prematricula'
                ? '📋 Pre-matrícula'
                : '✅ Matrícula';

            array_push($table->rows, [
                $matricula->id_matricula,
                $matricula->periodoAcademico->nombre ?? 'N/A',
                Carbon::parse($matricula->fecha_matricula)->format('d/m/Y'),
                $matricula->escala,
                $matricula->grado->nombre_grado ?? 'N/A',
                $matricula->nombreSeccion ?? 'Por asignar',
                $tipo,
                $matricula->observaciones ?? 'Sin observaciones'
            ]);
        }

        $paginator = new TablePaginator($params->page, $query->lastPage(), [
            'search' => $params->search,
            'showing' => $params->showing,
        ]);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function create(Request $request)
    {
        $alumno = $request->session()->get('alumno');
        if (!$alumno) {
            return redirect(route('principal'));
        }

        // Verificar si ya existe una solicitud de prematrícula para este alumno
        $solicitudExistente = SolicitudPrematricula::where('dni_alumno', $alumno->dni)->first();

        $infoPrematricula = PromocionHelper::obtenerInfoPrematricula($alumno->getKey());

        if (!$infoPrematricula['puede_prematricular']) {
            return redirect()->route('familiar_matricula_view')
                ->with('error', $infoPrematricula['mensaje_error']);
        }

        $header = Utils::crearHeaderConAlumnos($request);

        // Si es alumno nuevo, mostrar formulario especial o estado de solicitud
        if ($infoPrematricula['es_alumno_nuevo']) {
            // Si ya tiene una solicitud, mostrar el estado en lugar del formulario
            if ($solicitudExistente) {
                $content = new ViewBasedComponent('homev2.familiares.prematricula_estado', [
                    'data' => [
                        'return' => route('familiar_matricula_view'),
                        'alumno' => $alumno,
                        'solicitud' => $solicitudExistente,
                        'info_prematricula' => $infoPrematricula,
                    ]
                ]);

                return CRUDTablePage::new()
                    ->title("Estado de Solicitud de Prematrícula")
                    ->header($header)
                    ->sidebar(new FamiliarSidebarComponent())
                    ->content($content)
                    ->render();
            }
            
            $gradosDisponibles = PromocionHelper::obtenerGradosDisponibles();
            
            // Obtener niveles educativos
            $niveles = \App\Models\NivelEducativo::where("estado", "=", "1")
                ->orderBy('nombre_nivel', 'ASC')
                ->get();
            
            // Obtener todos los grados con sus relaciones
            $grados = Grado::where("estado", "=", "1")
                ->with('nivelEducativo')
                ->orderBy('id_nivel', 'ASC')
                ->get();
            
            // Obtener todas las secciones
            $secciones = Seccion::where('estado', 1)
                ->with('grado')
                ->get();
            
            // Obtener secciones agrupadas por grado para el select dinámico
            $seccionesPorGrado = Seccion::where('estado', 1)
                ->get()
                ->groupBy('id_grado')
                ->map(function ($secciones) {
                    return $secciones->map(function ($seccion) {
                        return ['nombreSeccion' => $seccion->nombreSeccion];
                    })->values();
                });

            $content = new ViewBasedComponent('homev2.familiares.prematricula_create_nuevo', [
                'data' => [
                    'return' => route('familiar_matricula_view'),
                    'alumno' => $alumno,
                    'info_prematricula' => $infoPrematricula,
                    'grados_disponibles' => $gradosDisponibles,
                    'niveles' => $niveles,
                    'grados' => $grados,
                    'secciones' => $secciones,
                    'secciones_por_grado' => $seccionesPorGrado,
                ]
            ]);

            return CRUDTablePage::new()
                ->title("Solicitar Prematrícula - Alumno Nuevo")
                ->header($header)
                ->sidebar(new FamiliarSidebarComponent())
                ->content($content)
                ->render();
        }

        // Alumno existente - promoción automática
        $seccionesDisponibles = Seccion::where('id_grado', $infoPrematricula['siguiente_grado']->id_grado)
            ->where('estado', 1)
            ->get();

        $content = new ViewBasedComponent('homev2.familiares.prematricula_create', [
            'data' => [
                'return' => route('familiar_matricula_view'),
                'alumno' => $alumno,
                'info_prematricula' => $infoPrematricula,
                'secciones_disponibles' => $seccionesDisponibles,
            ]
        ]);

        return CRUDTablePage::new()
            ->title("Registrar Prematrícula")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent())
            ->content($content)
            ->render();
    }

    public function store(Request $request)
    {
        $alumno = $request->session()->get('alumno');
        
        if ($alumno == null) {
            return redirect(route('principal'));
        }

        // Obtener el familiar actual desde Auth
        $usuario = auth()->user();
        
        // Buscar el familiar usando id_usuario (primary key correcta)
        $familiar = \App\Models\Familiar::where('estado', true)
            ->where('id_usuario', $usuario->id_usuario)
            ->first();
        
        // Si no lo encuentra con estado=true, buscar sin filtro
        if (!$familiar) {
            $familiar = \App\Models\Familiar::where('id_usuario', $usuario->id_usuario)->first();
        }

        if ($familiar == null) {
            return redirect()->route('familiar_matricula_view')
                ->with('error', 'No se pudo identificar al apoderado. Por favor, contacte con la administración.');
        }

        $infoPrematricula = PromocionHelper::obtenerInfoPrematricula($alumno->getKey());

        if (!$infoPrematricula['puede_prematricular']) {
            return redirect()->route('familiar_matricula_view')
                ->with('error', $infoPrematricula['mensaje_error']);
        }

        // Validación
        $validated = $request->validate([
                'nivel_educativo' => 'required|exists:niveles_educativos,id_nivel',
                'id_grado' => 'required|exists:grados,id_grado',
                'nombreSeccion' => 'required|string|size:1|in:A,B,C,D,E',
                'observaciones' => 'nullable|string|max:255',
            ], [
                'nivel_educativo.required' => 'Debe seleccionar un nivel educativo.',
                'nivel_educativo.exists' => 'El nivel educativo seleccionado no es válido.',
                'id_grado.required' => 'Debe seleccionar un grado.',
                'id_grado.exists' => 'El grado seleccionado no es válido.',
                'nombreSeccion.required' => 'Debe seleccionar una sección.',
                'nombreSeccion.in' => 'La sección debe ser A, B, C, D o E.',
            ]);
            
            // Verificar que la sección existe para el grado seleccionado
            $seccionExiste = Seccion::where('id_grado', $validated['id_grado'])
                ->where('nombreSeccion', $validated['nombreSeccion'])
                ->where('estado', 1)
                ->exists();
            
            if (!$seccionExiste) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'La sección seleccionada no está disponible para el grado elegido.');
            }

        // ========== ALUMNO NUEVO: Crear Solicitud de Prematrícula ==========
        if ($infoPrematricula['es_alumno_nuevo']) {
            // Verificar si ya existe una solicitud para este alumno
            $solicitudExistente = SolicitudPrematricula::where('dni_alumno', $alumno->dni)->first();
            
            if ($solicitudExistente) {
                $mensajeEstado = match($solicitudExistente->estado) {
                    'pendiente' => 'Su solicitud de prematrícula ya fue enviada y está pendiente de revisión.',
                    'aprobado' => 'Su solicitud de prematrícula ya fue aprobada.',
                    'rechazado' => 'Su solicitud anterior fue rechazada. Contacte con la administración para más detalles.',
                    default => 'Ya existe una solicitud de prematrícula para este alumno.'
                };
                
                return redirect()->route('familiar_matricula_view')
                    ->with('info', $mensajeEstado);
            }
            
            try {
                $datosCrear = [
                    // Datos del apoderado (familiar)
                    'dni_apoderado' => $familiar->dni,
                    'apellido_paterno_apoderado' => $familiar->apellido_paterno,
                    'apellido_materno_apoderado' => $familiar->apellido_materno,
                    'primer_nombre_apoderado' => $familiar->primer_nombre,
                    'otros_nombres_apoderado' => $familiar->otros_nombres,
                    'numero_contacto' => $familiar->numero_contacto ?? null,
                    'correo_electronico' => $familiar->correo_electronico ?? null,
                    'parentesco' => 'Apoderado',
                    
                    // Datos del alumno
                    'dni_alumno' => $alumno->dni,
                    'apellido_paterno_alumno' => $alumno->apellido_paterno,
                    'apellido_materno_alumno' => $alumno->apellido_materno ?? null,
                    'primer_nombre_alumno' => $alumno->primer_nombre,
                    'otros_nombres_alumno' => $alumno->otros_nombres ?? null,
                    'sexo' => $alumno->sexo,
                    'fecha_nacimiento' => $alumno->fecha_nacimiento,
                    'direccion_alumno' => $alumno->direccion ?? null,
                    'colegio_procedencia' => $alumno->colegio_procedencia ?? null,
                    'id_grado' => $request->input('id_grado'),
                    'nombreSeccion' => $request->input('nombreSeccion'),
                    'foto_alumno' => $alumno->foto ?? null,
                    
                    // Estado inicial
                    'estado' => 'pendiente',
                    'observaciones' => $request->input('observaciones'),
                ];
                
                $solicitud = SolicitudPrematricula::create($datosCrear);

                return redirect()->route('familiar_matricula_view')
                    ->with('success', 'Solicitud de prematrícula enviada exitosamente. El personal administrativo la revisará pronto.');
            } catch (\Exception $e) {
                return redirect()->route('familiar_matricula_view')
                    ->with('error', 'Error al enviar la solicitud: ' . $e->getMessage());
            }
        }

        // ========== ALUMNO EXISTENTE: Crear Prematrícula directamente ==========
        $idGrado = $infoPrematricula['siguiente_grado']->id_grado;
        $escala = $infoPrematricula['ultima_matricula']->escala ?? $alumno->escala ?? 'A';

        Matricula::create([
            'id_alumno' => $alumno->getKey(),
            'id_grado' => $idGrado,
            'nombreSeccion' => $request->input('nombreSeccion'),
            'año_escolar' => $infoPrematricula['periodo']['año_escolar'],
            'fecha_matricula' => Carbon::now(),
            'escala' => $escala,
            'tipo' => 'prematricula',
            'observaciones' => $request->input('observaciones'),
            'estado' => 1,
        ]);

        return redirect()->route('familiar_matricula_view')
            ->with('success', 'Prematrícula registrada exitosamente para el año escolar ' . $infoPrematricula['periodo']['año_escolar']);
    }

    /**
     * Obtener información financiera del alumno (API para familiares)
     */
    public function getAlumnoInfo(Request $request, $id_alumno)
    {
        // Verificar que el alumno pertenece al familiar actual
        $familiar = $request->session()->get('user');
        $alumno = \App\Models\Alumno::findOrFail($id_alumno);
        
        // Verificar que el familiar tiene acceso a este alumno
        $tieneAcceso = \App\Models\FamiliarAlumno::where('id_familiar', $familiar->getKey())
            ->where('id_alumno', $id_alumno)
            ->exists();
        
        if (!$tieneAcceso) {
            return response()->json(['error' => 'No tiene permiso para acceder a este alumno'], 403);
        }

        // Obtener la escala del alumno
        $escala = $alumno->escala ?? 'A';

        // Buscar el concepto de matrícula para esta escala
        $conceptoMatricula = \App\Models\ConceptoPago::where('tipo', 'matricula')
            ->where('escala', $escala)
            ->where('estado', 1)
            ->first();

        // Buscar el concepto de pensión para esta escala
        $conceptoPension = \App\Models\ConceptoPago::where('tipo', 'pension')
            ->where('escala', $escala)
            ->where('estado', 1)
            ->first();

        return response()->json([
            'escala' => $escala,
            'monto_matricula' => $conceptoMatricula ? $conceptoMatricula->monto : 0,
            'monto_pension' => $conceptoPension ? $conceptoPension->monto : 0,
            'numero_cuotas' => 10,
        ]);
    }
}