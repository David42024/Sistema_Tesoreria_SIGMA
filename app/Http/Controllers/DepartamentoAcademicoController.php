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
            ->columns(['Nombre'])
            ->rows([''])
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
        $table->columns = ["ID", "Nombre"];
        $table->rows = [];

        foreach ($query as $departamento) {
            array_push($table->rows, [
                $departamento->id_departamento,
                $departamento->nombre,
            ]);
        }

        $table->actions = [
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
        $docentes = Personal::where('id_departamento', '=', $id)->get();

        foreach ($docentes as $doc) {
            $doc->estado = 0;
            $doc->save();
        }

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

            \Log::info('Exportando departamentos académicos', [
                'format' => $format,
                'total_records' => $query->count(),
            ]);

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
        $headers = ['ID', 'Nombre'];
        $fileName = 'departamentos_academicos_' . date('Y-m-d_H-i-s') . '.xlsx';

        return ExcelExportHelper::exportExcel(
            $fileName,
            $headers,
            $departamentos,
            function ($sheet, $row, $departamento) {
                $sheet->setCellValue('A' . $row, $departamento->id_departamento);
                $sheet->setCellValue('B' . $row, $departamento->nombre);
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
            return [
                $departamento->id_departamento,
                $departamento->nombre,
            ];
        })->toArray();

        $html = PDFExportHelper::generateTableHtml([
            'title' => 'Departamentos Académicos',
            'subtitle' => 'Listado de Departamentos Académicos',
            'headers' => ['ID', 'Nombre'],
            'rows' => $rows,
            'footer' => 'Sistema de Gestión Académica SIGMA - Generado automáticamente',
        ]);

        return PDFExportHelper::exportPdf($fileName, $html);
    }
}