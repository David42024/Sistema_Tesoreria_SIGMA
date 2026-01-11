<?php

namespace App\Http\Controllers;

use App\Helpers\ArrayableTableAction;
use App\Helpers\FilteredSearchQuery;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Helpers\CRUDTablePage;
use App\Helpers\ExcelExportHelper;
use App\Helpers\PDFExportHelper;
use App\Helpers\RequestHelper;
use App\Helpers\TableAction;
use App\Helpers\Tables\AdministrativoHeaderComponent;
use App\Helpers\Tables\AdministrativoSidebarComponent;
use App\Helpers\Tables\CautionModalComponent;
use App\Helpers\Tables\CRUDTableComponent;
use App\Helpers\Tables\FilterConfig;
use App\Helpers\Tables\PaginatorRowsSelectorComponent;
use App\Helpers\Tables\SearchBoxComponent;
use App\Helpers\Tables\TableButtonComponent;
use App\Helpers\Tables\TableComponent;
use App\Helpers\Tables\TablePaginator;
use App\Http\Controllers\Controller;

class SeccionController extends Controller
{

    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = []){
        $columnMap = [
            'Nivel Educativo' => 'NivelEducativo.nombre_nivel',
            'Grado' => 'Grado.nombre_grado',
            'Seccion' => 'nombreSeccion',
        ];

        $query = Seccion::where('estado', '=', true);

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null) return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function index(Request $request, $long = false){
        $sqlColumns = ["NivelEducativo.nombre_nivel", "Grado.nombre_grado", "nombreSeccion"];
        $resource = 'academica';

        $params = RequestHelper::extractSearchParams($request);
        
        $page = CRUDTablePage::new()
            ->title("Secciones")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());
        
        $content = CRUDTableComponent::new()
            ->title("Secciones");

        /* Definición de botones */
        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "seccion_create"]);

        if (!$long){
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "seccion_viewAll"]);
        } else {
            $params->showing = 100;
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "seccion_view"]);
        }

        $content->addButton($filterButton);
        $content->addButton($vermasButton);
        $content->addButton($descargaButton);
        $content->addButton($createNewEntryButton);

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

        /* Modals usados */
        $cautionModal = CautionModalComponent::new()
            ->cautionMessage('¿Estás seguro?')
            ->action('Estás eliminando la Sección')
            ->columns(["Nivel Educativo", "Grado", "Seccion"])
            ->rows(['', '', ''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a esta Sección')
            ->confirmButton('Sí, bórralo')
            ->cancelButton('Cancelar')
            ->isForm(true)
            ->dataInputName('id')
            ->build();

        $page->modals([$cautionModal]);

        /* Lógica del controller */
        
        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);

        if ($params->page > $query->lastPage()){
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        $nivelesExistentes = NivelEducativo::select("nombre_nivel")
            ->distinct()
            ->where("estado", "=", 1)
            ->pluck("nombre_nivel");

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
            "Nivel Educativo", "Grado", "Seccion"
        ];
        $filterConfig->filterOptions = [
            "Nivel Educativo" => $nivelesExistentes,
            "Grado" => $gradosExistentes,
            "Seccion" => $seccionesExistentes
        ];
        $content->filterConfig = $filterConfig;
        
        $table = new TableComponent();
        $table->columns = ["#", "Nivel Educativo", "Grado", "Seccion"];
        $table->rows = [];

        $num = $params->showing * ($params->page - 1);
        foreach ($query as $seccion){
            array_push($table->rows,
            [
                ++$num,
                $seccion->grado->niveleducativo->nombre_nivel,
                $seccion->grado->nombre_grado,
                $seccion->nombreSeccion,
                $seccion->id_grado, // Está oculto ya que no tiene una columna asignada.
            ]); 
        }

        $table->actions = [
            new TableAction('seccion_edit', 'seccion_edit', $resource),
            new TableAction('delete', '', $resource),
            new TableAction('seccion_view_details', 'seccion_view_details', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), ['search' => $params->search,
        'showing' => $params->showing,
        'applied_filters' => $params->applied_filters
    ]);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function viewAll(Request $request){
        return static::index($request, true);
    }

    private static function fallbackDoSearch($sqlColumns, $search, $pagination){
        if (!isset($search)){
            $secciones = Seccion::where('estado', '=', '1')->paginate($pagination);
        } else {
            $secciones = Seccion::where('estado', '=', '1')
                ->whereAny($sqlColumns, 'LIKE', "%{$search}%")
                ->paginate($pagination);
        }

        return $secciones;
    }

    public function fallback(Request $request)
    {
        $sqlColumns = ["id_grado","nombreSeccion"];
        $tipoDeRecurso = "academica";

        $pagination = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

        if (!is_numeric($paginaActual) || $paginaActual <= 0) $paginaActual = 1;
        if (!is_numeric($pagination) || $pagination <= 0) $pagination = 10;

        $secciones = SeccionController::fallbackDoSearch($sqlColumns, $search, $pagination);

        if ($paginaActual > $secciones->lastPage()){
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $secciones = SeccionController::fallbackDoSearch($sqlColumns, $search, $pagination);
        }

        $data = [
            'titulo' => 'Secciones',
            'columnas' => [
                '#',
                'Nivel Educativo',
                'Grado',
                'Seccion'
            ],
            'filas' => [],
            'showing' => $pagination,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $secciones->lastPage(),
            'resource' => $tipoDeRecurso,
            'view' => 'seccion_view',
            'create' => 'seccion_create',
            'edit' => 'seccion_edit',
            'delete' => 'seccion_delete',
            'show_route' => 'seccion_view_details'
        ];

        if ($request->input("created", false)){
            $data['created'] = $request->input('created');
        }

        if ($request->input("edited", false)){
            $data['edited'] = $request->input('edited');
        }

        if ($request->input("abort", false)){
            $data['abort'] = $request->input('abort');
        }

        if ($request->input("deleted", false)){
            $data['deleted'] = $request->input('deleted');
        }

        foreach ($secciones as $key => $itemseccion) {
            $data['filas'][] = [
                $key + 1 + $pagination*($paginaActual-1),
                $itemseccion->grado->nivelEducativo->nombre_nivel,  
                $itemseccion->grado->nombre_grado,        
                $itemseccion->nombreSeccion,              
                $itemseccion->id_grado                    
            ];
        }
         
        return view('gestiones.seccion.index', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grados = Grado::where('estado', '=', '1')->get();
        $niveles = NivelEducativo::where("estado","=",1)->get();
        $data = [
            'return' => route('seccion_view', ['abort' => true]),
            'grados' => $grados,
            'niveles' => $niveles
        ];

        return view('gestiones.seccion.create', compact('data'));
    }
    /**
     * Store a newly created resource in storage.
     */

    public function createNewEntry(Request $request){

        $request->validate([
            'grado'=>'required',
            'nivel_educativo' => 'required',
            'seccion'=> ['required','string','max:2',
            Rule::unique('secciones', 'nombreSeccion')
                ->where('id_grado', $request->grado)
            ],
        ],[
            'grado.required'=> 'El grado es requerido',
            'nivel_educativo.required' => 'Nivel Educativo es requerido',
            'seccion.required'=> 'Es necesario ingresar un nombre para la seccion',
            'seccion.max'=> 'El nombre de la seccion es muy largo, max = 2 caracteres',
            'seccion.unique' => 'Ya existe una sección con este nombre en el grado seleccionado.',
        ]);

        Seccion::create([
            'id_grado' => $request->input('grado'),
            'nombreSeccion' => $request->input('seccion')
        ]);

        return redirect(route('seccion_view', ['created' => true]));

    }


    public function edit(Request $request, $idGrado, $nombreSeccion)
    {
        if (!isset($idGrado) || !isset($nombreSeccion)) {
            return redirect(route('seccion_view'));
        }


        $seccion = Seccion::where('id_grado', $idGrado)
                        ->where('nombreSeccion', $nombreSeccion)
                        ->firstOrFail();

        $grados = Grado::where("estado", "=", "1")->get();

        $niveles = NivelEducativo::where("estado","=",1)->get();

        $data = [
            'return' => route('seccion_view', ['abort' => true]),
            'id' => ['nombreGrado' => $seccion->grado->nombre_grado, 'nombreSeccion' => $nombreSeccion],
            'grados' => $grados,
            'niveles' => $niveles,
            'default' => [
                'grado' => $seccion->id_grado,
                'seccion' => $seccion->nombreSeccion,
                'nivel_educativo' => $seccion->grado->id_nivel
            ]
        ];

        return view('gestiones.seccion.edit', compact('data'));
    }

    public function editEntry(Request $request, $idGrado, $nombreSeccion)
    {
        $seccion = Seccion::findByCompositeKeyOrFail($idGrado, $nombreSeccion);

        $request->validate([
            'grado' => 'required',
            'seccion' => [
                'required',
                'string',
                'max:2',
                function ($attribute, $value, $fail) use ($request, $idGrado, $nombreSeccion) {
                    $exists = Seccion::where('id_grado', $request->grado)
                        ->where('nombreSeccion', $value)
                        ->where(function ($query) use ($idGrado, $nombreSeccion) {
                            $query->where('id_grado', '!=', $idGrado)
                                  ->orWhere('nombreSeccion', '!=', $nombreSeccion);
                        })
                        ->exists();
                    
                    if ($exists) {
                        $fail('Ya existe una sección con este nombre en el grado seleccionado.');
                    }
                },
            ],
        ], [
            'grado.required' => 'El grado es requerido',
            'seccion.required' => 'Es necesario ingresar un nombre para la sección',
            'seccion.max' => 'El nombre de la sección es muy largo, máx = 2 caracteres',
        ]);

        try {
            // Verificar si hay cátedras asociadas
            $tieneCatedras = $seccion->catedras()->exists();

            $tieneMatriculas = $seccion->matriculas()->exists();
            
            if ($tieneCatedras) {
                return back()->withErrors([
                    'error' => 'No se puede modificar esta sección porque tiene cátedras asignadas.'
                ])->withInput();
            }

            if ($tieneMatriculas) {
                return back()->withErrors([
                    'error' => 'No se puede modificar esta sección porque tiene matriculas asignadas.'
                ])->withInput();
            }
            // Eliminar usando método personalizado y crear nuevo registro
            Seccion::where('id_grado', $idGrado)
                   ->where('nombreSeccion', $nombreSeccion)
                   ->delete();
            
            Seccion::create([
                'id_grado' => $request->input('grado'),
                'nombreSeccion' => $request->input('seccion'),
                'estado' => 1
            ]);

            return redirect(route('seccion_view', ['edited' => true]));

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar la sección: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function view_details(Request $request, $id_grado, $nombreSeccion)
    {
        $seccion = Seccion::where('id_grado', $id_grado)
            ->where('nombreSeccion', $nombreSeccion)
            ->firstOrFail();

        $anioEscolar = $request->input('año_escolar', Carbon::now()->year);

        $Pagination = $request->query('showing', 5);
        $Page = $request->query('page', 1);


        $matriculas = Matricula::with('alumno')
            ->where('id_grado', $id_grado)
            ->where('nombreSeccion', $nombreSeccion)
            ->where('año_escolar', $anioEscolar)
            ->where('estado', 1) // Matrículas activas
            ->whereHas('alumno', function($q){
                $q->where('estado',1); // Alumnos activos
            })
            ->orderBy('fecha_matricula', 'desc')
            ->paginate($Pagination)
            ->withQueryString(); 

        $añosDisponibles = Matricula::where('id_grado', $id_grado)
            ->where('nombreSeccion', $nombreSeccion)
            ->distinct()
            ->pluck('año_escolar');

        if ($añosDisponibles->isEmpty()) {
            $añosDisponibles = collect([date('Y')]);
        }

        return view('gestiones.seccion.view_details', compact(
            'seccion',
            'matriculas',
            'anioEscolar',
            'añosDisponibles',
            'Pagination',
            'Page'
        ));
    }

    public function delete(Request $request)
    {
        
        $id = $request->input('id');
        $ids = explode(",",$id);


        $seccion = Seccion::where('id_grado', $ids[0])
                   ->where('nombreSeccion', $ids[1])
                   ->update(['estado' => 0]);


        return redirect(route('seccion_view', ['deleted' => true]));
    }

    public function export(
        Request $request,
        IExportRequestFactory $requestFactory,
        IExporterService $exporterService
    ) {
        try {

            $sqlColumns = ['NivelEducativo.nombre_nivel', 'Grado.nombre_grado', 'nombreSeccion'];

            $params = RequestHelper::extractSearchParams($request);
            $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

            $query = $query->sortBy(function ($seccion) {
                return ($seccion->grado->niveleducativo->nombre_nivel ?? '') . 
                    ($seccion->grado->nombre_grado ?? '') .
                    ($seccion->nombreSeccion ?? '');
            });

            $data = $query->map(function ($seccion) {
                return [
                    $seccion->grado->niveleducativo->nombre_nivel ?? 'N/A',
                    $seccion->grado->nombre_grado ?? 'N/A',
                    $seccion->nombreSeccion ?? 'N/A',
                ];
            });

            $title = 'Secciones';
            $headers = ['Nivel Educativo', 'Grado', 'Sección'];

            $exportRequest = $requestFactory->create(
                $title,
                $headers,
                $data->toArray(),
                [
                    'filename' => 'secciones_' . date('d_m_Y'),
                    'subtitle' => 'Listado de secciones del sistema',
                    'footer' => 'Sistema de Gestión Académica SIGMA - Generado automáticamente'
                ]
            );

            return $exporterService->exportAsResponse($request, $exportRequest);

        } catch (\Exception $e) {
            \Log::error('Error en exportación de secciones: ' . $e->getMessage());
            return back()->with('error', 'Error durante la exportación');
        }
    }





}
