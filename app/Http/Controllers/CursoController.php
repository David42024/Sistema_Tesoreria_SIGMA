<?php

namespace App\Http\Controllers;

use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Helpers\FilteredSearchQuery;
use App\Models\Curso;
use App\Models\NivelEducativo;
use Illuminate\Http\Request;
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

class CursoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = []){
        $columnMap = [
            'ID' => 'id_curso',
            'Código del curso' => 'codigo_curso',
            'Pertenece al nivel'=> 'NivelEducativo.nombre_nivel',
            'Nombre del curso' => 'nombre_curso',
        ];

        $query = Curso::where('estado', '=', true);

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null) return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function index(Request $request, $long = false){
        $sqlColumns = ['id_curso', 'codigo_curso', 'NivelEducativo.nombre_nivel', 'nombre_curso'];
        $resource = 'academica';

        $params = RequestHelper::extractSearchParams($request);
        
        $page = CRUDTablePage::new()
            ->title("Cursos")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());
        
        $content = CRUDTableComponent::new()
            ->title("Cursos");

        /* Definición de botones */
        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "curso_create"]);

        if (!$long){
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "curso_viewAll"]);
        } else {
            $params->showing = 100;
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "curso_view"]);
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

        /* Modales usados */
        $cautionModal = CautionModalComponent::new()
            ->cautionMessage('¿Estás seguro?')
            ->action('Estás eliminando el Curso')
            ->columns(["Código del curso", "Pertenece al nivel", "Nombre del curso"])
            ->rows(['', '', ''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Curso')
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

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID", "Código del curso", "Pertenece al nivel", "Nombre del curso"
        ];
        $filterConfig->filterOptions = [
            "Pertenece al nivel" => $nivelesExistentes
        ];
        $content->filterConfig = $filterConfig;
        
        $table = new TableComponent();
        $table->columns = ["ID", "Código del curso", "Pertenece al nivel", "Nombre del curso"];
        $table->rows = [];

        foreach ($query as $curso){
            array_push($table->rows,
            [
                $curso->id_curso,
                $curso->codigo_curso,
                $curso->niveleducativo->nombre_nivel,
                $curso->nombre_curso,
            ]); 
        }
        $table->actions = [
            new TableAction('edit', 'curso_edit', $resource),
            new TableAction('delete', '', $resource),
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
    public function fallback(Request $request){
        $sqlColumns = ['id_curso', 'codigo_curso', 'nombre_curso'];
        $resource = 'academica';

        $maxEntriesShow = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

        if (!is_numeric($paginaActual) || $paginaActual <= 0) $paginaActual = 1;
        if (!is_numeric($maxEntriesShow) || $maxEntriesShow <= 0) $maxEntriesShow = 10;
        
        $query = CursoController::doSearch($sqlColumns, $search, $maxEntriesShow);

        if ($paginaActual > $query->lastPage()){
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $query = CursoController::doSearch($sqlColumns, $search, $maxEntriesShow);
        }
        
        $data = [
            'titulo' => 'Cursos',
            'columnas' => [
                'ID',
                'Código del curso',
                'Pertenece al nivel',
                'Nombre del curso'
            ],
            'filas' => [],
            'showing' => $maxEntriesShow,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $query->lastPage(),
            'resource' => $resource,
            'view' => 'curso_view',
            'create' => 'curso_create',
            'edit' => 'curso_edit',
            'delete' => 'curso_delete',
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

        

        foreach ($query as $curso){
            $nivel = NivelEducativo::findOrFail($curso->id_nivel);

            array_push($data['filas'],
            [
                $curso->id_curso,
                $curso->codigo_curso,
                $nivel->nombre_nivel,
                $curso->nombre_curso,
            ]); 
        }
        return view('gestiones.curso.index', compact('data'));
    }

    public function create(Request $request){
        $niveles = NivelEducativo::where('estado', '=', '1')->get();

        $data = [
            'niveles' => $niveles,
            'return' => route('curso_view', ['abort' => true]),
        ];

        return view('gestiones.curso.create', compact('data'));
    }

    public function createNewEntry(Request $request){

        $nivel = $request->input('nivel_educativo');
        $codigoCurso = $request->input('código_del_curso');
        $nombreCurso = $request->input('nombre_del_curso');
        
        $request->validate([
            'nivel_educativo' => 'required|max:10',
            'código_del_curso' => 'required|max:10',
            'nombre_del_curso' => 'required|max:100'
        ],[
            'nivel_educativo.required' => 'Seleccione un nivel educativo válido.',
            'código_del_curso.required' => 'Ingrese un código del curso válido.',
            'nombre_del_curso.required' => 'Ingrese un nombre del curso válido.',
            'nivel_educativo.max' => 'El ID del nivel educativo no puede superar los 10 dígitos.',
            'código_del_curso.max' => 'El código del curso no puede superar los 10 dígitos.',
            'nombre_del_curso.max' => 'El nombre del curso no puede superar los 100 caracteres.'
        ]);

        Curso::create(
            [
            'id_nivel' => $nivel,
            'codigo_curso' => $codigoCurso,
            'nombre_curso' => $nombreCurso
        ]);

        return redirect(route('curso_view', ['created' => true]));
    }

    public function edit(Request $request, $id){
        if (!isset($id)){
            return redirect(route('curso_view'));
        }

        $requested = Curso::findOrFail($id);

        $niveles = NivelEducativo::where('estado', '=', '1')->get();


        $data = [
            'return' => route('curso_view', ['abort' => true]),
            'id' => $id,
            'niveles' => $niveles,
            'default' => [
                'nivel_educativo' => $requested->id_nivel,
                'código_del_curso' => $requested->codigo_curso,
                'nombre_del_curso' => $requested->nombre_curso
            ]
        ];
        return view('gestiones.curso.edit', compact('data'));
    }

    public function editEntry(Request $request, $id){
        if (!isset($id)){
            return redirect(route('nivel_educativo_view'));
        }

        $request->validate([
            'nivel_educativo' => 'required|max:10',
            'código_del_curso' => 'required|max:10',
            'nombre_del_curso' => 'required|max:100'
        ],[
            'nivel_educativo.required' => 'Seleccione un nivel educativo válido.',
            'código_del_curso.required' => 'Ingrese un código del curso válido.',
            'nombre_del_curso.required' => 'Ingrese un nombre del curso válido.',
            'nivel_educativo.max' => 'El ID del nivel educativo no puede superar los 10 dígitos.',
            'código_del_curso.max' => 'El código del curso no puede superar los 10 dígitos.',
            'nombre_del_curso.max' => 'El nombre del curso no puede superar los 100 caracteres.'
        ]);

        $requested = Curso::find($id);

        if (isset($requested)){
            $newNivelEducativo = $request->input('nivel_educativo');
            $newCodigoCurso = $request->input('código_del_curso');
            $newNombreCurso = $request->input('nombre_del_curso');

            $requested->update(['id_nivel' => $newNivelEducativo, 'codigo_curso' => $newCodigoCurso, 'nombre_curso' => $newNombreCurso]);
        }

        return redirect(route('curso_view', ['edited' => true]));
    }

    public function delete(Request $request){
        $id = $request->input('id');

        $requested = Curso::find($id);
        $requested->update(['estado' => '0']);

        return redirect(route('curso_view', ['deleted' => true]));
    }


    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = ['id_curso', 'codigo_curso', 'nivel.nombre_nivel', 'nombre_curso'];

        $params = RequestHelper::extractSearchParams($request);
        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

        $data = $query->map(function ($curso) {
            return [
                $curso->id_curso,
                $curso->codigo_curso,
                $curso->nivel->nombre_nivel ?? 'N/A',
                $curso->nombre_curso,
            ];
        });

        $title = 'Listado de Cursos';
        $headers = ['ID', 'Código', 'Nivel Educativo', 'Nombre'];

        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'cursos_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);
    }

}

