<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\ConceptoPago;
use App\Models\Deuda;
use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\OrdenPago;
use App\Models\DistribucionPagoDeuda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
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

class PagoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_pago',
            'Fecha de Pago' => 'fecha_pago',
            'Monto' => 'monto',
            'Observaciones' => 'observaciones',
        ];

        // Columnas que deben usar búsqueda por fecha (LIKE con año-mes o año)
        $dateColumns = ['fecha_pago'];

        $query = Pago::where('estado', '=', '1');

        // Búsqueda general
        if (!empty($search)) {
            $query->where(function($q) use ($sqlColumns, $search) {
                foreach ($sqlColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        // Filtros aplicados
        if (!empty($appliedFilters)) {
            foreach ($appliedFilters as $filter) {
                $key = $filter['key'] ?? null;
                $value = $filter['value'] ?? null;

                if ($key && $value && isset($columnMap[$key])) {
                    $sqlColumn = $columnMap[$key];

                    // Si es columna de fecha, usar LIKE para búsqueda parcial
                    if (in_array($sqlColumn, $dateColumns)) {
                        // Permite buscar por: "2025", "2025-01", "01/2025", "enero", etc.
                        $query->where(function($q) use ($sqlColumn, $value) {
                            // Formato YYYY-MM-DD
                            $q->where($sqlColumn, 'LIKE', "%{$value}%");
                            
                            // Si viene en formato DD/MM/YYYY, convertir
                            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
                                $fechaFormateada = $matches[3] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                                $q->orWhere($sqlColumn, 'LIKE', "%{$fechaFormateada}%");
                            }
                            
                            // Si viene solo mes/año (MM/YYYY)
                            if (preg_match('/^(\d{1,2})\/(\d{4})$/', $value, $matches)) {
                                $fechaFormateada = $matches[2] . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                                $q->orWhere($sqlColumn, 'LIKE', "%{$fechaFormateada}%");
                            }
                        });
                    } else {
                        $query->where($sqlColumn, 'LIKE', "%{$value}%");
                    }
                }
            }
        }

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_pago', 'fecha_pago', 'monto', 'observaciones'];
        $resource = 'financiera';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Pagos")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Pagos");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "pago_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "pago_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "pago_view"]);
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
            ->action('Estás eliminando el Pago')
            ->columns(['Concepto de Pago', 'Estudiante', 'Fecha de Pago', 'Monto','Observaciones'])
            ->rows(['', '', '', '',''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Pago.')
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
            "ID", "Fecha de Pago", "Monto", "Observaciones"
        ];
        $filterConfig->filterOptions = [];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Concepto de Pago", "Nombre del Alumno", "Fecha de Pago", "Monto", "Observaciones"];
        $table->rows = [];

        foreach ($query as $pago) {
            // Cargar relaciones según el tipo de pago
            if ($pago->id_deuda) {
                $pago = Pago::with(['deuda.conceptoPago', 'deuda.alumno'])->findOrFail($pago->id_pago);

                $concepto = $pago->deuda->conceptoPago->descripcion ?? 'Sin concepto';
                $alumno = trim(
                    ($pago->deuda->alumno->primer_nombre ?? '') . ' ' .
                    ($pago->deuda->alumno->otros_nombres ?? '') . ' ' .
                    ($pago->deuda->alumno->apellido_paterno ?? '') . ' ' .
                    ($pago->deuda->alumno->apellido_materno ?? '')
                ) ?: 'Sin nombre';

            } else if ($pago->id_orden) {
                $pago = Pago::with(['ordenPago.alumno'])->findOrFail($pago->id_pago);

                $tipoPagoLabel = match ($pago->tipo_pago) {
                    'orden_completa' => 'ORDEN COMPLETA',
                    'orden_parcial' => 'ORDEN PARCIAL',
                    default => 'PAGO DE ORDEN'
                };
                $concepto = $tipoPagoLabel;

                $alumno = trim(
                    ($pago->ordenPago->alumno->primer_nombre ?? '') . ' ' .
                    ($pago->ordenPago->alumno->otros_nombres ?? '') . ' ' .
                    ($pago->ordenPago->alumno->apellido_paterno ?? '') . ' ' .
                    ($pago->ordenPago->alumno->apellido_materno ?? '')
                ) ?: 'Sin nombre';

            } else {
                $concepto = 'Sin concepto';
                $alumno = 'Sin nombre';
            }

            $fechaPago = $pago->fecha_pago instanceof Carbon
                ? $pago->fecha_pago->format('d/m/Y')
                : Carbon::parse($pago->fecha_pago)->format('d/m/Y');

            $observaciones = $pago->observaciones ?? 'Sin Observaciones';

            array_push($table->rows, [
                $pago->id_pago,
                $concepto,
                $alumno,
                $fechaPago,
                'S/ ' . number_format($pago->monto, 2),
                $observaciones,
            ]);
        }

        $table->actions = [
            new TableAction('edit', 'pago_edit', $resource),
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
        $alumnos = Alumno::all(['id_alumno', 'codigo_educando']);
        $deudas = Deuda::all(['id_deuda', 'id_alumno', 'id_concepto', 'periodo', 'monto_total']);
        $conceptos = ConceptoPago::all(['id_concepto', 'descripcion']);

        $data = [
            'return' => route('pago_view', ['abort' => true]),
            'alumnos' => $alumnos,
            'deudas' => $deudas,
            'conceptos' => $conceptos,
        ];
        return view('gestiones.pago.create', compact('data'));
    }

    public function crearPagoOrden(Request $request)
    {
        return view('gestiones.pago.registrar_pago_orden');
    }

    public function createNewEntry(Request $request)
    {
        $request->validate([
            'codigo_alumno' => 'required|string',
            'id_deuda' => 'required|exists:deudas,id_deuda',
        ], [
            'codigo_alumno.required' => 'Ingrese el código del estudiante.',
            'id_deuda.required' => 'Seleccione una deuda.',
            'id_deuda.exists' => 'La deuda seleccionada no es válida.',
        ]);

        $metodoPago = $request->input('metodo_pago', []);
        $detalleRecibo = $request->input('detalle_recibo', []);
        $detalleMonto = $request->input('detalle_monto', []);
        $detalleFecha = $request->input('detalle_fecha', []);
        $detallesExistentes = (int) $request->input('detalles_existentes', 0);

        $deuda = Deuda::with('conceptoPago')->findOrFail($request->input('id_deuda'));
        $conceptoPago = $deuda->conceptoPago;

        $hoy = Carbon::now();
        $mesActual = $hoy->month;
        $anioActual = $hoy->year;

        $descripcion = $conceptoPago->descripcion ?? '';
        $partes = explode(' ', $descripcion);

        $meses = [
            'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
            'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
            'SETIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
        ];

        $esAdelanto = false;
        if (count($partes) >= 2) {
            $mesDeuda = $meses[$partes[0]] ?? 0;
            $anioDeuda = intval($partes[1]);

            if ($anioDeuda > $anioActual || ($anioDeuda == $anioActual && $mesDeuda > $mesActual)) {
                $esAdelanto = true;
            }
        }

        if ($esAdelanto) {
            if ($detallesExistentes > 0) {
                return back()->withErrors(['id_deuda' => 'Los adelantos deben pagarse en una sola vez. Esta deuda ya tiene un pago registrado.'])->withInput();
            }

            $montoTotalIntento = 0;
            foreach (range(0, 1) as $index) {
                $monto = $detalleMonto[$index] ?? null;
                if (!empty($monto) && is_numeric($monto)) {
                    $montoTotalIntento += floatval($monto);
                }
            }

            $montoDeuda = floatval($deuda->monto_total);
            if (abs($montoTotalIntento - $montoDeuda) > 0.01) {
                return back()->withErrors([
                    'detalle_monto.0' => sprintf(
                        'Los adelantos deben pagarse completos. El monto total debe ser S/ %.2f (ingresó S/ %.2f)',
                        $montoDeuda,
                        $montoTotalIntento
                    )
                ])->withInput();
            }
        }

        $detallesCompletos = [];
        $detallesIncompletos = [];

        foreach (range(0, 1) as $index) {
            if ($index < $detallesExistentes) continue;

            $metodo = $metodoPago[$index] ?? null;
            $recibo = $detalleRecibo[$index] ?? null;
            $monto = $detalleMonto[$index] ?? null;
            $fecha = $detalleFecha[$index] ?? null;

            $tieneDatos = !empty($metodo) || !empty($recibo) || !empty($monto) || !empty($fecha);

            if ($tieneDatos) {
                $estaCompleto = !empty($metodo) && !empty($recibo) && !empty($monto) && !empty($fecha);

                if ($estaCompleto) {
                    $detallesCompletos[] = $index;
                } else {
                    $detallesIncompletos[] = $index;
                }
            }
        }

        if ($detallesExistentes === 0 && empty($detallesCompletos) && empty($detallesIncompletos)) {
            return back()->withErrors(["metodo_pago.0" => 'Debe ingresar al menos un detalle de pago.'])->withInput();
        }

        if ($detallesExistentes > 0 && empty($detallesCompletos) && empty($detallesIncompletos)) {
            return back()->with('info', 'No se realizaron cambios.')->withInput();
        }

        if ($detallesExistentes + count($detallesCompletos) + count($detallesIncompletos) > 2) {
            return back()->withErrors(['metodo_pago.1' => 'Solo puede registrar hasta 2 detalles de pago.'])->withInput();
        }

        $errors = [];

        foreach ($detallesIncompletos as $index) {
            $metodo = $metodoPago[$index] ?? null;
            $recibo = $detalleRecibo[$index] ?? null;
            $monto = $detalleMonto[$index] ?? null;
            $fecha = $detalleFecha[$index] ?? null;

            if (empty($metodo)) {
                $errors["metodo_pago.{$index}"] = "Seleccione un método de pago para el detalle " . ($index + 1) . ".";
            }

            if (empty($recibo)) {
                $errors["detalle_recibo.{$index}"] = "Ingrese el número de operación para el detalle " . ($index + 1) . ".";
            }

            if (empty($monto)) {
                $errors["detalle_monto.{$index}"] = "Ingrese el monto para el detalle " . ($index + 1) . ".";
            } elseif (!is_numeric($monto)) {
                $errors["detalle_monto.{$index}"] = "El monto del detalle " . ($index + 1) . " debe ser numérico.";
            } elseif (floatval($monto) <= 0) {
                $errors["detalle_monto.{$index}"] = "El monto del detalle " . ($index + 1) . " debe ser mayor a 0.";
            }

            if (empty($fecha)) {
                $errors["detalle_fecha.{$index}"] = "Ingrese la fecha para el detalle " . ($index + 1) . ".";
            } elseif (!strtotime($fecha)) {
                $errors["detalle_fecha.{$index}"] = "La fecha del detalle " . ($index + 1) . " no es válida.";
            }
        }

        foreach ($detallesCompletos as $index) {
            $metodo = strtolower(trim($metodoPago[$index] ?? ''));

            if (in_array($metodo, ['transferencia', 'yape', 'plin'])) {
                if (!$request->hasFile("voucher_path.$index")) {
                    $errors["voucher_path.{$index}"] = "Debe subir la constancia de pago para el método: " . ucfirst($metodo) . ".";
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $detallesValidos = $detallesCompletos;

        DB::transaction(function () use ($request, $detallesValidos) {
            $idDeuda = $request->input('id_deuda');
            $fechas = $request->input('detalle_fecha');
            $montos = $request->input('detalle_monto');
            $recibos = $request->input('detalle_recibo');
            $observaciones = $request->input('detalle_observaciones', []);
            $metodos = $request->input('metodo_pago');

            $montoTotalPago = 0;
            foreach ($detallesValidos as $index) {
                $montoTotalPago += floatval($montos[$index] ?? 0);
            }

            $ultimaFecha = null;
            foreach ($detallesValidos as $index) {
                $ultimaFecha = $fechas[$index];
            }

            $pagoExistente = Pago::where('id_deuda', $idDeuda)
                ->where('estado', 1)
                ->first();

            $deudaObj = Deuda::with('detallesOrdenPago.ordenPago')->find($idDeuda);
            $idOrden = null;

            if ($deudaObj && $deudaObj->detallesOrdenPago->isNotEmpty()) {
                $detalleOrden = $deudaObj->detallesOrdenPago->first();
                if ($detalleOrden && $detalleOrden->ordenPago) {
                    $idOrden = $detalleOrden->ordenPago->id_orden;
                }
            }

            if ($pagoExistente) {
                $pago = $pagoExistente;
                if (!$pago->id_orden && $idOrden) {
                    $pago->update(['id_orden' => $idOrden]);
                }
            } else {
                $pago = Pago::create([
                    'id_deuda' => $idDeuda,
                    'id_orden' => $idOrden,
                    'fecha_pago' => $ultimaFecha,
                    'monto' => 0,
                    'observaciones' => $request->input('observaciones'),
                    'estado_validacion' => 'pendiente',
                ]);
            }

            foreach ($detallesValidos as $index) {
                $metodo = strtolower(trim($metodos[$index] ?? ''));
                $recibo = $recibos[$index] ?? null;
                $monto = floatval($montos[$index] ?? 0);
                $fecha = $fechas[$index] ?? null;
                $obs = $observaciones[$index] ?? null;

                $voucherPath = null;
                if (in_array($metodo, ['transferencia', 'yape', 'plin'])) {
                    $voucherPath = $request->file("voucher_path.$index")->store('vouchers', 'public');
                }

                DetallePago::create([
                    'id_pago' => $pago->getKey(),
                    'fecha_pago' => $fecha,
                    'monto' => $monto,
                    'nro_recibo' => $recibo,
                    'observacion' => $obs,
                    'metodo_pago' => $metodo,
                    'voucher_path' => $voucherPath,
                    'estado' => 1,
                    'estado_validacion' => 'pendiente',
                ]);
            }

            $montoTotalCalculado = DetallePago::where('id_pago', $pago->getKey())
                ->where('estado', 1)
                ->sum('monto');

            $pago->update([
                'fecha_pago' => $ultimaFecha,
                'monto' => $montoTotalCalculado,
                'observaciones' => $request->input('observaciones') ?: $pago->observaciones,
            ]);

            if ($pago->id_orden) {
                $ordenPago = OrdenPago::find($pago->id_orden);
                if ($ordenPago && $ordenPago->estaPagadaCompletamente()) {
                    $ordenPago->update(['estado' => 'pagado']);
                }
            }

            $deuda = Deuda::find($idDeuda);
            if ($deuda) {
                $conceptoPago = ConceptoPago::find($deuda->id_concepto);

                $hoy = Carbon::now();
                $mesActual = $hoy->month;
                $anioActual = $hoy->year;

                $descripcion = $conceptoPago->descripcion ?? '';
                $partes = explode(' ', $descripcion);

                $meses = [
                    'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
                    'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
                    'SETIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
                ];

                $esAdelanto = false;
                if (count($partes) >= 2) {
                    $mesDeuda = $meses[$partes[0]] ?? 0;
                    $anioDeuda = intval($partes[1]);

                    if ($anioDeuda > $anioActual || ($anioDeuda == $anioActual && $mesDeuda > $mesActual)) {
                        $esAdelanto = true;
                    }
                }

                if ($esAdelanto) {
                    $deuda->update([
                        'monto_adelantado' => $montoTotalCalculado
                    ]);
                } else {
                    $montoActualACuenta = $deuda->monto_a_cuenta ?? 0;
                    $deuda->update([
                        'monto_a_cuenta' => $montoActualACuenta + $montoTotalPago
                    ]);
                }
            }
        });

        return redirect()->route('pago_view')
            ->with('success', 'Pago registrado correctamente en estado pendiente.');
    }


    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('pago_view'));
        }

        $pago = Pago::findOrFail($id);

        $data = [
            'return' => route('pago_view', ['abort' => true]),
            'id' => $id,
            'default' => [
                'fecha_pago' => $pago->fecha_pago instanceof \Carbon\Carbon ? $pago->fecha_pago->format('Y-m-d\TH:i') : \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d\TH:i'),                'monto' => $pago->monto,
                'observaciones' => $pago->observaciones,
            ]
        ];

        return view('gestiones.pago.edit', compact('data'));
    }

    public function editEntry(Request $request, $id){
        if (!isset($id)){
            return redirect(route('pago_view'));
        }

        $requested = Pago::find($id);

        if (!$requested) {
            return redirect()->route('pago_view')->with('error', 'Deuda no encontrada.');
        }

        $request->validate([
            'fecha_pago' => 'required|date',
            'monto' => 'required|numeric',
            'observaciones' => 'nullable|max:255',
        ], [
            'fecha_pago.required' => 'Ingrese una fecha vÃ¡lida.',
            'fecha_pago.date' => 'La fecha debe tener un formato vÃ¡lido.',
            'monto.required' => 'Ingrese un monto vÃ¡lido.',
            'monto.numeric' => 'El monto debe ser numÃ©rico.',
            'observaciones.max' => 'Las observaciones no pueden superar los 255 caracteres.',
        ]);

        $requested->update([
            'fecha_pago' => $request->input('fecha_pago'),
            'monto' => $request->input('monto'),
            'observaciones' => $request->input('observaciones'),
        ]);

        return redirect()->route('pago_view')->with('success', 'Pago actualizado correctamente.');
    }

    public function delete(Request $request){
        $id = $request->input('id');

        $requested = Pago::find($id);
        $requested->update(['estado' => '0']);

        return redirect()->route('pago_view')->with('success', 'Pago desactivado correctamente.');
    }

    public function viewDetalles($id)
    {
        if (!isset($id)) {
            return redirect(route('pago_view'));
        }

        $pago = Pago::findOrFail($id);

        $detalles = DetallePago::where('id_pago', $id)->get();

        // Obtener alumno según el tipo de pago
        if ($pago->id_deuda) {
            // Pago de deuda individual
            $alumno = Alumno::select(
                'codigo_educando',
                'codigo_modular',
                'año_ingreso',
                'dni',
                'apellido_paterno',
                'apellido_materno',
                'primer_nombre',
                'otros_nombres',
                'sexo',
                'fecha_nacimiento',
                'direccion',
                'telefono'
            )
            ->where('id_alumno', function($q) use ($pago) {
                $q->select('id_alumno')
                ->from('deudas')
                ->where('id_deuda', $pago->id_deuda)
                ->limit(1);
            })
            ->first();
            
        } else if ($pago->id_orden) {
            // Pago de orden - obtener alumno desde la orden
            $alumno = Alumno::select(
                'codigo_educando',
                'codigo_modular',
                'año_ingreso',
                'dni',
                'apellido_paterno',
                'apellido_materno',
                'primer_nombre',
                'otros_nombres',
                'sexo',
                'fecha_nacimiento',
                'direccion',
                'telefono'
            )
            ->where('id_alumno', function($q) use ($pago) {
                $q->select('id_alumno')
                ->from('ordenes_pago')
                ->where('id_orden', $pago->id_orden)
                ->limit(1);
            })
            ->first();
            
        } else {
            // Sin deuda ni orden
            $alumno = null;
        }

        return view('gestiones.pago.detalles', compact('pago', 'detalles', 'alumno'));
    }

    public function completar($id)
    {
        if (!isset($id)) {
            return redirect(route('pago_view'));
        }

        $pago = Pago::findOrFail($id);
        
        $cantidadDetalles = DetallePago::where('id_pago', $id)
            ->where('estado', 1)
            ->count();
            
        if ($cantidadDetalles !== 1) {
            return redirect(route('pago_view'))
                ->withErrors(['error' => 'Este pago no puede ser completado.']);
        }

        // Verificar si es pago de deuda o de orden
        if ($pago->id_deuda) {
            // Pago de deuda individual
            $deuda = Deuda::findOrFail($pago->id_deuda);
            $alumno = Alumno::findOrFail($deuda->id_alumno);
            $conceptoPago = ConceptoPago::findOrFail($deuda->id_concepto);
        } else if ($pago->id_orden) {
            // Pago de orden
            if ($pago->tipo_pago === 'orden_completa') {
                // Orden completa no puede completarse
                return redirect(route('pago_view'))
                    ->withErrors(['error' => 'Los pagos de orden completa no pueden completarse. Ya fue pagado el monto total.']);
            }
            
            // Para orden_parcial, obtener datos desde la orden
            $ordenPago = OrdenPago::with('alumno')->findOrFail($pago->id_orden);
            $alumno = $ordenPago->alumno;
            
            // Para orden parcial, usar el tipo de pago como "concepto"
            $conceptoPago = (object)[
                'id_concepto' => null,
                'descripcion' => 'ORDEN PARCIAL'
            ];
            
            // Crear objeto deuda simulado con los datos de la orden
            $deuda = (object)[
                'id_deuda' => null,
                'id_alumno' => $ordenPago->id_alumno,
                'id_concepto' => null,
                'monto_total' => $ordenPago->monto_total,
                'periodo' => null,
            ];
        } else {
            // Sin deuda ni orden - error
            return redirect(route('pago_view'))
                ->withErrors(['error' => 'Este pago no tiene una deuda u orden asociada.']);
        }

        $detalleExistente = DetallePago::where('id_pago', $id)
            ->where('estado', 1)
            ->first();

        $data = [
            'return' => route('pago_view', ['abort' => true]),
            'id_pago' => $id,
            'pago' => $pago,
            'deuda' => $deuda,
            'alumno' => $alumno,
            'concepto' => $conceptoPago,
            'detalle_existente' => $detalleExistente,
        ];

        return view('gestiones.pago.completar', compact('data'));
    }

    public function completarPago(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);
        
        $cantidadDetalles = DetallePago::where('id_pago', $id)
            ->where('estado', 1)
            ->count();
            
        if ($cantidadDetalles !== 1) {
            return redirect(route('pago_view'))
                ->withErrors(['error' => 'Este pago no puede ser completado.']);
        }

        // Verificar que sea un pago de deuda o de orden parcial
        if ($pago->id_orden && $pago->tipo_pago === 'orden_completa') {
            return redirect(route('pago_view'))
                ->withErrors(['error' => 'Los pagos de orden completa no pueden completarse. Ya fue pagado el monto total.']);
        }

        $rules = [
            'metodo_pago' => 'required|string|in:tarjeta,yape,plin,transferencia,paypal',
            'detalle_recibo' => 'required|string',
            'detalle_monto' => 'required|numeric|min:0.01',
            'detalle_fecha' => 'required|date',
        ];

        $messages = [
            'metodo_pago.required' => 'Seleccione un mÃ©todo de pago.',
            'metodo_pago.in' => 'El mÃ©todo de pago seleccionado no es vÃ¡lido.',
            'detalle_recibo.required' => 'Ingrese el nÃºmero de operaciÃ³n o recibo.',
            'detalle_monto.required' => 'Ingrese el monto del pago.',
            'detalle_monto.numeric' => 'El monto debe ser un valor numÃ©rico.',
            'detalle_monto.min' => 'El monto debe ser mayor a 0.',
            'detalle_fecha.required' => 'Ingrese la fecha del pago.',
            'detalle_fecha.date' => 'La fecha ingresada no es vÃ¡lida.',
        ];

        $metodo = strtolower(trim($request->input('metodo_pago', '')));
        if (in_array($metodo, ['transferencia', 'yape', 'plin'])) {
            $rules['voucher_path'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            $messages['voucher_path.required'] = 'Debe subir la constancia de pago para ' . ucfirst($metodo) . '.';
            $messages['voucher_path.file'] = 'El archivo no es vÃ¡lido.';
            $messages['voucher_path.mimes'] = 'El archivo debe ser JPG, PNG o PDF.';
            $messages['voucher_path.max'] = 'El archivo no debe superar los 2MB.';
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Obtener datos según el tipo de pago
        if ($pago->id_deuda) {
            // Pago de deuda individual
            $deuda = Deuda::findOrFail($pago->id_deuda);
            $conceptoPago = ConceptoPago::find($deuda->id_concepto);
            $montoTotal = $deuda->monto_total;
            
            // Validación de adelantos solo para deudas
            $hoy = Carbon::now();
            $mesActual = $hoy->month;
            $anioActual = $hoy->year;
            
            $descripcion = $conceptoPago->descripcion ?? '';
            $partes = explode(' ', $descripcion);
            
            $meses = [
                'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
                'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
                'SETIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
            ];
            
            $esAdelanto = false;
            if (count($partes) >= 2) {
                $mesDeuda = $meses[$partes[0]] ?? 0;
                $anioDeuda = intval($partes[1]);
                
                if ($anioDeuda > $anioActual || ($anioDeuda == $anioActual && $mesDeuda > $mesActual)) {
                    $esAdelanto = true;
                }
            }
            
            if ($esAdelanto) {
                return redirect()->back()
                    ->withErrors(['error' => 'Los adelantos deben pagarse completos en una sola transacción. No se pueden pagar por partes.'])
                    ->withInput();
            }
            
        } else if ($pago->id_orden) {
            // Pago de orden parcial
            $ordenPago = OrdenPago::findOrFail($pago->id_orden);
            $montoTotal = $ordenPago->monto_total;
            
        } else {
            return redirect(route('pago_view'))
                ->withErrors(['error' => 'Este pago no tiene una deuda u orden asociada.']);
        }
        
        $detalleExistente = DetallePago::where('id_pago', $id)
            ->where('estado', 1)
            ->first();
        
        $montoRestante = $montoTotal - $detalleExistente->monto;
        $montoIngresado = floatval($request->input('detalle_monto'));
        
        if ($montoIngresado > $montoRestante) {
            return redirect()->back()
                ->withErrors(['detalle_monto' => 'El monto no puede exceder el restante (S/ ' . number_format($montoRestante, 2) . ').'])
                ->withInput();
        }

        try {
            DB::transaction(function() use ($request, $pago, $metodo) {
                $voucherPath = null;
                if (in_array($metodo, ['transferencia', 'yape', 'plin']) && $request->hasFile('voucher_path')) {
                    $voucherPath = $request->file('voucher_path')->store('vouchers', 'public');
                }

                DetallePago::create([
                    'id_pago' => $pago->id_pago,
                    'fecha_pago' => $request->input('detalle_fecha'),
                    'monto' => $request->input('detalle_monto'),
                    'nro_recibo' => $request->input('detalle_recibo'),
                    'observacion' => null,
                    'metodo_pago' => $metodo,
                    'voucher_path' => $voucherPath,
                    'estado' => 1,
                    'estado_validacion' => 'pendiente',
                ]);

                $montoTotalCalculado = DetallePago::where('id_pago', $pago->id_pago)
                    ->where('estado', 1)
                    ->sum('monto');

                $pago->update([
                    'monto' => $montoTotalCalculado,
                    'observaciones' => $request->input('observaciones') ?: $pago->observaciones,
                ]);

                // Verificar si el pago está relacionado con una orden y actualizar su estado
                if ($pago->id_orden) {
                    $ordenPago = OrdenPago::find($pago->id_orden);
                    
                    // DISTRIBUIR EL MONTO ADICIONAL ENTRE LAS DEUDAS
                    $montoNuevoDetalle = floatval($request->input('detalle_monto'));
                    $montoRestante = $montoNuevoDetalle;
                    
                    foreach ($ordenPago->detalles as $detalle) {
                        if ($montoRestante <= 0) break;

                        $deuda = $detalle->deuda;
                        $montoPendienteDeuda = $deuda->monto_total - ($deuda->monto_a_cuenta + $deuda->monto_adelantado);

                        if ($montoPendienteDeuda <= 0) continue; // Ya está pagada

                        // Calcular cuánto aplicar a esta deuda
                        $montoAAplicar = min($montoRestante, $montoPendienteDeuda);

                        // Crear registro de distribución
                        \App\Models\DistribucionPagoDeuda::create([
                            'id_pago' => $pago->id_pago,
                            'id_deuda' => $deuda->id_deuda,
                            'monto_aplicado' => $montoAAplicar,
                        ]);

                        // Actualizar la deuda
                        // Si es adelanto, va a monto_adelantado, si no va a monto_a_cuenta
                        $hoy = Carbon::now();
                        $mesActual = $hoy->month;
                        $anioActual = $hoy->year;
                        
                        $descripcion = $deuda->conceptoPago->descripcion ?? '';
                        $partes = explode(' ', $descripcion);
                        
                        $meses = [
                            'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
                            'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
                            'SETIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
                        ];
                        
                        $esAdelanto = false;
                        if (count($partes) >= 2) {
                            $mesDeuda = $meses[$partes[0]] ?? 0;
                            $anioDeuda = intval($partes[1]);

                            if ($anioDeuda > $anioActual || ($anioDeuda == $anioActual && $mesDeuda > $mesActual)) {
                                $esAdelanto = true;
                            }
                        }

                        if ($esAdelanto) {
                            $deuda->monto_adelantado += $montoAAplicar;
                        } else {
                            $deuda->monto_a_cuenta += $montoAAplicar;
                        }
                        
                        $deuda->save();

                        $montoRestante -= $montoAAplicar;
                    }
                    
                    // Verificar si la orden está completamente pagada
                    if ($ordenPago && $ordenPago->estaPagadaCompletamente()) {
                        $ordenPago->update(['estado' => 'pagado']);
                    }
                }

                // Solo actualizar deuda directamente si es pago de deuda individual
                $deuda = Deuda::find($pago->id_deuda);
                if ($deuda) {
                    $conceptoPago = ConceptoPago::find($deuda->id_concepto);
                    
                    $hoy = Carbon::now();
                    $mesActual = $hoy->month;
                    $anioActual = $hoy->year;
                    
                    $descripcion = $conceptoPago->descripcion ?? '';
                    $partes = explode(' ', $descripcion);
                    
                    $meses = [
                        'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
                        'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
                        'SETIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
                    ];
                    
                    $esAdelanto = false;
                    if (count($partes) >= 2) {
                        $mesDeuda = $meses[$partes[0]] ?? 0;
                        $anioDeuda = intval($partes[1]);
                        
                        if ($anioDeuda > $anioActual || ($anioDeuda == $anioActual && $mesDeuda > $mesActual)) {
                            $esAdelanto = true;
                        }
                    }
                    
                    $montoIngresado = floatval($request->input('detalle_monto'));
                    
                    if ($esAdelanto) {
                        $deuda->update([
                            'monto_adelantado' => $montoTotalCalculado
                        ]);
                    } else {
                        $montoActualACuenta = $deuda->monto_a_cuenta ?? 0;
                        $deuda->update([
                            'monto_a_cuenta' => $montoActualACuenta + $montoIngresado
                        ]);
                    }
                }
            });
            
            \Log::info('TransacciÃ³n completada exitosamente');
            
        } catch (\Exception $e) {
            \Log::error('Error en transacciÃ³n', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Error al procesar el pago: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('pago_view')
            ->with('success', 'Pago completado correctamente.');
    }

    public function buscarAlumno($codigo)
    {
        \Log::info('Buscando alumno con código: ' . $codigo);
        
        $alumno = Alumno::where('codigo_educando', $codigo)->first();
        
        \Log::info('Resultado búsqueda alumno: ' . ($alumno ? 'ENCONTRADO - ID: ' . $alumno->id_alumno : 'NO ENCONTRADO'));

        if (!$alumno) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el alumno con ese código.'
            ], 404);
        }

        $ultimaOrden = \DB::table('ordenes_pago')
            ->where('id_alumno', $alumno->id_alumno)
            ->where('estado', 'pendiente')
            ->orderBy('fecha_orden_pago', 'desc')
            ->first();

        if (!$ultimaOrden) {
            return response()->json([
                'success' => false,
                'sin_orden' => true,
                'alumno' => [
                    'codigo_educando' => $alumno->codigo_educando,
                    'nombre_completo' => trim($alumno->apellido_paterno . ' ' . $alumno->apellido_materno . ' ' . $alumno->primer_nombre)
                ],
                'message' => 'Este alumno no tiene ninguna orden de pago pendiente. Debe generar una orden de pago antes de poder registrar un pago.'
            ], 404);
        }

        // Verificar si la orden está vencida
        $fechaActual = Carbon::now()->startOfDay();
        $fechaVencimiento = Carbon::parse($ultimaOrden->fecha_vencimiento)->startOfDay();

        if ($fechaActual->greaterThan($fechaVencimiento)) {
            // Verificar si la orden tiene algún pago realizado
            $pagosRealizados = Pago::where('id_orden', $ultimaOrden->id_orden)
                ->where('estado', 1)
                ->sum('monto');

            // Si NO tiene pagos, bloquear (orden vencida sin pagar)
            if ($pagosRealizados == 0) {
                return response()->json([
                    'success' => false,
                    'orden_vencida' => true,
                    'alumno' => [
                        'codigo_educando' => $alumno->codigo_educando,
                        'nombre_completo' => trim($alumno->apellido_paterno . ' ' . $alumno->apellido_materno . ' ' . $alumno->primer_nombre)
                    ],
                    'orden' => [
                        'codigo_orden' => $ultimaOrden->codigo_orden,
                        'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y')
                    ],
                    'message' => 'La orden de pago ' . $ultimaOrden->codigo_orden . ' venció el ' . $fechaVencimiento->format('d/m/Y') . ' y no tiene ningún pago registrado. Por favor, genere una nueva orden de pago actualizada.'
                ], 400);
            }
            
            // Si tiene pagos parciales, permitir continuar pagando
            // (El flujo continúa normalmente)
        }

        $idsDeudas = \DB::table('detalle_orden_pago')
            ->where('id_orden', $ultimaOrden->id_orden)
            ->pluck('id_deuda')
            ->toArray();

        if (empty($idsDeudas)) {
            return response()->json([
                'success' => false,
                'message' => 'La orden de pago no tiene deudas asociadas.'
            ], 404);
        }

        $deudas = Deuda::whereIn('id_deuda', $idsDeudas)->get();

        $deudasFormateadas = $deudas->map(function ($deuda) {
            $pagos = Pago::where('id_deuda', $deuda->id_deuda)->get();

            $pagoIds = $pagos->pluck('id_pago')->toArray();
            $detalles = collect();
            if (!empty($pagoIds)) {
                $detalles = DetallePago::whereIn('id_pago', $pagoIds)->get();
            }

            $montoPagado = $detalles->sum(function ($d) {
                return floatval($d->monto);
            });

            $detallesArr = $detalles->map(function ($d) {
                return [
                    'metodo_pago'      => $d->metodo_pago,
                    'nro_recibo'       => $d->nro_recibo,
                    'monto'            => floatval($d->monto),
                    'fecha_pago'       => $d->fecha_pago ? $d->fecha_pago->format('Y-m-d H:i:s') : null,
                    'observacion'      => $d->observacion,
                    'voucher_path'     => $d->voucher_path,
                    'estado_validacion'=> $d->estado_validacion,
                ];
            })->values();

            return [
                'id_deuda'      => $deuda->id_deuda,
                'periodo'       => $deuda->periodo,
                'monto_total'   => floatval($deuda->monto_total),
                'monto_pagado'  => round($montoPagado, 2),
                'concepto'      => $deuda->id_concepto ? ConceptoPago::find($deuda->id_concepto)->descripcion : 'Desconocido',
                'escala'        => $deuda->id_concepto ? ConceptoPago::find($deuda->id_concepto)->escala : 'Desconocido',
            ];
        });

        return response()->json([
            'success' => true,
            'alumno' => [
                'id_alumno' => $alumno->id_alumno,
                'codigo_educando' => $alumno->codigo_educando,
                'nombre_completo' => trim($alumno->apellido_paterno . ' ' . $alumno->apellido_materno . ' ' . $alumno->primer_nombre)
            ],
            'deudas' => $deudasFormateadas,
            'id_orden' => $ultimaOrden->id_orden,
        ]);
    }

    public function buscarPagosDeuda($id_deuda)
    {
        try {
            $detalles = \DB::select("
                SELECT 
                    dp.id_detalle,
                    dp.id_pago,
                    dp.nro_recibo,
                    dp.monto,
                    dp.metodo_pago,
                    dp.fecha_pago,
                    dp.observacion,
                    dp.estado,
                    dp.voucher_path,
                    dp.estado_validacion
                FROM detalle_pago dp
                INNER JOIN pagos p ON dp.id_pago = p.id_pago
                WHERE p.id_deuda = ? AND p.estado = 1
            ", [$id_deuda]);
            
            $pagos_existentes = [];
            $montoPagadoTotal = 0;
            
            foreach ($detalles as $detalle) {
                $pagos_existentes[] = [
                    'id_detalle' => $detalle->id_detalle,
                    'id_pago' => $detalle->id_pago,
                    'nro_recibo' => $detalle->nro_recibo,
                    'monto' => floatval($detalle->monto),
                    'metodo_pago' => $detalle->metodo_pago,
                    'fecha_pago' => $detalle->fecha_pago ? date('Y-m-d', strtotime($detalle->fecha_pago)) : null,
                    'observacion' => $detalle->observacion,
                    'estado' => $detalle->estado ? 'pagado' : 'pendiente',
                    'voucher_path' => $detalle->voucher_path,
                    'estado_validacion' => $detalle->estado_validacion ?? 'pendiente',
                ];
                
                $montoPagadoTotal += floatval($detalle->monto);
            }
            
            $deuda = Deuda::findOrFail($id_deuda);
            
            return response()->json([
                'success' => true,
                'deuda' => [
                    'id_deuda' => $deuda->id_deuda,
                    'monto_total' => floatval($deuda->monto_total),
                    'monto_pagado' => round($montoPagadoTotal, 2),
                ],
                'pagos_existentes' => $pagos_existentes,
                'puede_agregar_pago' => count($pagos_existentes) < 2,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarDetalle(Request $request, $idDetalle)
    {
        try {
            $detalle = DetallePago::findOrFail($idDetalle);
            
            // Solo permitir editar si estÃ¡ rechazado
            if ($detalle->estado_validacion !== 'rechazado') {
                return redirect()
                    ->back()
                    ->with('error', 'Solo se pueden editar detalles rechazados.');
            }

            // Validar campos
            $request->validate([
                'fecha_pago' => 'required|date',
                'monto' => 'required|numeric|min:0',
                'nro_recibo' => 'required|string|max:255',
                'nuevo_voucher' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
            ]);

            // Actualizar campos bÃ¡sicos
            $detalle->fecha_pago = $request->fecha_pago;
            $detalle->monto = $request->monto;
            $detalle->nro_recibo = $request->nro_recibo;

            // Manejar nuevo voucher si se subiÃ³
            if ($request->hasFile('nuevo_voucher')) {
                // Eliminar voucher anterior si existe
                if ($detalle->voucher_path) {
                    \Storage::disk('public')->delete($detalle->voucher_path);
                }

                // Guardar nuevo voucher
                $file = $request->file('nuevo_voucher');
                $filename = 'voucher_' . $idDetalle . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('vouchers', $filename, 'public');
                $detalle->voucher_path = $path;
                
                // Limpiar texto extraÃ­do anterior
                $detalle->voucher_texto = null;
            }

            // Resetear estado de validaciÃ³n a pendiente
            $detalle->estado_validacion = 'pendiente';
            $detalle->validado_por_ia = false;
            $detalle->porcentaje_confianza = null;
            $detalle->razon_ia = null;

            $detalle->save();

            // Actualizar monto total del pago
            $pago = Pago::findOrFail($detalle->id_pago);
            $montoTotal = DetallePago::where('id_pago', $detalle->id_pago)
                ->where('estado', 1)
                ->sum('monto');
            $pago->monto = $montoTotal;
            $pago->save();

            return redirect()
                ->route('pago_detalles', ['id' => $detalle->id_pago])
                ->with('success', 'Detalle actualizado correctamente. El estado ha sido cambiado a pendiente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar el detalle: ' . $e->getMessage());
        }
    }

    /**
     * Registrar pago a nivel de Orden de Pago
     * Distribuye automÃ¡ticamente el monto entre las deudas de la orden
     */
    public function registrarPagoOrden(Request $request)
    {
        $request->validate([
            'id_orden' => 'required|exists:ordenes_pago,id_orden',
            'tipo_pago' => 'required|in:orden_completa,orden_parcial',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'numero_operacion' => 'required|string',
            'fecha_pago' => 'required|date',
            'voucher' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'id_orden.required' => 'Debe seleccionar una orden de pago.',
            'id_orden.exists' => 'La orden de pago no existe.',
            'tipo_pago.required' => 'Debe especificar el tipo de pago.',
            'tipo_pago.in' => 'El tipo de pago no es vÃ¡lido.',
            'monto.required' => 'Ingrese el monto del pago.',
            'monto.numeric' => 'El monto debe ser numÃ©rico.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'metodo_pago.required' => 'Seleccione un mÃ©todo de pago.',
            'numero_operacion.required' => 'Ingrese el nÃºmero de operaciÃ³n.',
            'fecha_pago.required' => 'Ingrese la fecha de pago.',
            'voucher.mimes' => 'El voucher debe ser una imagen (JPG, PNG) o PDF.',
            'voucher.max' => 'El voucher no debe superar 2MB.',
        ]);

        DB::beginTransaction();

        try {
            \Log::info('=== INICIO registrarPagoOrden ===', [
                'id_orden' => $request->id_orden,
                'tipo_pago' => $request->tipo_pago,
                'monto' => $request->monto
            ]);
            
            $orden = OrdenPago::with('detalles.deuda')->findOrFail($request->id_orden);
            $montoPago = floatval($request->monto);
            $tipoPago = $request->tipo_pago;
            
            \Log::info('Orden cargada', [
                'id_orden' => $orden->id_orden,
                'cantidad_detalles' => $orden->detalles->count()
            ]);

            // Validar estado de la orden
            if ($orden->estado !== 'pendiente') {
                return back()->withErrors(['id_orden' => 'Esta orden ya ha sido pagada o estÃ¡ anulada.'])->withInput();
            }

            // Calcular el monto total de la orden
            $montoTotalOrden = $orden->detalles->sum(function($detalle) {
                return $detalle->deuda->monto_total;
            });

            // Calcular cuÃ¡nto se ha pagado de esta orden hasta ahora
            $pagosRealizados = Pago::where('id_orden', $orden->id_orden)
                ->where('estado', 1)
                ->sum('monto');

            $montoPendiente = $montoTotalOrden - $pagosRealizados;

            // Validaciones segÃºn el tipo de pago
            if ($tipoPago === 'orden_completa') {
                // Pago completo: el monto debe ser igual al pendiente
                if (abs($montoPago - $montoPendiente) > 0.01) {
                    return back()->withErrors([
                        'monto' => sprintf(
                            'Para pago completo, el monto debe ser exactamente S/ %.2f (monto pendiente de la orden).',
                            $montoPendiente
                        )
                    ])->withInput();
                }
            } else if ($tipoPago === 'orden_parcial') {
                // Pago parcial: validar que no exceda el mÃ¡ximo de 2 pagos parciales
                $cantidadPagosParciales = Pago::where('id_orden', $orden->id_orden)
                    ->where('tipo_pago', 'orden_parcial')
                    ->where('estado', 1)
                    ->count();

                if ($cantidadPagosParciales >= 2) {
                    return back()->withErrors([
                        'tipo_pago' => 'Ya se han realizado 2 pagos parciales para esta orden. No se permiten mÃ¡s pagos parciales.'
                    ])->withInput();
                }

                // Validar que el monto no exceda el pendiente
                if ($montoPago > $montoPendiente) {
                    return back()->withErrors([
                        'monto' => sprintf(
                            'El monto no puede exceder el monto pendiente de S/ %.2f',
                            $montoPendiente
                        )
                    ])->withInput();
                }

                // Validar que el monto no sea todo el pendiente (debe usar orden_completa)
                if (abs($montoPago - $montoPendiente) < 0.01) {
                    return back()->withErrors([
                        'monto' => 'Si va a pagar el monto completo, seleccione "Pago Completo" en lugar de "Pago Parcial".'
                    ])->withInput();
                }
            }

            // Subir voucher si existe
            $voucherPath = null;
            if ($request->hasFile('voucher')) {
                $file = $request->file('voucher');
                $filename = time() . '_' . $orden->id_orden . '.' . $file->getClientOriginalExtension();
                $voucherPath = $file->storeAs('vouchers/ordenes', $filename, 'public');
            }

            // Calcular nÃºmero de pago parcial
            $numeroPagoParcial = null;
            if ($tipoPago === 'orden_parcial') {
                $cantidadParciales = Pago::where('id_orden', $orden->id_orden)
                    ->where('tipo_pago', 'orden_parcial')
                    ->where('estado', 1)
                    ->count();
                $numeroPagoParcial = $cantidadParciales + 1;
            }

            // Crear el pago principal (a nivel de orden)
            $pago = Pago::create([
                'id_deuda' => null, // NULL porque es pago de orden
                'id_orden' => $orden->id_orden,
                'tipo_pago' => $tipoPago,
                'numero_pago_parcial' => $numeroPagoParcial,
                'fecha_pago' => $request->fecha_pago,
                'monto' => $montoPago,
                'observaciones' => $request->observaciones ?? 'Pago registrado a nivel de orden',
                'estado' => 1,
            ]);

            // Crear detalle de pago
            DetallePago::create([
                'id_pago' => $pago->id_pago,
                'metodo_pago' => $request->metodo_pago,
                'nro_recibo' => $request->numero_operacion,
                'monto' => $montoPago,
                'fecha_pago' => $request->fecha_pago,
                'voucher_path' => $voucherPath,
                'estado_validacion' => 'pendiente',
                'estado' => 1,
            ]);
            
            \Log::info('Pago y DetallePago creados', [
                'id_pago' => $pago->id_pago,
                'monto' => $montoPago
            ]);

            // DISTRIBUIR EL PAGO ENTRE LAS DEUDAS
            $montoRestante = $montoPago;
            
            \Log::info('Iniciando distribución de pago', [
                'id_pago' => $pago->id_pago,
                'monto_total' => $montoPago,
                'cantidad_detalles' => $orden->detalles->count()
            ]);
            
            foreach ($orden->detalles as $detalle) {
                if ($montoRestante <= 0) break;

                $deuda = $detalle->deuda;
                
                if (!$deuda) {
                    \Log::warning('Detalle sin deuda', ['id_detalle' => $detalle->id_detalle]);
                    continue;
                }
                
                $montoPendienteDeuda = $deuda->monto_total - ($deuda->monto_a_cuenta + $deuda->monto_adelantado);

                \Log::info('Procesando deuda', [
                    'id_deuda' => $deuda->id_deuda,
                    'monto_total' => $deuda->monto_total,
                    'monto_a_cuenta' => $deuda->monto_a_cuenta,
                    'monto_adelantado' => $deuda->monto_adelantado,
                    'pendiente' => $montoPendienteDeuda
                ]);

                if ($montoPendienteDeuda <= 0) {
                    \Log::info('Deuda ya pagada, saltando', ['id_deuda' => $deuda->id_deuda]);
                    continue; // Ya está pagada
                }

                // Calcular cuánto aplicar a esta deuda
                $montoAAplicar = min($montoRestante, $montoPendienteDeuda);
                
                \Log::info('Aplicando monto a deuda', [
                    'id_deuda' => $deuda->id_deuda,
                    'monto_aplicar' => $montoAAplicar
                ]);

                // Crear registro de distribución
                $distribucion = \App\Models\DistribucionPagoDeuda::create([
                    'id_pago' => $pago->id_pago,
                    'id_deuda' => $deuda->id_deuda,
                    'monto_aplicado' => $montoAAplicar,
                ]);
                
                \Log::info('Distribución creada', [
                    'id_distribucion' => $distribucion->id_distribucion ?? 'NULL',
                    'id_pago' => $pago->id_pago,
                    'id_deuda' => $deuda->id_deuda,
                    'monto_aplicado' => $montoAAplicar
                ]);

                // Actualizar la deuda
                // Si es adelanto, va a monto_adelantado, si no va a monto_a_cuenta
                $hoy = Carbon::now();
                $mesActual = $hoy->month;
                $anioActual = $hoy->year;
                
                $descripcion = $deuda->conceptoPago->descripcion ?? '';
                $partes = explode(' ', $descripcion);
                
                $meses = [
                    'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
                    'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
                    'SETIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
                ];
                
                $esAdelanto = false;
                if (count($partes) >= 2) {
                    $mesDeuda = $meses[$partes[0]] ?? 0;
                    $anioDeuda = intval($partes[1]);

                    if ($anioDeuda > $anioActual || ($anioDeuda == $anioActual && $mesDeuda > $mesActual)) {
                        $esAdelanto = true;
                    }
                }

                if ($esAdelanto) {
                    $deuda->monto_adelantado += $montoAAplicar;
                } else {
                    $deuda->monto_a_cuenta += $montoAAplicar;
                }
                
                $deuda->save();

                $montoRestante -= $montoAAplicar;
            }

            // Actualizar estado de la orden si estÃ¡ completamente pagada
            $totalPagadoOrden = Pago::where('id_orden', $orden->id_orden)
                ->where('estado', 1)
                ->sum('monto');

            if (abs($totalPagadoOrden - $montoTotalOrden) < 0.01) {
                $orden->estado = 'pagado';
                $orden->save();
            }

            DB::commit();

            // Si es petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pago registrado correctamente. El monto se distribuyó automáticamente entre las deudas de la orden.',
                    'pago_id' => $pago->id_pago
                ]);
            }

            return redirect()
                ->route('orden_pago_view')
                ->with('success', 'Pago registrado correctamente. El monto se distribuyó automáticamente entre las deudas de la orden.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si es petición AJAX, devolver JSON con error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el pago: ' . $e->getMessage(),
                    'errors' => ['error' => $e->getMessage()]
                ], 500);
            }
            
            return back()
                ->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Obtener informaciÃ³n de una orden para registrar pago
     */
    public function obtenerInfoOrden($id_orden)
    {
        try {
            $orden = OrdenPago::with(['detalles.deuda.conceptoPago', 'alumno'])->findOrFail($id_orden);

            if ($orden->estado !== 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta orden ya ha sido pagada o estÃ¡ anulada.'
                ], 400);
            }

            // Calcular el monto total de la orden
            $montoTotalOrden = $orden->detalles->sum(function($detalle) {
                return $detalle->deuda->monto_total;
            });

            // Calcular cuÃ¡nto se ha pagado
            $pagosRealizados = Pago::where('id_orden', $orden->id_orden)
                ->where('estado', 1)
                ->with('distribuciones')
                ->get();

            $montoPagado = $pagosRealizados->sum('monto');
            $montoPendiente = $montoTotalOrden - $montoPagado;

            // Contar pagos parciales
            $cantidadPagosParciales = $pagosRealizados->where('tipo_pago', 'orden_parcial')->count();
            $puedeHacerPagoParcial = $cantidadPagosParciales < 2;

            // Detalles de las deudas
            $deudas = $orden->detalles->map(function($detalle) {
                $deuda = $detalle->deuda;
                $totalPagado = $deuda->monto_a_cuenta + $deuda->monto_adelantado;
                
                return [
                    'id_deuda' => $deuda->id_deuda,
                    'concepto' => $deuda->conceptoPago->descripcion ?? 'Sin concepto',
                    'monto_total' => $deuda->monto_total,
                    'monto_pagado' => $totalPagado,
                    'monto_pendiente' => $deuda->monto_total - $totalPagado,
                ];
            });

            return response()->json([
                'success' => true,
                'orden' => [
                    'id_orden' => $orden->id_orden,
                    'codigo_orden' => $orden->codigo_orden,
                    'codigo_estudiante' => $orden->alumno->codigo_educando ?? '',
                    'nombre_estudiante' => trim(
                        ($orden->alumno->primer_nombre ?? '') . ' ' .
                        ($orden->alumno->otros_nombres ?? '') . ' ' .
                        ($orden->alumno->apellido_paterno ?? '') . ' ' .
                        ($orden->alumno->apellido_materno ?? '')
                    ),
                    'alumno' => trim(
                        ($orden->alumno->primer_nombre ?? '') . ' ' .
                        ($orden->alumno->otros_nombres ?? '') . ' ' .
                        ($orden->alumno->apellido_paterno ?? '') . ' ' .
                        ($orden->alumno->apellido_materno ?? '')
                    ),
                    'monto_total' => $montoTotalOrden,
                    'monto_pagado' => $montoPagado,
                    'monto_pendiente' => $montoPendiente,
                    'cantidad_deudas' => $orden->detalles->count(),
                    'puede_pago_parcial' => $puedeHacerPagoParcial,
                    'pagos_parciales_realizados' => $cantidadPagosParciales,
                    'deudas' => $deudas, // Añadido para compatibilidad
                ],
                'deudas' => $deudas,
                'pagos_realizados' => $pagosRealizados->map(function($pago) {
                    return [
                        'id_pago' => $pago->id_pago,
                        'tipo_pago' => $pago->tipo_pago,
                        'numero_pago_parcial' => $pago->numero_pago_parcial,
                        'monto' => $pago->monto,
                        'fecha_pago' => $pago->fecha_pago->format('d/m/Y'),
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener informaciÃ³n de la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar orden por código
     */
    public function buscarOrdenPorCodigo($codigo_orden)
    {
        try {
            // Decodificar el código por si viene codificado
            $codigo_orden = urldecode($codigo_orden);
            
            \Log::info('Buscando orden con código: ' . $codigo_orden);
            
            $orden = OrdenPago::where('codigo_orden', $codigo_orden)
                ->with(['detalles.deuda.conceptoPago', 'alumno'])
                ->first();

            if (!$orden) {
                \Log::warning('Orden no encontrada: ' . $codigo_orden);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una orden con el código: ' . $codigo_orden
                ], 404);
            }

            if ($orden->estado !== 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta orden ya ha sido pagada o está anulada.'
                ], 400);
            }

            // Calcular cuánto se ha pagado ANTES de verificar vencimiento
            $pagosRealizados = Pago::where('id_orden', $orden->id_orden)
                ->where('estado', 1)
                ->sum('monto');

            // Verificar si la orden está vencida
            $fechaActual = Carbon::now()->startOfDay();
            $fechaVencimiento = Carbon::parse($orden->fecha_vencimiento)->startOfDay();

            if ($fechaActual->greaterThan($fechaVencimiento)) {
                // Si NO tiene pagos, bloquear (orden vencida sin pagar)
                if ($pagosRealizados == 0) {
                    return response()->json([
                        'success' => false,
                        'orden_vencida' => true,
                        'alumno' => [
                            'codigo_educando' => $orden->alumno->codigo_educando ?? '-',
                            'nombre_completo' => trim(
                                ($orden->alumno->primer_nombre ?? '') . ' ' .
                                ($orden->alumno->otros_nombres ?? '') . ' ' .
                                ($orden->alumno->apellido_paterno ?? '') . ' ' .
                                ($orden->alumno->apellido_materno ?? '')
                            )
                        ],
                        'orden' => [
                            'codigo_orden' => $orden->codigo_orden,
                            'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y')
                        ],
                        'message' => 'La orden de pago ' . $orden->codigo_orden . ' venció el ' . $fechaVencimiento->format('d/m/Y') . ' y no tiene ningún pago registrado. Por favor, genere una nueva orden de pago actualizada.'
                    ], 400);
                }
                
                // Si tiene pagos parciales, permitir continuar pagando
                // (El flujo continúa normalmente)
            }

            // Calcular el monto total de la orden
            $montoTotalOrden = $orden->detalles->sum(function($detalle) {
                return $detalle->deuda->monto_total;
            });

            // Calcular cuánto se ha pagado
            $pagosRealizados = Pago::where('id_orden', $orden->id_orden)
                ->where('estado', 1)
                ->sum('monto');

            $montoPendiente = $montoTotalOrden - $pagosRealizados;

            // Detalles de las deudas
            $deudas = $orden->detalles->map(function($detalle) {
                $deuda = $detalle->deuda;
                
                // Calcular cuánto se ha pagado de esta deuda específica
                $montoPagado = $deuda->monto_a_cuenta + $deuda->monto_adelantado;
                
                return [
                    'id_deuda' => $deuda->id_deuda,
                    'concepto' => $deuda->conceptoPago->descripcion ?? 'Sin concepto',
                    'periodo' => $deuda->periodo,
                    'monto_total' => $deuda->monto_total,
                    'monto_pagado' => $montoPagado,
                    'monto_pendiente' => $deuda->monto_total - $montoPagado,
                ];
            });

            return response()->json([
                'success' => true,
                'orden' => [
                    'id_orden' => $orden->id_orden,
                    'codigo_orden' => $orden->codigo_orden,
                    'codigo_estudiante' => $orden->alumno->codigo_educando ?? '-',
                    'nombre_estudiante' => trim(
                        ($orden->alumno->primer_nombre ?? '') . ' ' .
                        ($orden->alumno->otros_nombres ?? '') . ' ' .
                        ($orden->alumno->apellido_paterno ?? '') . ' ' .
                        ($orden->alumno->apellido_materno ?? '')
                    ),
                    'monto_total' => $montoTotalOrden,
                    'monto_pagado' => $pagosRealizados,
                    'monto_pendiente' => $montoPendiente,
                    'deudas' => $deudas,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(
        Request $request,
        IExportRequestFactory $requestFactory,
        IExporterService $exporterService
    ) {
        try {

            $sqlColumns = ['id_pago', 'fecha_pago', 'monto', 'observaciones'];

            $params = RequestHelper::extractSearchParams($request);
            $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

            \Log::info('Exportando pagos', [
                'total_records' => $query->count(),
            ]);

            $query = $query->sortByDesc('fecha_pago');

            $data = $query->map(function ($pago) {

                if ($pago->id_deuda) {
                    $pago->load(['deuda.conceptoPago', 'deuda.alumno']);

                    $concepto = $pago->deuda->conceptoPago->descripcion ?? 'Sin concepto';
                    $alumno = trim(
                        ($pago->deuda->alumno->primer_nombre ?? '') . ' ' .
                        ($pago->deuda->alumno->apellido_paterno ?? '') . ' ' .
                        ($pago->deuda->alumno->apellido_materno ?? '')
                    );

                } elseif ($pago->id_orden) {
                    $pago->load(['ordenPago.alumno']);

                    $concepto = $pago->tipo_pago === 'orden_completa'
                        ? 'ORDEN COMPLETA'
                        : 'ORDEN PARCIAL';

                    $alumno = trim(
                        ($pago->ordenPago->alumno->primer_nombre ?? '') . ' ' .
                        ($pago->ordenPago->alumno->apellido_paterno ?? '') . ' ' .
                        ($pago->ordenPago->alumno->apellido_materno ?? '')
                    );
                } else {
                    $concepto = 'Sin concepto';
                    $alumno = 'Sin nombre';
                }

                $fechaPago = $pago->fecha_pago instanceof Carbon
                    ? $pago->fecha_pago->format('d/m/Y')
                    : Carbon::parse($pago->fecha_pago)->format('d/m/Y');

                return [
                    $pago->id_pago,
                    $concepto,
                    $alumno,
                    $fechaPago,
                    number_format($pago->monto, 2),
                    $pago->observaciones ?? 'Sin observaciones',
                ];
            });

            $title = 'Pagos';
            $headers = ['ID', 'Concepto de Pago', 'Estudiante', 'Fecha de Pago', 'Monto (S/)', 'Observaciones'];

            $exportRequest = $requestFactory->create(
                $title,
                $headers,
                $data->toArray(),
                [
                    'filename' => 'pagos_' . date('d_m_Y'),
                    'subtitle' => 'Listado de pagos del sistema',
                    'footer' => 'Sistema de Gestión Académica SIGMA - Generado automáticamente'
                ]
            );

            return $exporterService->exportAsResponse($request, $exportRequest);

        } catch (\Exception $e) {
            \Log::error('Error en exportación de pagos: ' . $e->getMessage());
            return back()->with('error', 'Error durante la exportación');
        }
    }

}

