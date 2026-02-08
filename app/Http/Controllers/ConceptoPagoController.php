<?php

namespace App\Http\Controllers;

use App\Models\ConceptoPago;
use Illuminate\Http\Request;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
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
use Illuminate\Validation\Rule;

class ConceptoPagoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_concepto',
            'Descripción' => 'descripcion',
            'Escala' => 'escala',
            'Monto' => 'monto'
        ];

        $query = ConceptoPago::where('estado', '=', '1');

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_concepto', 'descripcion', 'escala', 'monto'];
        $resource = 'financiera';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Conceptos de Pago")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Conceptos de Pago");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "concepto_de_pago_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "concepto_de_pago_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "concepto_de_pago_view"]);
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
            ->action('Estás eliminando el Concepto de Pago')
            ->columns(['Descripción', 'Escala', 'Monto'])
            ->rows(['', '', ''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Concepto de Pago.')
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
            "ID", "Descripción", "Escala", "Monto"
        ];
        $filterConfig->filterOptions = [
            "Escala" => ["A", "B", "C", "D", "E"]
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Descripción", "Escala", "Monto (S/)"];
        $table->rows = [];

        foreach ($query as $concepto) {
            array_push($table->rows, [
                $concepto->id_concepto,
                $concepto->descripcion,
                $concepto->escala,
                number_format($concepto->monto, 2),
            ]);
        }

        $table->actions = [
            new TableAction('edit', 'concepto_de_pago_edit', $resource),
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

    public function create()
    {
        $escalas = [
            ['id' => 'A', 'descripcion' => 'A'],
            ['id' => 'B', 'descripcion' => 'B'],
            ['id' => 'C', 'descripcion' => 'C'],
            ['id' => 'D', 'descripcion' => 'D'],
            ['id' => 'E', 'descripcion' => 'E'],
        ];

        $data = [
            'return' => route('concepto_de_pago_view', ['abort' => true]),
            'escalas' => $escalas,
        ];

        return view('gestiones.conceptoPago.create', compact('data'));
    }

    public function createNewEntry(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|max:100',
            'escala' => 'required|in:A,B,C,D,E',
            'monto' => 'required|numeric|min:0'
        ], [
            'descripcion.required' => 'Ingrese una descripción válida.',
            'descripcion.max' => 'La descripción no puede superar los 100 caracteres.',
            'escala.required' => 'Seleccione una escala válida.',
            'escala.in' => 'La escala debe ser A, B, C, D o E.',
            'monto.required' => 'Ingrese un monto válido.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'monto.min' => 'El monto no puede ser negativo.',
        ]);

        ConceptoPago::create([
            'descripcion' => $request->input('descripcion'),
            'escala' => $request->input('escala'),
            'monto' => $request->input('monto'),
            'estado' => 1
        ]);

        return redirect()->route('concepto_de_pago_view')->with('success', 'Concepto de pago creado correctamente.');
    }

    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('concepto_de_pago_view'));
        }

        $concepto = ConceptoPago::findOrFail($id);

        $escalas = [
            ['id' => 'A', 'descripcion' => 'A'],
            ['id' => 'B', 'descripcion' => 'B'],
            ['id' => 'C', 'descripcion' => 'C'],
            ['id' => 'D', 'descripcion' => 'D'],
            ['id' => 'E', 'descripcion' => 'E'],
        ];

        $data = [
            'return' => route('concepto_de_pago_view', ['abort' => true]),
            'id' => $id,
            'escalas' => $escalas,
            'default' => [
                'descripcion' => $concepto->descripcion,
                'escala' => $concepto->escala,
                'monto' => $concepto->monto
            ]
        ];

        return view('gestiones.conceptoPago.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('concepto_de_pago_view'));
        }

        $request->validate([
            'descripcion' => 'required|max:100',
            'escala' => 'required|in:A,B,C,D,E',
            'monto' => 'required|numeric|min:0'
        ], [
            'descripcion.required' => 'Ingrese una descripción válida.',
            'descripcion.max' => 'La descripción no puede superar los 100 caracteres.',
            'escala.required' => 'Seleccione una escala válida.',
            'escala.in' => 'La escala debe ser A, B, C, D o E.',
            'monto.required' => 'Ingrese un monto válido.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'monto.min' => 'El monto no puede ser negativo.',
        ]);

        $concepto = ConceptoPago::find($id);

        if (isset($concepto)) {
            $concepto->update([
                'descripcion' => $request->input('descripcion'),
                'escala' => $request->input('escala'),
                'monto' => $request->input('monto')
            ]);
        }

        return redirect()->route('concepto_de_pago_view')->with('success', 'Concepto de pago actualizado correctamente.');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $concepto = ConceptoPago::findOrFail($id);
        $concepto->update(['estado' => '0']);

        return redirect()->route('concepto_de_pago_view')->with('success', 'Concepto de pago desactivado correctamente.');
    }

    /* ==================== EXPORTACIÓN ==================== */

    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = ['id_concepto', 'descripcion', 'escala', 'monto'];

        $params = RequestHelper::extractSearchParams($request);
        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

        $data = $query->map(function ($concepto) {
            return [
                $concepto->descripcion,
                $concepto->escala,
                'S/ ' . number_format($concepto->monto, 2),
            ];
        });

        $title = 'Listado de Conceptos de Pago';
        $headers = ['Descripción', 'Escala', 'Monto'];

        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'conceptos_pago_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);
    }
}
