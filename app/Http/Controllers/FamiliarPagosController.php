<?php

namespace App\Http\Controllers;

use App\Models\ConceptoPago;
use App\Models\Deuda;
use App\Models\Pago;
use Illuminate\Http\Request;

use App\Helpers\Home\Familiar\FamiliarHeaderComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
use App\Http\Controllers\Home\Utils;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Seccion;
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
use App\Http\Controllers\Controller;
use App\Models\NivelEducativo;
use App\Models\DistribucionPagoDeuda;
use App\Models\DetallePago;

class FamiliarPagosController extends Controller
{
    private static function doSearchDeudas($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [], Request $request){
        $columnMap = [
            "ID" => "id_deuda",
            "Concepto" => "ConceptoPago.descripcion",
            "Periodo" => "periodo",
            "Monto" => "monto_total",
        ];

        $query = Deuda::where('estado', '=', true);

        $requested = $request->session()->get('alumno');
        $query->where('id_alumno', '=', $requested->getKey());

        // Excluir deudas que fueron pagadas y validadas completamente
        // Paso 1: Obtener IDs de pagos que tienen estado_validacion = 'validado' en detalle_pago
        $idPagosValidados = DetallePago::where('estado_validacion', '=', 'validado')
            ->where('estado', '=', true)
            ->pluck('id_pago')
            ->unique()
            ->toArray();

        // Paso 2: Obtener IDs de deudas asociadas a esos pagos validados a través de distribucion_pago_deuda
        if (!empty($idPagosValidados)) {
            $idDeudasValidadas = DistribucionPagoDeuda::whereIn('id_pago', $idPagosValidados)
                ->pluck('id_deuda')
                ->unique()
                ->toArray();

            // Paso 3: Excluir esas deudas de la consulta
            if (!empty($idDeudasValidadas)) {
                $query->whereNotIn('id_deuda', $idDeudasValidadas);
            }
        }

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null) return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function indexDeudas(Request $request, $long = false){
        $requested = $request->session()->get('alumno');

        if ($requested == null){
            return redirect(route('principal'));
        }

        $sqlColumns = ['id_deuda', 'ConceptoPago.descripcion', 'periodo', 'fecha_limite', 'monto_total'];
        $resource = 'pagos';

        $params = RequestHelper::extractSearchParams($request);

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Deudas")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        $content = CRUDTableComponent::new()
            ->title("Deudas de tu alumno");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        $pagoButton = new TableButtonComponent("tablesv2.buttons.realizar-pago");
        $content->addButton($pagoButton);

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

        $query = static::doSearchDeudas($sqlColumns, $params->search, $params->showing, $params->applied_filters, $request);

        if ($params->page > $query->lastPage()){
            $params->page = 1;
            $query = static::doSearchDeudas($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        $periodosExistentes = Deuda::select("periodo")
            ->distinct()
            ->where("estado", "=", 1)
            ->pluck("periodo");


        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID", "Concepto", "Periodo", "Monto"
        ];
        $filterConfig->filterOptions = [
            "Periodo" => $periodosExistentes,
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Concepto", "Periodo", "Fecha Límite", "Monto"];
        $table->rows = [];

        foreach ($query as $deuda){
            $totalpagado = Pago::where('id_deuda', $deuda->id_deuda)
                ->where('estado', true)
                ->sum('monto');
            if ($totalpagado >= $deuda->monto_total) continue;
            array_push($table->rows,
            [
                $deuda->id_deuda,
                $deuda->concepto->descripcion,
                $deuda->periodo,
                \Carbon\Carbon::parse($deuda->fecha_limite)->format('d/m/Y'),
                $deuda->monto_total,
            ]);
        }

        $paginator = new TablePaginator($params->page, $query->lastPage(), []);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    private static function doSearchPagos($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [], Request $request){
        $columnMap = [
            'Mes' => 'ConceptoPago.descripcion',
            'Nº Orden' => 'OrdenPago.codigo_orden',
            'Monto Pagado' => 'monto_subtotal',
            'Estado' => 'estado_validacion',
        ];

        $query = \App\Models\DetalleOrdenPago::query();

        // Obtener el alumno seleccionado de la sesión
        $requested = $request->session()->get('alumno');

        // Filtrar por alumno a través de la orden de pago
        $query->whereHas('ordenPago', function($q) use ($requested) {
            $q->where('id_alumno', '=', $requested->getKey());
        });

        // Solo mostrar detalles de órdenes que tienen pagos asociados
        $query->whereHas('ordenPago.pagos', function($q) {
            $q->where('estado', '=', true);
        });

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null) return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function indexPagos(Request $request, $long = false){
        $requested = $request->session()->get('alumno');

        if ($requested == null){
            return redirect(route('principal'));
        }

        $sqlColumns = ['ConceptoPago.descripcion', 'OrdenPago.codigo_orden', 'monto_subtotal'];
        $resource = 'pagos';

        $params = RequestHelper::extractSearchParams($request);

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Pagos Anteriores")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        $content = CRUDTableComponent::new()
            ->title("Pagos Anteriores de tu alumno");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

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

        $query = static::doSearchPagos($sqlColumns, $params->search, $params->showing, $params->applied_filters, $request);

        if ($params->page > $query->lastPage()){
            $params->page = 1;
            $query = static::doSearchPagos($sqlColumns, $params->search, $params->showing, $params->applied_filters, $request);
        }

        // Obtener conceptos únicos para el filtro
        $conceptosExistentes = \App\Models\ConceptoPago::whereHas('detallesOrden', function($q) use ($requested) {
            $q->whereHas('ordenPago', function($q2) use ($requested) {
                $q2->where('id_alumno', '=', $requested->getKey())
                   ->whereHas('pagos', function($q3) {
                       $q3->where('estado', '=', true);
                   });
            });
        })->pluck('descripcion', 'descripcion');

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "Mes", "Nº Orden", "Monto Pagado"
        ];
        $filterConfig->filterOptions = [
            "Mes" => $conceptosExistentes,
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["Mes", "Nº Orden", "Monto Pagado", "Estado"];
        $table->rows = [];

        foreach ($query as $detalleOrden){
            // Obtener el estado de validación navegando por las relaciones
            $estadoValidacion = 'Sin estado';

            // Desde DetalleOrdenPago -> OrdenPago -> Pagos -> DetallePago
            if ($detalleOrden->ordenPago && $detalleOrden->ordenPago->pagos) {
                foreach ($detalleOrden->ordenPago->pagos as $pago) {
                    if ($pago->estado && $pago->detallesPago) {
                        foreach ($pago->detallesPago as $detallePago) {
                            if ($detallePago->estado_validacion) {
                                $estadoValidacion = ucfirst($detallePago->estado_validacion);
                                break 2; // Salir de ambos foreach
                            }
                        }
                    }
                }
            }

            array_push($table->rows, [
                $detalleOrden->conceptoPago ? $detalleOrden->conceptoPago->descripcion : 'N/A',
                $detalleOrden->ordenPago ? $detalleOrden->ordenPago->codigo_orden : 'N/A',
                'S/. ' . number_format($detalleOrden->monto_subtotal, 2),
                $estadoValidacion
            ]);
        }

        $paginator = new TablePaginator($params->page, $query->lastPage(), []);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }
}
