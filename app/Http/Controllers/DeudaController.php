<?php

namespace App\Http\Controllers;

use App\Models\Deuda;
use App\Models\Alumno;
use App\Models\ConceptoPago;
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

class DeudaController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_deuda',
            'Periodo' => 'periodo',
            'Monto Total' => 'monto_total',
            'Observaciones' => 'observacion',
        ];

        $query = Deuda::where('estado', '=', '1')
            ->with(['alumno', 'concepto']);

        // Búsqueda general
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id_deuda', 'LIKE', "%{$search}%")
                ->orWhere('periodo', 'LIKE', "%{$search}%")
                ->orWhere('monto_total', 'LIKE', "%{$search}%")
                ->orWhere('observacion', 'LIKE', "%{$search}%")
                // Buscar en relación alumno
                ->orWhereHas('alumno', function ($subQ) use ($search) {
                    $subQ->where('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('codigo_educando', 'LIKE', "%{$search}%");
                })
                // Buscar en relación concepto
                ->orWhereHas('concepto', function ($subQ) use ($search) {
                    $subQ->where('descripcion', 'LIKE', "%{$search}%");
                });
            });
        }

        // Filtros aplicados
        if (!empty($appliedFilters)) {
            foreach ($appliedFilters as $filter) {
                $key = $filter['key'] ?? null;
                $value = $filter['value'] ?? null;

                if (!$key || !$value) continue;

                // Filtros de columnas directas
                if (isset($columnMap[$key])) {
                    $sqlColumn = $columnMap[$key];
                    $query->where($sqlColumn, 'LIKE', "%{$value}%");
                }
                // Filtro por Alumno (busca en nombre o código)
                elseif ($key === 'Alumno') {
                    $query->whereHas('alumno', function ($subQ) use ($value) {
                        $subQ->where('primer_nombre', 'LIKE', "%{$value}%")
                            ->orWhere('apellido_paterno', 'LIKE', "%{$value}%")
                            ->orWhere('apellido_materno', 'LIKE', "%{$value}%")
                            ->orWhere('codigo_educando', 'LIKE', "%{$value}%");
                    });
                }
                // Filtro por Concepto
                elseif ($key === 'Concepto') {
                    $query->whereHas('concepto', function ($subQ) use ($value) {
                        $subQ->where('descripcion', 'LIKE', "%{$value}%");
                    });
                }
            }
        }

        $query->orderBy('id_concepto', 'asc')->orderBy('id_deuda', 'asc');

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_deuda', 'id_alumno', 'id_concepto', 'periodo', 'monto_total', 'observacion'];
        $resource = 'financiera';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Deudas")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Deudas");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "deuda_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "deuda_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "deuda_view"]);
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
            ->action('Estás eliminando la Deuda')
            ->columns([ 'Periodo', 'Alumno', 'Concepto', 'Monto Total','Observaciones'])
            ->rows(['', '', '', '',''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a esta Deuda.')
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
            "ID", "Periodo", "Alumno", "Concepto", "Monto Total", "Observaciones"
        ];
        $filterConfig->filterOptions = [];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Periodo", "Alumno", "Concepto", "Monto Total (S/)", "Observaciones"];
        $table->rows = [];

        foreach ($query as $deuda) {
            $alumnoNombre = $deuda->alumno
                ? trim($deuda->alumno->primer_nombre . ' ' . $deuda->alumno->apellido_paterno . ' ' . $deuda->alumno->apellido_materno)
                : 'Sin nombre';

            $conceptoNombre = $deuda->concepto
                ? $deuda->concepto->descripcion
                : 'Sin concepto';

            array_push($table->rows, [
                $deuda->id_deuda,
                $deuda->periodo,
                $alumnoNombre,
                $conceptoNombre,
                'S/ ' . number_format($deuda->monto_total, 2),
                $deuda->observacion ?? 'Sin observaciones',
            ]);
        }

        $table->actions = [
            new TableAction('edit', 'deuda_edit', $resource),
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
        $conceptos = ConceptoPago::where('estado', 1)->get();

        $escalasPorConcepto = [];
        $montosPorConceptoEscala = [];

        foreach ($conceptos as $concepto) {
            if (!isset($escalasPorConcepto[$concepto->id_concepto])) {
                $escalasPorConcepto[$concepto->id_concepto] = [];
            }
            if (!in_array($concepto->escala, $escalasPorConcepto[$concepto->id_concepto])) {
                $escalasPorConcepto[$concepto->id_concepto][] = $concepto->escala;
            }
            $montosPorConceptoEscala[$concepto->id_concepto][$concepto->escala] = $concepto->monto;
        }

        $data = [
            'return' => route('deuda_view', ['abort' => true]),
            'conceptos' => $conceptos,
            'escalasPorConcepto' => $escalasPorConcepto,
            'montosPorConceptoEscala' => $montosPorConceptoEscala,
        ];

        return view('gestiones.deuda.create', compact('data'));
    }

    public function createNewEntry(Request $request)
    {
        $request->validate([
            'codigo_educando' => [
                'required',
                'numeric',
                'exists:alumnos,codigo_educando'
            ],
            'id_concepto' => 'required|numeric',
            'fecha_limite' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'observacion' => 'nullable|max:255',
        ], [
            'codigo_educando.required' => 'Ingrese un código de educando.',
            'codigo_educando.numeric' => 'El código de educando debe ser numérico.',
            'codigo_educando.exists' => 'El alumno no existe.',
            'id_concepto.required' => 'Seleccione un concepto de pago.',
            'id_concepto.numeric' => 'El concepto de pago debe ser numérico.',
            'fecha_limite.required' => 'Ingrese una fecha límite válida.',
            'fecha_limite.date' => 'La fecha límite debe tener un formato de fecha válido.',
            'monto_total.required' => 'Ingrese un monto total válido.',
            'monto_total.numeric' => 'El monto total debe ser un número.',
            'monto_total.min' => 'El monto total no puede ser negativo.',
            'observacion.max' => 'La observación no puede superar los 255 caracteres.',
        ]);

        $alumno = Alumno::where('codigo_educando', $request->input('codigo_educando'))->first();

        Deuda::create([
            'id_alumno' => $alumno->id_alumno,
            'id_concepto' => $request->input('id_concepto'),
            'fecha_limite' => $request->input('fecha_limite'),
            'monto_total' => $request->input('monto_total'),
            'periodo' => $request->input('periodo'),
            'monto_a_cuenta' => 0,
            'monto_adelantado' => 0,
            'observacion' => $request->input('observacion'),
            'estado' => 1
        ]);

        return redirect(route('deuda_view', ['created' => true]));
    }

    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('deuda_view'));
        }

        $deuda = Deuda::findOrFail($id);

        $data = [
            'return' => route('deuda_view', ['abort' => true]),
            'id' => $id,
            'default' => [
                'id_alumno' => $deuda->id_alumno,
                'id_concepto' => $deuda->id_concepto,
                'fecha_limite' => $deuda->fecha_limite->format('Y-m-d'),
                'monto_total' => $deuda->monto_total,
                'periodo' => $deuda->periodo,
                'monto_a_cuenta' => $deuda->monto_a_cuenta,
                'monto_adelantado' => $deuda->monto_adelantado,
                'observacion' => $deuda->observacion,
            ]
        ];

        return view('gestiones.deuda.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('deuda_view'));
        }

        $request->validate([
            'fecha_limite' => 'required|date',
            'monto_total' => 'required|numeric|min:0',
            'observacion' => 'nullable|max:255',
        ], [
            'fecha_limite.required' => 'Ingrese una fecha límite válida.',
            'fecha_limite.date' => 'La fecha límite debe tener un formato de fecha válido.',
            'monto_total.required' => 'Ingrese un monto total válido.',
            'monto_total.numeric' => 'El monto total debe ser un número.',
            'monto_total.min' => 'El monto total no puede ser negativo.',
            'observacion.max' => 'La observación no puede superar los 255 caracteres.',
        ]);

        $deuda = Deuda::find($id);

        if (!$deuda) {
            return redirect()->route('deuda_view')->with('error', 'Deuda no encontrada.');
        }

        $deuda->update([
            'fecha_limite' => $request->input('fecha_limite'),
            'monto_total' => $request->input('monto_total'),
            'observacion' => $request->input('observacion'),
        ]);

        return redirect()->route('deuda_view', ['edited' => true]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $deuda = Deuda::findOrFail($id);
        $deuda->update(['estado' => '0']);

        return redirect(route('deuda_view', ['deleted' => true]));
    }

    /* ==================== EXPORTACIÓN ==================== */

    public function export(Request $request)
    {
        try {
            $format = $request->input('export', 'excel');

            $sqlColumns = ['id_deuda', 'id_alumno', 'id_concepto', 'periodo', 'monto_total', 'observacion'];

            $params = RequestHelper::extractSearchParams($request);

            $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

            \Log::info('Exportando deudas', [
                'format' => $format,
                'total_records' => $query->count(),
            ]);

            if ($format === 'pdf') {
                return $this->exportPdf($query);
            }

            return $this->exportExcel($query);

        } catch (\Exception $e) {
            \Log::error('Error en exportación de deudas: ' . $e->getMessage());
            return back()->with('error', 'Error durante la exportación');
        }
    }

    private function exportExcel($deudas)
    {
        $headers = ['ID', 'Periodo', 'Alumno', 'Concepto', 'Monto Total (S/)', 'Observaciones'];
        $fileName = 'deudas_' . date('Y-m-d_H-i-s') . '.xlsx';

        return ExcelExportHelper::exportExcel(
            $fileName,
            $headers,
            $deudas,
            function ($sheet, $row, $deuda) {
                $alumnoNombre = $deuda->alumno
                    ? trim($deuda->alumno->primer_nombre . ' ' . $deuda->alumno->apellido_paterno . ' ' . $deuda->alumno->apellido_materno)
                    : 'Sin nombre';

                $conceptoNombre = $deuda->concepto
                    ? $deuda->concepto->descripcion
                    : 'Sin concepto';

                $sheet->setCellValue('A' . $row, $deuda->id_deuda);
                $sheet->setCellValue('B' . $row, $deuda->periodo ?? 'N/A');
                $sheet->setCellValue('C' . $row, $alumnoNombre);
                $sheet->setCellValue('D' . $row, $conceptoNombre);
                $sheet->setCellValue('E' . $row, 'S/ ' . number_format($deuda->monto_total, 2));
                $sheet->setCellValue('F' . $row, $deuda->observacion ?? 'Sin observaciones');
            },
            'Deudas',
            'Exportación de Deudas',
            'Listado de deudas del sistema'
        );
    }

    private function exportPdf($deudas)
    {
        if ($deudas->isEmpty()) {
            return response()->json(['error' => 'No hay datos para exportar'], 400);
        }

        $fileName = 'deudas_' . date('Y-m-d_H-i-s') . '.pdf';

        $rows = $deudas->map(function ($deuda) {
            $alumnoNombre = $deuda->alumno
                ? trim($deuda->alumno->primer_nombre . ' ' . $deuda->alumno->apellido_paterno . ' ' . $deuda->alumno->apellido_materno)
                : 'Sin nombre';

            $conceptoNombre = $deuda->concepto
                ? $deuda->concepto->descripcion
                : 'Sin concepto';

            return [
                $deuda->id_deuda,
                $deuda->periodo ?? 'N/A',
                $alumnoNombre,
                $conceptoNombre,
                'S/ ' . number_format($deuda->monto_total, 2),
                $deuda->observacion ?? 'Sin observaciones'
            ];
        })->toArray();

        $html = PDFExportHelper::generateTableHtml([
            'title' => 'Deudas',
            'subtitle' => 'Listado de Deudas',
            'headers' => ['ID', 'Periodo', 'Alumno', 'Concepto', 'Monto Total', 'Observaciones'],
            'rows' => $rows,
            'footer' => 'Sistema de Gestión Académica SIGMA - Generado automáticamente',
        ]);

        return PDFExportHelper::exportPdf($fileName, $html);
    }
}