<?php

namespace App\Http\Controllers;

use App\Models\SolicitudPrematricula;
use App\Models\Grado;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Familiar;
use App\Models\Matricula;
use App\Models\Seccion;
use App\Models\PeriodoAcademico;
use App\Helpers\PreMatricula\PromocionHelper;
use App\Helpers\CRUDTablePage;
use App\Helpers\RequestHelper;
use App\Helpers\TableAction;
use App\Helpers\Tables\AdministrativoHeaderComponent;
use App\Helpers\Tables\AdministrativoSidebarComponent;
use App\Helpers\Tables\CRUDTableComponent;
use App\Helpers\Tables\FilterConfig;
use App\Helpers\Tables\PaginatorRowsSelectorComponent;
use App\Helpers\Tables\SearchBoxComponent;
use App\Helpers\Tables\TableButtonComponent;
use App\Helpers\Tables\TableComponent;
use App\Helpers\Tables\TablePaginator;
use App\Helpers\FilteredSearchQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SolicitudPrematriculaController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_solicitud',
            'DNI Alumno' => 'dni_alumno',
            'Nombre Alumno' => 'primer_nombre_alumno',
            'Apellido Paterno' => 'apellido_paterno_alumno',
            'DNI Apoderado' => 'dni_apoderado',
            'Estado' => 'estado',
        ];

        $query = SolicitudPrematricula::with(['grado.nivelEducativo']);

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);
        
        $query->orderBy('created_at', 'desc');

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_solicitud', 'dni_alumno', 'primer_nombre_alumno', 'apellido_paterno_alumno', 'apellido_materno_alumno', 'dni_apoderado', 'estado'];
        $resource = 'solicitudes_prematricula';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Solicitudes de Prematrícula")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Solicitudes de Prematrícula");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        
        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "solicitudes_prematricula.index_all"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "solicitudes_prematricula.index"]);
            $params->showing = 100;
        }

        $content->addButton($vermasButton);
        $content->addButton($descargaButton);

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        if ($long)
            $paginatorRowsSelector = new PaginatorRowsSelectorComponent([100]);
        $paginatorRowsSelector->valueSelected = $params->showing;
        $content->paginatorRowsSelector($paginatorRowsSelector);

        /* Searchbox */
        $searchBox = new SearchBoxComponent();
        $searchBox->placeholder = "Buscar por DNI, nombre...";
        $searchBox->value = $params->search;
        $content->searchBox($searchBox);

        /* Lógica del controller */
        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);

        if ($params->page > $query->lastPage()) {
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID",
            "DNI Alumno",
            "Nombre Alumno",
            "Apellido Paterno",
            "DNI Apoderado",
            "Estado"
        ];
        $filterConfig->filterOptions = [
            "Estado" => ["Pendiente", "En_revision", "Aprobada", "Rechazada"]
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Alumno", "DNI", "Nivel", "Grado", "Sección", "Apoderado", "Estado", "Fecha"];
        $table->rows = [];

        foreach ($query as $solicitud) {
            $estadoBadge = match($solicitud->estado) {
                'pendiente' => 'Pendiente',
                'en_revision' => 'En Revisión',
                'aprobado' => 'Aprobado',
                'rechazado' => 'Rechazado',
                default => 'Desconocido',
            };
            
            $estadoClass = match($solicitud->estado) {
                'pendiente' => 'estado-pendiente',
                'en_revision' => 'estado-revision',
                'aprobado' => 'estado-aprobado',
                'rechazado' => 'estado-rechazado',
                default => 'estado-desconocido',
            };

            array_push(
                $table->rows,
                [
                    $solicitud->id_solicitud,                                      // ID
                    $solicitud->nombre_completo_alumno,                            // Alumno
                    $solicitud->dni_alumno,                                        // DNI
                    $solicitud->grado->nivelEducativo->nombre_nivel ?? 'N/A',     // Nivel
                    $solicitud->grado->nombre_grado ?? 'N/A',                     // Grado
                    $solicitud->nombreSeccion ?? 'Sin asignar',                   // Sección
                    $solicitud->nombre_completo_apoderado,                        // Apoderado
                    $estadoBadge,                                                  // Estado
                    $solicitud->created_at->format('d/m/Y'),                      // Fecha
                ]
            );
        }

        $table->actions = [
            new TableAction('view', 'solicitudes_prematricula.show', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), [
            'search' => $params->search,
            'showing' => $params->showing,
            'applied_filters' => $params->applied_filters
        ]);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $data = [
            'titulo' => 'Solicitudes de Prematrícula',
            'contenido' => $content->build()->render(),
        ];

        return view('gestiones.solicitudes_prematricula.index', compact('data'));
    }
    
    // Ver más
    public function indexAll(Request $request)
    {
        return static::index($request, true);
    }
    
    /**
     * Muestra detalle de una solicitud
     */
    public function show($id)
    {
        $solicitud = SolicitudPrematricula::with(['grado.nivelEducativo', 'seccion'])->findOrFail($id);
        
        // Obtener periodos académicos activos
        $periodosAcademicos = PeriodoAcademico::where('estado', 1)
            ->orderBy('nombre', 'desc')
            ->get();
        
        // Obtener periodo académico actual (el más reciente activo)
        $periodoActual = $periodosAcademicos->first();
        
        // Calcular vacantes de la sección solicitada
        $seccionSolicitada = null;
        if ($solicitud->nombreSeccion && $periodoActual) {
            // Buscar la sección solicitada usando id_grado y nombreSeccion
            $seccion = Seccion::where('id_grado', $solicitud->id_grado)
                              ->where('nombreSeccion', $solicitud->nombreSeccion)
                              ->where('estado', 1)
                              ->first();
            
            if ($seccion) {
                $seccionSolicitada = [
                    'matriculados' => $seccion->getAlumnosMatriculadosCount($periodoActual->id_periodo_academico),
                    'capacidad' => $seccion->capacidad_maxima,
                    'vacantes' => $seccion->getVacantesDisponibles($periodoActual->id_periodo_academico),
                    'tiene_espacio' => $seccion->tieneVacantes($periodoActual->id_periodo_academico),
                ];
            }
        }
        
        // Obtener secciones disponibles con información de vacantes
        $seccionesDisponibles = Seccion::where('id_grado', $solicitud->id_grado)
            ->where('estado', 1)
            ->get()
            ->map(function ($seccion) use ($periodoActual) {
                if ($periodoActual) {
                    $seccion->vacantes = $seccion->getVacantesDisponibles($periodoActual->id_periodo_academico);
                    $seccion->matriculados = $seccion->getAlumnosMatriculadosCount($periodoActual->id_periodo_academico);
                } else {
                    $seccion->vacantes = $seccion->capacidad_maxima;
                    $seccion->matriculados = 0;
                }
                return $seccion;
            });
        
        $data = [
            'titulo' => 'Detalle de Solicitud #' . $solicitud->id_solicitud,
            'solicitud' => $solicitud,
            'periodos' => $periodosAcademicos,
            'secciones' => $seccionesDisponibles,
            'periodo_actual' => $periodoActual,
            'seccion_solicitada_info' => $seccionSolicitada,
        ];
        
        return view('gestiones.solicitudes_prematricula.show', compact('data'));
    }
    
    /**
     * Formulario público de solicitud (desde login, sin autenticación)
     */
    public function create()
    {

        $grados = Grado::where('estado', true)->get();

        $parentescos = [
            ['id' => 'Padre', 'descripcion' => 'Padre'],
            ['id' => 'Madre', 'descripcion' => 'Madre'],
            ['id' => 'Tutor Legal', 'descripcion' => 'Tutor Legal'],
            ['id' => 'Abuelo/a', 'descripcion' => 'Abuelo/a'],
            ['id' => 'Tío/a', 'descripcion' => 'Tío/a'],
            ['id' => 'Hermano/a Mayor', 'descripcion' => 'Hermano/a Mayor'],
            ['id' => 'Otro', 'descripcion' => 'Otro'],
        ];

        $sexos = [
            ['id' => 'M', 'descripcion' => 'Masculino'],
            ['id' => 'F', 'descripcion' => 'Femenino'],
        ];

        return view('solicitud_prematricula.create', compact('grados', 'parentescos', 'sexos'));
    }

    /**
     * Guardar solicitud y crear usuario limitado
     */
    public function store(Request $request)
    {
        $request->validate([
            // Datos del apoderado
            'dni_apoderado' => 'required|string|size:8',
            'apellido_paterno_apoderado' => 'required|string|max:50',
            'apellido_materno_apoderado' => 'nullable|string|max:50',
            'primer_nombre_apoderado' => 'required|string|max:50',
            'otros_nombres_apoderado' => 'nullable|string|max:100',
            'numero_contacto' => 'required|string|max:20',
            'correo_electronico' => 'nullable|email|max:100',
            'direccion_apoderado' => 'nullable|string|max:255',
            'parentesco' => 'required|string|max:50',
            
            // Datos del alumno
            'dni_alumno' => 'required|string|size:8|unique:solicitudes_prematricula,dni_alumno|unique:alumnos,dni',
            'apellido_paterno_alumno' => 'required|string|max:50',
            'apellido_materno_alumno' => 'nullable|string|max:50',
            'primer_nombre_alumno' => 'required|string|max:50',
            'otros_nombres_alumno' => 'nullable|string|max:100',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:today',
            'direccion_alumno' => 'nullable|string|max:255',
            'telefono_alumno' => 'nullable|string|max:20',
            'colegio_procedencia' => 'nullable|string|max:100',
            'id_grado' => 'required|exists:grados,id_grado',
            'escala' => 'required|in:A,B,C,D,E',
            
            // Documentos
            'partida_nacimiento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificado_estudios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_alumno' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'dni_alumno.unique' => 'Ya existe una solicitud o alumno registrado con este DNI.',
            'dni_alumno.size' => 'El DNI del alumno debe tener 8 dígitos.',
            'dni_apoderado.size' => 'El DNI del apoderado debe tener 8 dígitos.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'numero_contacto.required' => 'El número de contacto es obligatorio.',
        ]);

        DB::beginTransaction();

        try {
            // Guardar documentos
            $rutaPartida = null;
            $rutaCertificado = null;
            $rutaFoto = null;

            if ($request->hasFile('partida_nacimiento')) {
                $rutaPartida = $request->file('partida_nacimiento')
                    ->store('solicitudes_prematricula/partidas', 'public');
            }

            if ($request->hasFile('certificado_estudios')) {
                $rutaCertificado = $request->file('certificado_estudios')
                    ->store('solicitudes_prematricula/certificados', 'public');
            }

            if ($request->hasFile('foto_alumno')) {
                $rutaFoto = $request->file('foto_alumno')
                    ->store('solicitudes_prematricula/fotos', 'public');
            }

            // Crear usuario limitado para el apoderado (si no existe)
            $usuario = User::where('username', $request->dni_apoderado)->first();
            
            if (!$usuario) {
                $usuario = User::create([
                    'name' => $request->primer_nombre_apoderado . ' ' . $request->apellido_paterno_apoderado,
                    'username' => $request->dni_apoderado,
                    'tipo' => 'PreApoderado',
                    'email' => $request->correo_electronico ?? $request->dni_apoderado . '@temporal.com',
                    'password' => Hash::make($request->dni_apoderado),
                    'estado' => true,
                ]);
            }

            // Crear solicitud
            $solicitud = SolicitudPrematricula::create([
                // Apoderado
                'dni_apoderado' => $request->dni_apoderado,
                'apellido_paterno_apoderado' => $request->apellido_paterno_apoderado,
                'apellido_materno_apoderado' => $request->apellido_materno_apoderado,
                'primer_nombre_apoderado' => $request->primer_nombre_apoderado,
                'otros_nombres_apoderado' => $request->otros_nombres_apoderado,
                'numero_contacto' => $request->numero_contacto,
                'correo_electronico' => $request->correo_electronico,
                'direccion_apoderado' => $request->direccion_apoderado,
                'parentesco' => $request->parentesco,
                // Alumno
                'dni_alumno' => $request->dni_alumno,
                'apellido_paterno_alumno' => $request->apellido_paterno_alumno,
                'apellido_materno_alumno' => $request->apellido_materno_alumno,
                'primer_nombre_alumno' => $request->primer_nombre_alumno,
                'otros_nombres_alumno' => $request->otros_nombres_alumno,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion_alumno' => $request->direccion_alumno,
                'telefono_alumno' => $request->telefono_alumno,
                'colegio_procedencia' => $request->colegio_procedencia,
                'id_grado' => $request->id_grado,
                'escala' => $request->escala,
                // Documentos
                'partida_nacimiento' => $rutaPartida,
                'certificado_estudios' => $rutaCertificado,
                'foto_alumno' => $rutaFoto,
                // Estado
                'estado' => 'pendiente',
                'id_usuario' => $usuario->id_usuario,
            ]);

            DB::commit();

            return redirect()->route('solicitud_prematricula.exito')->with([
                'solicitud_id' => $solicitud->id_solicitud,
                'usuario' => $request->dni_apoderado,
                'password' => $request->dni_apoderado,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        }
    }

    /**
     * Página de éxito con credenciales
     */
    public function exito()
    {
        if (!session('solicitud_id')) {
            return redirect()->route('solicitud_prematricula.create');
        }

        return view('solicitud_prematricula.exito');
    }

    /**
     * Vista del pre-apoderado: Estado de su solicitud
     */
    public function estadoSolicitud(Request $request)
    {
        $usuario = auth()->user();
        
        if ($usuario->tipo !== 'PreApoderado') {
            return redirect()->route('principal');
        }
        
        // Obtener todas las solicitudes del usuario
        $solicitudes = SolicitudPrematricula::where('id_usuario', $usuario->id_usuario)
            ->with('grado')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($solicitudes->isEmpty()) {
            // Si no tiene solicitudes, redirigir a crear una nueva
            return redirect()->route('pre_apoderado.nueva_solicitud');
        }

        // Si se especifica un ID, mostrar esa solicitud, sino la primera
        $solicitudId = $request->get('id');
        
        if ($solicitudId) {
            $solicitud = $solicitudes->firstWhere('id_solicitud', $solicitudId);
            if (!$solicitud) {
                $solicitud = $solicitudes->first();
            }
        } else {
            $solicitud = $solicitudes->first();
        }

        return view('solicitud_prematricula.estado', compact('solicitud', 'solicitudes'));
    }

    /**
     * Formulario para nueva solicitud (usuario ya autenticado)
     */
    public function nuevaSolicitud()
    {
        $usuario = auth()->user();
        
        if ($usuario->tipo !== 'PreApoderado') {
            return redirect()->route('principal');
        }

        // Obtener datos del apoderado de la primera solicitud
        $solicitudExistente = SolicitudPrematricula::where('id_usuario', $usuario->id_usuario)->first();

        $grados = Grado::where('estado', true)->get();

        $parentescos = [
            ['id' => 'Padre', 'descripcion' => 'Padre'],
            ['id' => 'Madre', 'descripcion' => 'Madre'],
            ['id' => 'Tutor Legal', 'descripcion' => 'Tutor Legal'],
            ['id' => 'Abuelo/a', 'descripcion' => 'Abuelo/a'],
            ['id' => 'Tío/a', 'descripcion' => 'Tío/a'],
            ['id' => 'Hermano/a Mayor', 'descripcion' => 'Hermano/a Mayor'],
            ['id' => 'Otro', 'descripcion' => 'Otro'],
        ];

        $sexos = [
            ['id' => 'M', 'descripcion' => 'Masculino'],
            ['id' => 'F', 'descripcion' => 'Femenino'],
        ];

        return view('solicitud_prematricula.nueva', compact('grados', 'parentescos', 'sexos', 'solicitudExistente'));
    }

    /**
     * Guardar nueva solicitud de usuario autenticado
     */
    public function guardarNuevaSolicitud(Request $request)
    {
        $usuario = auth()->user();
        
        if ($usuario->tipo !== 'PreApoderado') {
            return redirect()->route('principal');
        }

        $request->validate([
            'dni_alumno' => 'required|string|size:8|unique:solicitudes_prematricula,dni_alumno|unique:alumnos,dni',
            'apellido_paterno_alumno' => 'required|string|max:50',
            'apellido_materno_alumno' => 'nullable|string|max:50',
            'primer_nombre_alumno' => 'required|string|max:50',
            'otros_nombres_alumno' => 'nullable|string|max:100',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:today',
            'id_grado' => 'required|exists:grados,id_grado',
            'parentesco' => 'required|string|max:50',
        ]);

        // Obtener datos del apoderado de solicitud existente
        $solicitudExistente = SolicitudPrematricula::where('id_usuario', $usuario->id_usuario)->first();

        if (!$solicitudExistente) {
            return back()->withErrors(['error' => 'No se encontró información del apoderado.']);
        }

        try {
            DB::beginTransaction();

            // Crear nueva solicitud con los datos del apoderado existente
            $solicitud = SolicitudPrematricula::create([
                // Datos del apoderado (copiados de la solicitud existente)
                'dni_apoderado' => $solicitudExistente->dni_apoderado,
                'apellido_paterno_apoderado' => $solicitudExistente->apellido_paterno_apoderado,
                'apellido_materno_apoderado' => $solicitudExistente->apellido_materno_apoderado,
                'primer_nombre_apoderado' => $solicitudExistente->primer_nombre_apoderado,
                'otros_nombres_apoderado' => $solicitudExistente->otros_nombres_apoderado,
                'numero_contacto' => $solicitudExistente->numero_contacto,
                'correo_electronico' => $solicitudExistente->correo_electronico,
                'direccion_apoderado' => $solicitudExistente->direccion_apoderado,
                'parentesco' => $request->parentesco,
                
                // Datos del nuevo alumno
                'dni_alumno' => $request->dni_alumno,
                'apellido_paterno_alumno' => $request->apellido_paterno_alumno,
                'apellido_materno_alumno' => $request->apellido_materno_alumno,
                'primer_nombre_alumno' => $request->primer_nombre_alumno,
                'otros_nombres_alumno' => $request->otros_nombres_alumno,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion_alumno' => $request->direccion_alumno,
                'telefono_alumno' => $request->telefono_alumno,
                'colegio_procedencia' => $request->colegio_procedencia,
                'id_grado' => $request->id_grado,
                
                // Estado y usuario
                'estado' => 'pendiente',
                'id_usuario' => $usuario->id_usuario,
            ]);

            // Guardar archivos si se subieron
            if ($request->hasFile('foto_alumno')) {
                $solicitud->foto_alumno = $request->file('foto_alumno')
                    ->storeAs('solicitudes_prematricula', 'foto_' . $solicitud->id_solicitud . '.' . $request->file('foto_alumno')->getClientOriginalExtension(), 'public');
            }

            if ($request->hasFile('partida_nacimiento')) {
                $solicitud->partida_nacimiento = $request->file('partida_nacimiento')
                    ->storeAs('solicitudes_prematricula', 'partida_' . $solicitud->id_solicitud . '.' . $request->file('partida_nacimiento')->getClientOriginalExtension(), 'public');
            }

            if ($request->hasFile('certificado_estudios')) {
                $solicitud->certificado_estudios = $request->file('certificado_estudios')
                    ->storeAs('solicitudes_prematricula', 'certificado_' . $solicitud->id_solicitud . '.' . $request->file('certificado_estudios')->getClientOriginalExtension(), 'public');
            }

            $solicitud->save();

            DB::commit();

            return redirect()->route('pre_apoderado.estado_solicitud', ['id' => $solicitud->id_solicitud])
                ->with('success', 'Solicitud creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la solicitud: ' . $e->getMessage()]);
        }
    }
    /**
     * ADMIN: Aprobar solicitud
     */
    public function aprobar(Request $request, $id)
    {
        $solicitud = SolicitudPrematricula::findOrFail($id);

        if ($solicitud->estado === 'aprobado') {
            return back()->withErrors(['error' => 'Esta solicitud ya fue aprobada.']);
        }

        $request->validate([
            'id_periodo_academico' => 'required|exists:periodos_academicos,id_periodo_academico',
            'nombreSeccion' => 'required|string',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Obtener la sección seleccionada por nombre y grado
        $seccion = Seccion::where('id_grado', $solicitud->id_grado)
                          ->where('nombreSeccion', $request->nombreSeccion)
                          ->where('estado', 1)
                          ->first();
        
        if (!$seccion) {
            return back()->withErrors(['error' => 'La sección seleccionada no es válida para el grado del estudiante.']);
        }
        
        // Verificar vacantes disponibles
        if (!$seccion->tieneVacantes($request->id_periodo_academico)) {
            return back()->withErrors(['error' => 'Por favor seleccione otra sección con vacantes disponibles.']);
        }

        DB::beginTransaction();

        try {
            // 1. Crear el Familiar (Apoderado)
            $familiar = Familiar::updateOrCreate(
                ['dni' => $solicitud->dni_apoderado],
                [
                    'apellido_paterno' => $solicitud->apellido_paterno_apoderado,
                    'apellido_materno' => $solicitud->apellido_materno_apoderado,
                    'primer_nombre' => $solicitud->primer_nombre_apoderado,
                    'otros_nombres' => $solicitud->otros_nombres_apoderado,
                    'numero_contacto' => $solicitud->numero_contacto,
                    'correo_electronico' => $solicitud->correo_electronico,
                    'estado' => true,
                ]
            );

            // 2. Crear o actualizar el Alumno
            $alumno = Alumno::updateOrCreate(
                ['dni' => $solicitud->dni_alumno],
                [
                    'apellido_paterno' => $solicitud->apellido_paterno_alumno,
                    'apellido_materno' => $solicitud->apellido_materno_alumno,
                    'primer_nombre' => $solicitud->primer_nombre_alumno,
                    'otros_nombres' => $solicitud->otros_nombres_alumno,
                    'sexo' => $solicitud->sexo,
                    'fecha_nacimiento' => $solicitud->fecha_nacimiento,
                    'direccion' => $solicitud->direccion_alumno ?? '',
                    'colegio_procedencia' => $solicitud->colegio_procedencia,
                    'escala' => 'A', // Escala por defecto
                    'año_ingreso' => date('Y'),
                    'estado' => true,
                ]
            );

            // 3. Vincular Familiar con Alumno (si no existe ya la relación)
            if (!$familiar->alumnos()->where('alumnos.id_alumno', $alumno->id_alumno)->exists()) {
                $familiar->alumnos()->attach($alumno->id_alumno, [
                    'parentesco' => $solicitud->parentesco,
                ]);
            }

            // 4. Crear la Matrícula en el periodo académico seleccionado
            $matricula = Matricula::create([
                'id_alumno' => $alumno->id_alumno,
                'id_grado' => $solicitud->id_grado,
                'nombreSeccion' => $seccion->nombreSeccion,
                'id_periodo_academico' => $request->id_periodo_academico,
                'escala' => $solicitud->escala,
                'fecha_matricula' => now(),
                'tipo' => 'prematricula',
                'observaciones' => $request->observaciones ?? 'Prematrícula aprobada desde solicitud #' . $solicitud->id_solicitud,
                'estado' => 1,
            ]);

            // 5. Generar deudas automáticamente para el estudiante
            $matricula->generarDeudas();

            // 6. Actualizar solicitud
            $solicitud->update([
                'estado' => 'aprobado',
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return back()->with('success', 'Solicitud aprobada exitosamente. El alumno ha sido matriculado en la sección ' . $seccion->nombreSeccion . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al aprobar: ' . $e->getMessage()]);
        }
    }

    /**
     * ADMIN: Rechazar solicitud
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        $solicitud = SolicitudPrematricula::findOrFail($id);

        $solicitud->update([
            'estado' => 'rechazada',
            'motivo_rechazo' => $request->motivo_rechazo,
            'revisado_por' => auth()->id(),
            'fecha_revision' => now(),
        ]);

        return back()->with('success', 'Solicitud rechazada.');
    }
    
    /**
     * Marca una solicitud como "en revisión"
     */
    public function marcarEnRevision($id)
    {
        $solicitud = SolicitudPrematricula::findOrFail($id);
        
        if ($solicitud->estado === 'pendiente') {
            $solicitud->update([
                'estado' => 'en_revision',
            ]);
            
            return back()->with('success', 'Solicitud marcada como "En Revisión".');
        }
        
        return back()->with('error', 'Solo se pueden marcar como "En Revisión" las solicitudes pendientes.');
    }
}