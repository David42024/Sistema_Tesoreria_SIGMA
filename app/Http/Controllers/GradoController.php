<?php

namespace App\Http\Controllers;

use App\Helpers\FilteredSearchQuery;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use DB;
use Illuminate\Http\Request;
use App\Models\Grado;

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

class GradoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_grado',
            'Grado' => 'nombre_grado',
            'Nivel Educativo' => 'NivelEducativo.descripcion',
        ];

        $query = Grado::where('estado', '=', true);

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null)
            return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ["id_grado", "nombre_grado", "NivelEducativo.descripcion"];
        $resource = 'academica';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Grados")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Grados");

        /* Definición de botones */
        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "grado_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "grado_viewAll"]);
        } else {
            $params->showing = 100;
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "grado_view"]);
        }

        $content->addButton($filterButton);
        $content->addButton($vermasButton);
        $content->addButton($descargaButton);
        $content->addButton($createNewEntryButton);

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        if ($long)
            $paginatorRowsSelector = new PaginatorRowsSelectorComponent([100]);
        $paginatorRowsSelector->valueSelected = $params->showing;
        $content->paginatorRowsSelector($paginatorRowsSelector);

        /* Searchbox */
        $searchBox = new SearchBoxComponent();
        $searchBox->placeholder = "Buscar...";
        $searchBox->value = $params->search;
        $content->searchBox($searchBox);

        /* Modales usados */
        $cautionModal = CautionModalComponent::new()
            ->cautionMessage('¿Estás seguro?')
            ->action('Estás eliminando el Grado')
            ->columns(["Grado", "Nivel Educativo"])
            ->rows(['', ''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Grado')
            ->confirmButton('Sí, bórralo')
            ->cancelButton('Cancelar')
            ->isForm(true)
            ->dataInputName('id')
            ->build();

        $page->modals([$cautionModal]);

        /* Lógica del controller */

        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);

        if ($params->page > $query->lastPage()) {
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        $nivelesExistentes = NivelEducativo::select("descripcion")
            ->distinct()
            ->where("estado", "=", 1)
            ->pluck("descripcion");

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID",
            "Grado",
            "Nivel Educativo"
        ];
        $filterConfig->filterOptions = [
            "Nivel Educativo" => $nivelesExistentes
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Grado", "Nivel Educativo"];
        $table->rows = [];

        foreach ($query as $grado) {
            array_push(
                $table->rows,
                [
                    $grado->id_grado,
                    $grado->nombre_grado,
                    $grado->niveleducativo->descripcion
                ]
            );
        }
        $table->actions = [
            new TableAction('edit', 'grado_edit', $resource),
            new TableAction('delete', '', $resource),
            new TableAction('grado_view_details', 'grado_view_details', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), []);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function viewAll(Request $request)
    {
        return static::index($request, true);
    }

    public function fallback(Request $request)
    {
        $sqlColumns = ["id_grado", "id_nivel", "nombre_grado"];
        $tipoDeRecurso = "academica";

        $pagination = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

        if (!is_numeric($paginaActual) || $paginaActual <= 0)
            $paginaActual = 1;
        if (!is_numeric($pagination) || $pagination <= 0)
            $pagination = 10;

        $grados = GradoController::doSearch($sqlColumns, $search, $pagination);

        if ($paginaActual > $grados->lastPage()) {
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $grados = GradoController::doSearch($sqlColumns, $search, $pagination);
        }

        $data = [
            'titulo' => 'Grados',
            'columnas' => [
                'ID',
                'Grado',
                'Nivel Educativo'
            ],
            'filas' => [],
            'showing' => $pagination,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $grados->lastPage(),
            'resource' => $tipoDeRecurso,
            'view' => 'grado_view',
            'create' => 'grado_create',
            'edit' => 'grado_edit',
            'delete' => 'grado_delete',
            'show_route' => 'grado_view_details'
        ];

        if ($request->input("created", false)) {
            $data['created'] = $request->input('created');
        }

        if ($request->input("edited", false)) {
            $data['edited'] = $request->input('edited');
        }

        if ($request->input("abort", false)) {
            $data['abort'] = $request->input('abort');
        }

        if ($request->input("deleted", false)) {
            $data['deleted'] = $request->input('deleted');
        }

        foreach ($grados as $itemgrado) {
            array_push(
                $data['filas'],
                [
                    $itemgrado->id_grado,
                    $itemgrado->nombre_grado,
                    $itemgrado->nivelEducativo->descripcion
                ]
            );
        }

        return view('gestiones.grado.index', compact('data'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $niveles = NivelEducativo::where('estado', '=', '1')->get();
        $data = [
            'return' => route('grado_view', ['abort' => true]),
            'niveles' => $niveles
        ];

        return view('gestiones.grado.create', compact('data'));
    }


    public function createNewEntry(Request $request)
    {

        $request->validate([
            'nombre_del_grado' => 'required|string|max:50|unique:grados,nombre_grado',
            'nivel_educativo' => 'required|integer|exists:niveles_educativos,id_nivel',
        ], [
            'nombre_del_grado.required' => 'El nombre del grado es obligatorio.',
            'nombre_del_grado.unique' => 'Ya existe un grado con ese nombre.',
            'nombre_del_grado.max' => 'Maximo num de caracteres: 50',
            'nivel_educativo.required' => 'Debes seleccionar un nivel educativo.',
            'nivel_educativo.exists' => 'El nivel educativo seleccionado no es válido.',
        ]);

        Grado::create([
            'id_nivel' => $request->input('nivel_educativo'),
            'nombre_grado' => $request->input('nombre_del_grado')
        ]);

        return redirect(route('grado_view', ['created' => true]));

    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        //
    }


    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('grado_view'));
        }

        $grado = Grado::findOrFail($id);
        $niveles = NivelEducativo::where("estado", "=", "1")->get();

        $data = [
            'return' => route('grado_view', ['abort' => true]),
            'id' => $id,
            'niveles' => $niveles,
            'default' => [
                'id_nivel' => $grado->id_nivel,
                'nombre_del_grado' => $grado->nombre_grado
            ]
        ];

        return view('gestiones.grado.edit', compact('data'));
    }



    public function editEntry(Request $request, $id)
    {


        if (!isset($id)) {
            return redirect(route('grado_view'));
        }
        $request->validate([
            'nombre_del_grado' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = Grado::where('id_nivel', $request->nivel_educativo)
                        ->where('nombre_grado', $value)
                        ->where(
                            function ($query) use ($id) {
                                $query->where('id_grado', '!=', $id);
                            }
                        )->exists();
                    if ($exists) {
                        $fail('Ya existe un grado con este nombre en el nivel seleccionado.');
                    }
                }
            ],
            'nivel_educativo' => 'required|integer|exists:niveles_educativos,id_nivel',
        ], [
            'nombre_del_grado.required' => 'El nombre del grado es obligatorio.',
            'nombre_del_grado.max' => 'Máximo número de caracteres: 50.',
            'nivel_educativo.required' => 'Debes seleccionar un nivel educativo.',
            'nivel_educativo.exists' => 'El nivel educativo seleccionado no es válido.',
        ]);

        $grado = Grado::findOrFail($id);

        $grado->nombre_grado = $request->input('nombre_del_grado');
        $grado->id_nivel = $request->input('nivel_educativo');
        $grado->save();

        return redirect()->route('grado_view', ['edited' => true]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $grado = Grado::findOrFail($id);
        $grado->update(['estado' => '0']);


        return redirect(route('grado_view', ['deleted' => true]));
    }

    public function view_details(Request $request, $id)
    {
        $anioActual = date('Y');
        $anio = $request->query('anio', $anioActual);

        $cursoPagination = $request->query('curso_showing', 5);
        $cursoPage = $request->query('curso_page', 1);

        $seccionPagination = $request->query('seccion_showing', 5);
        $seccionPage = $request->query('seccion_page', 1);

        $grado = Grado::with('nivelEducativo')->findOrFail($id);

        $cursosQuery = $grado->cursos()
            ->where('cursos.estado', 1)
            ->wherePivot('año_escolar', $anio)
            ->paginate($cursoPagination, ['*'], 'curso_page', $cursoPage);

        $seccionesQuery = $grado->secciones()
            ->where('estado', 1)
            ->paginate($seccionPagination, ['*'], 'seccion_page', $seccionPage);

        $aniosDisponibles = DB::table('cursos_grados')
            ->select('año_escolar')
            ->distinct()
            ->orderBy('id_periodo_academico', 'desc')
            ->pluck('año_escolar');

        return view('gestiones.grado.view_details', compact(
            'grado',
            'anio',
            'aniosDisponibles',
            'cursosQuery',
            'seccionesQuery',
            'cursoPagination',
            'seccionPagination'
        ));
    }

    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = ['id_alumno', 'codigo_educando', 'dni', 'apellido_paterno', 'apellido_materno', 'primer_nombre', 'otros_nombres', 'sexo'];

        $params = RequestHelper::extractSearchParams($request);

        // Para ambos formatos, obtener todos los registros (sin paginación)
        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

        $data = $query->map(function ($grado) {
            $nivelEducativo = $grado->nivelEducativo;
            return [
                $nivelEducativo->nombre_nivel,
                $grado->nombre_grado,
            ];
        });

        $title = 'Listado de Grados';
        $headers = ["Nivel Educativo", "Grado"];
        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'grados_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);
    }
}
