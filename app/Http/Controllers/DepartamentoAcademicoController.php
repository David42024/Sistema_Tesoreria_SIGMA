<?php

namespace App\Http\Controllers;

use App\Models\DepartamentoAcademico;
use App\Models\Personal;
use Illuminate\Http\Request;

use App\Helpers\CRUDTablePage;
use App\Helpers\ExcelExportHelper;
use App\Helpers\FilteredSearchQuery;
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

class DepartamentoAcademicoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_departamento',
            'Nombre' => 'nombre',
        ];

        $query = DepartamentoAcademico::where('estado', '=', '1');

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_departamento', 'nombre'];
        $resource = 'personal';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Departamentos Académicos")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Departamentos Académicos");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "departamento_academico_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "departamento_academico_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "departamento_academico_view"]);
            $params->showing = 100;
        }

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
            ->action('Estás eliminando el Departamento Académico')
            ->columns(['ID', 'Nombre'])
            ->rows(['', ''])
            ->lastWarningMessage('Borrar esto afectará a todo el personal vinculado a este Departamento.')
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

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID", "Nombre"
        ];
        $filterConfig->filterOptions = [];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Nombre", "N° Docentes"];
        $table->rows = [];

        foreach ($query as $departamento) {
            // Contar docentes activos del departamento
            $cantidadDocentes = Personal::where('id_departamento', $departamento->id_departamento)
                ->where('estado', 1)
                ->count();

            array_push($table->rows, [
                $departamento->id_departamento,
                $departamento->nombre,
                $cantidadDocentes,
            ]);
        }

        $table->actions = [
            new TableAction('view', 'departamento_academico_docentes', $resource), // Ver docentes
            new TableAction('edit', 'departamento_academico_edit', $resource),
            new TableAction('delete', '', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), [
            'search' => $params->search,
            'showing' => $params->showing,
            'applied_filters' => $params->applied_filters
        ]);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function viewAll(Request $request)
    {
        return static::index($request, true);
    }

    /* ==================== GESTIÓN DE DOCENTES ==================== */

    public function docentes(Request $request, $id)
    {
        $departamento = DepartamentoAcademico::findOrFail($id);

        $params = RequestHelper::extractSearchParams($request);
        $search = $params->search;
        $showing = $params->showing ?? 10;

        $page = CRUDTablePage::new()
            ->title("Docentes - " . $departamento->nombre)
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Docentes del Departamento: " . $departamento->nombre);

        /* Botones */
        $volverButton = new TableButtonComponent("tablesv2.buttons.volver", ["redirect" => "departamento_academico_view"]);
        $agregarDocenteButton = new TableButtonComponent("tablesv2.buttons.agregar", ["redirect" => "departamento_academico_agregar_docente", "params" => ['id' => $id]]);

        $content->addButton($volverButton);
        $content->addButton($agregarDocenteButton);

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        $paginatorRowsSelector->valueSelected = $showing;
        $content->paginatorRowsSelector($paginatorRowsSelector);

        /* Searchbox */
        $searchBox = new SearchBoxComponent();
        $searchBox->placeholder = "Buscar docente...";
        $searchBox->value = $search;
        $content->searchBox($searchBox);

        /* Modal para quitar docente */
        $cautionModal = CautionModalComponent::new()
            ->cautionMessage('¿Estás seguro?')
            ->action('Estás quitando al docente del departamento')
            ->columns(['DNI', 'Nombre', 'Cargo'])
            ->rows(['', '', ''])
            ->lastWarningMessage('El docente será removido de este departamento.')
            ->confirmButton('Sí, quitar')
            ->cancelButton('Cancelar')
            ->isForm(true)
            ->dataInputName('id_personal')
            ->build();

        $page->modals([$cautionModal]);

        /* Query de docentes */
        $queryDocentes = Personal::where('id_departamento', $id)
            ->where('estado', 1);

        if (!empty($search)) {
            $queryDocentes->where(function ($q) use ($search) {
                $q->where('dni', 'LIKE', "%{$search}%")
                    ->orWhere('primer_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%")
                    ->orWhere('codigo_personal', 'LIKE', "%{$search}%");
            });
        }

        $docentes = $queryDocentes->paginate($showing);

        if ($params->page > $docentes->lastPage() && $docentes->lastPage() > 0) {
            $params->page = 1;
            $docentes = $queryDocentes->paginate($showing);
        }

        $table = new TableComponent();
        $table->columns = ["ID", "DNI", "Nombre Completo", "Cargo", "Teléfono"];
        $table->rows = [];

        foreach ($docentes as $docente) {
            $nombreCompleto = trim(
                ($docente->primer_nombre ?? '') . ' ' .
                ($docente->otros_nombres ?? '') . ' ' .
                ($docente->apellido_paterno ?? '') . ' ' .
                ($docente->apellido_materno ?? '')
            );

            array_push($table->rows, [
                $docente->id_personal,
                $docente->dni,
                $nombreCompleto,
                $docente->cargo ?? 'Sin cargo',
                $docente->telefono ?? 'Sin teléfono',
            ]);
        }

        $table->actions = [
            new TableAction('remove', 'departamento_academico_quitar_docente', 'personal', ['id_departamento' => $id]),
        ];

        $paginator = new TablePaginator($params->page, $docentes->lastPage(), [
            'search' => $search,
            'showing' => $showing,
        ]);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function agregarDocente(Request $request, $id)
    {
        $departamento = DepartamentoAcademico::findOrFail($id);

        // Docentes sin departamento asignado o disponibles
        $docentesDisponibles = Personal::where('estado', 1)
            ->where(function ($q) {
                $q->whereNull('id_departamento')
                    ->orWhere('id_departamento', 0);
            })
            ->get();

        $data = [
            'return' => route('departamento_academico_docentes', ['id' => $id]),
            'departamento' => $departamento,
            'docentes_disponibles' => $docentesDisponibles,
        ];

        return view('gestiones.departamento_academico.agregar_docente', compact('data'));
    }

    public function guardarDocente(Request $request, $id)
    {
        $request->validate([
            'id_personal' => 'required|exists:personal,id_personal',
        ], [
            'id_personal.required' => 'Seleccione un docente.',
            'id_personal.exists' => 'El docente seleccionado no existe.',
        ]);

        $departamento = DepartamentoAcademico::findOrFail($id);
        $docente = Personal::findOrFail($request->input('id_personal'));

        // Verificar que el docente no esté ya en otro departamento
        if ($docente->id_departamento && $docente->id_departamento != $id) {
            return back()->withErrors(['id_personal' => 'Este docente ya pertenece a otro departamento.'])->withInput();
        }

        $docente->update([
            'id_departamento' => $id
        ]);

        return redirect()->route('departamento_academico_docentes', ['id' => $id])
            ->with('success', 'Docente agregado correctamente al departamento.');
    }

   public function quitarDocente(Request $request)
    {
        $idPersonal = $request->input('id_personal');
        
        $docente = Personal::where('id_personal', $idPersonal)
            ->whereNotNull('id_departamento')
            ->firstOrFail();
        
        $idDepartamento = $docente->id_departamento;
        
        // Obtener el nombre del departamento antes de quitar
        $departamento = DepartamentoAcademico::find($idDepartamento);
        $nombreDepartamento = $departamento->nombre ?? 'desconocido';

        $docente->update([
            'id_departamento' => null
        ]);

        return redirect()->route('departamento_academico_docentes', ['id' => $idDepartamento])
            ->with('removed', $nombreDepartamento);
    }

    /* ==================== CRUD BÁSICO ==================== */

    public function create(Request $request)
    {
        $data = [
            'return' => route('departamento_academico_view', ['abort' => true]),
        ];

        return view('gestiones.departamento_academico.create', compact('data'));
    }

    public function createNewEntry(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:90|unique:departamentos_academicos,nombre',
        ], [
            'nombre.required' => 'Ingrese un nombre válido.',
            'nombre.max' => 'El nombre no puede superar los 90 caracteres.',
            'nombre.unique' => 'Ya existe un departamento con ese nombre.',
        ]);

        DepartamentoAcademico::create([
            'nombre' => $request->input('nombre'),
        ]);

        return redirect(route('departamento_academico_view', ['created' => true]));
    }

    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('departamento_academico_view'));
        }

        $requested = DepartamentoAcademico::findOrFail($id);

        $data = [
            'return' => route('departamento_academico_view', ['abort' => true]),
            'id' => $id,
            'default' => [
                'nombre' => $requested->nombre,
            ]
        ];

        return view('gestiones.departamento_academico.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('departamento_academico_view'));
        }

        $request->validate([
            'nombre' => 'required|max:90|unique:departamentos_academicos,nombre,' . $id . ',id_departamento',
        ], [
            'nombre.required' => 'Ingrese un nombre válido.',
            'nombre.max' => 'El nombre no puede superar los 90 caracteres.',
            'nombre.unique' => 'Ya existe un departamento con ese nombre.',
        ]);

        $requested = DepartamentoAcademico::find($id);

        if (isset($requested)) {
            $requested->update([
                'nombre' => $request->input('nombre')
            ]);
        }

        return redirect(route('departamento_academico_view', ['edited' => true]));
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');

        $requested = DepartamentoAcademico::findOrFail($id);
        $requested->update(['estado' => '0']);

        // Desactivar personal vinculado
        Personal::where('id_departamento', '=', $id)->update(['estado' => 0]);

        return redirect(route('departamento_academico_view', ['deleted' => true]));
    }

    /* ==================== EXPORTACIÓN ==================== */

    public function export(Request $request)
    {
        try {
            $format = $request->input('export', 'excel');

            $sqlColumns = ['id_departamento', 'nombre'];

            $params = RequestHelper::extractSearchParams($request);

            $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

            if ($format === 'pdf') {
                return $this->exportPdf($query);
            }

            return $this->exportExcel($query);

        } catch (\Exception $e) {
            \Log::error('Error en exportación de departamentos académicos: ' . $e->getMessage());
            return back()->with('error', 'Error durante la exportación');
        }
    }

    private function exportExcel($departamentos)
    {
        $headers = ['ID', 'Nombre', 'N° Docentes'];
        $fileName = 'departamentos_academicos_' . date('Y-m-d_H-i-s') . '.xlsx';

        return ExcelExportHelper::exportExcel(
            $fileName,
            $headers,
            $departamentos,
            function ($sheet, $row, $departamento) {
                $cantidadDocentes = Personal::where('id_departamento', $departamento->id_departamento)
                    ->where('estado', 1)
                    ->count();

                $sheet->setCellValue('A' . $row, $departamento->id_departamento);
                $sheet->setCellValue('B' . $row, $departamento->nombre);
                $sheet->setCellValue('C' . $row, $cantidadDocentes);
            },
            'Departamentos Académicos',
            'Exportación de Departamentos Académicos',
            'Listado de departamentos académicos del sistema'
        );
    }

    private function exportPdf($departamentos)
    {
        if ($departamentos->isEmpty()) {
            return response()->json(['error' => 'No hay datos para exportar'], 400);
        }

        $fileName = 'departamentos_academicos_' . date('Y-m-d_H-i-s') . '.pdf';

        $rows = $departamentos->map(function ($departamento) {
            $cantidadDocentes = Personal::where('id_departamento', $departamento->id_departamento)
                ->where('estado', 1)
                ->count();

            return [
                $departamento->id_departamento,
                $departamento->nombre,
                $cantidadDocentes,
            ];
        })->toArray();

        $html = PDFExportHelper::generateTableHtml([
            'title' => 'Departamentos Académicos',
            'subtitle' => 'Listado de Departamentos Académicos',
            'headers' => ['ID', 'Nombre', 'N° Docentes'],
            'rows' => $rows,
            'footer' => 'Sistema de Gestión Académica SIGMA - Generado automáticamente',
        ]);

        return PDFExportHelper::exportPdf($fileName, $html);
    }
}