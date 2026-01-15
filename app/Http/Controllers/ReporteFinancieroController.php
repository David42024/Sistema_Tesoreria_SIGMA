<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteFinancieroController extends Controller
{
    public function pagosPorMes(Request $request)
    {
        // Obtener todos los años sin importar el estado
        $anios = DB::table('pagos')
            ->selectRaw('DISTINCT YEAR(fecha_pago) as anio')
            ->whereNotNull('fecha_pago')
            ->orderByDesc('anio')
            ->pluck('anio');
        $anio = $request->input('anio', 'all');
        $anio = ($anio === 'all') ? null : $anio;
        $anio_actual = $anio;
        $conceptos = DB::table('conceptos_pago')->distinct()->get(['id_concepto', 'descripcion']);
        $tipos_pago = DB::table('pagos')->distinct()->pluck('tipo_pago')->filter()->values();
        $metodos_pago = DB::table('detalle_pago')->distinct()->whereNotNull('metodo_pago')->pluck('metodo_pago')->filter()->values();
        $mes = $request->input('mes');
        $tipo_pago = $request->input('tipo_pago');
        $metodo_pago = $request->input('metodo_pago');
        $fecha_inicio_input = $request->input('fecha_inicio');
        $fecha_fin_input = $request->input('fecha_fin');

        // Convertir fechas dd/mm/yyyy a Y-m-d
        $fecha_inicio = null;
        $fecha_fin = null;
        if ($fecha_inicio_input) {
            $partes = explode('/', $fecha_inicio_input);
            if (count($partes) === 3) {
                $fecha_inicio = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            }
        }
        if ($fecha_fin_input) {
            $partes = explode('/', $fecha_fin_input);
            if (count($partes) === 3) {
                $fecha_fin = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            }
        }

        $labels = [];
        $data = [];
        $query = DB::table('pagos')->where('estado', true);
        
        // Aplicar JOIN si hay filtro de método de pago
        if ($metodo_pago) {
            $query->join('detalle_pago', 'pagos.id_pago', '=', 'detalle_pago.id_pago')
                  ->where('detalle_pago.metodo_pago', $metodo_pago);
        }

        // Lógica adaptativa para el eje X
        $usarRango = ($fecha_inicio && $fecha_fin);
        if ($usarRango) {
            $inicio = date_create($fecha_inicio);
            $fin = date_create($fecha_fin);
            $diff = $inicio && $fin ? $inicio->diff($fin)->days : null;
            $mismoMes = $inicio && $fin && $inicio->format('Y-m') === $fin->format('Y-m');

            if ($diff !== null && $diff < 31 && $mismoMes) {
                // Mostrar por día
                $dias = (int)$fin->format('d');
                $labels = range(1, $dias);
                $query->selectRaw('DAY(pagos.fecha_pago) as dia, SUM(DISTINCT pagos.monto) as total')
                    ->whereDate('pagos.fecha_pago', '>=', $fecha_inicio)
                    ->whereDate('pagos.fecha_pago', '<=', $fecha_fin);
                if ($tipo_pago) {
                    $query->where('pagos.tipo_pago', $tipo_pago);
                }
                $pagos = $query->groupBy(DB::raw('DAY(pagos.fecha_pago)'))->orderBy('dia')->get();
                $data = array_fill(0, $dias, 0);
                foreach ($pagos as $pago) {
                    $data[$pago->dia - 1] = (float)$pago->total;
                }
            } else {
                // Mostrar por mes
                $labels = [];
                $meses = [];
                $period = new \DatePeriod($inicio, new \DateInterval('P1M'), $fin->modify('+1 day'));
                foreach ($period as $dt) {
                    $labels[] = ucfirst(strtolower($dt->format('F')));
                    $meses[] = (int)$dt->format('m');
                }
                $query->selectRaw('MONTH(pagos.fecha_pago) as mes, SUM(DISTINCT pagos.monto) as total')
                    ->whereDate('pagos.fecha_pago', '>=', $fecha_inicio)
                    ->whereDate('pagos.fecha_pago', '<=', $fecha_fin);
                if ($tipo_pago) {
                    $query->where('tipo_pago', $tipo_pago);
                }
                $pagos = $query->groupBy(DB::raw('MONTH(fecha_pago)'))->orderBy('mes')->get();
                $data = array_fill(0, count($labels), 0);
                foreach ($pagos as $pago) {
                    $idx = array_search((int)$pago->mes, $meses);
                    if ($idx !== false) {
                        $data[$idx] = (float)$pago->total;
                    }
                }
            }
        } else if ($mes) {
            // Si hay mes, mostrar días del mes
            $dias = cal_days_in_month(CAL_GREGORIAN, array_search($mes, ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'])+1, $anio ?: date('Y'));
            $labels = range(1, $dias);
            $query->selectRaw('DAY(pagos.fecha_pago) as dia, SUM(DISTINCT pagos.monto) as total');
            
            if ($anio) {
                $query->whereYear('pagos.fecha_pago', $anio);
            }
            
            $query->whereMonth('pagos.fecha_pago', array_search($mes, ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'])+1);
            
            if ($tipo_pago) {
                $query->where('pagos.tipo_pago', $tipo_pago);
            }
            $pagos = $query->groupBy(DB::raw('DAY(pagos.fecha_pago)'))->orderBy('dia')->get();
            $data = array_fill(0, $dias, 0);
            foreach ($pagos as $pago) {
                $data[$pago->dia - 1] = (float)$pago->total;
            }
        } else {
            // Si no hay mes ni rango, mostrar por años o meses
            if (!$anio) {
                // Mostrar todos los años
                $labels = $anios->toArray();
                $query->selectRaw('YEAR(pagos.fecha_pago) as anio, SUM(DISTINCT pagos.monto) as total');
                if ($tipo_pago) {
                    $query->where('pagos.tipo_pago', $tipo_pago);
                }
                $pagos = $query->groupBy(DB::raw('YEAR(pagos.fecha_pago)'))->orderBy('anio')->get();
                $data = array_fill(0, count($labels), 0);
                foreach ($pagos as $idx => $pago) {
                    $key = array_search($pago->anio, $labels);
                    if ($key !== false) {
                        $data[$key] = (float)$pago->total;
                    }
                }
            } else {
                // Mostrar meses del año seleccionado
                $labels = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                $query->selectRaw('MONTH(pagos.fecha_pago) as mes, SUM(DISTINCT pagos.monto) as total')
                    ->whereYear('pagos.fecha_pago', $anio);
                if ($tipo_pago) {
                    $query->where('pagos.tipo_pago', $tipo_pago);
                }
                $pagos = $query->groupBy(DB::raw('MONTH(pagos.fecha_pago)'))->orderBy('mes')->get();
                $data = array_fill(1, 12, 0);
                foreach ($pagos as $pago) {
                    $data[$pago->mes] = (float)$pago->total;
                }
            }
        }

        // ===== DATOS PARA GRÁFICO DE DONA (Distribución por Método de Pago) =====
        $metodosLabels = [];
        $metodosData = [];
        
        $distribucionMetodos = DB::table('detalle_pago')
            ->join('pagos', 'detalle_pago.id_pago', '=', 'pagos.id_pago')
            ->where('pagos.estado', true)
            ->whereNotNull('detalle_pago.metodo_pago');
        
        // Aplicar filtros de fecha
        if ($usarRango && $fecha_inicio && $fecha_fin) {
            $distribucionMetodos->whereDate('pagos.fecha_pago', '>=', $fecha_inicio)
                               ->whereDate('pagos.fecha_pago', '<=', $fecha_fin);
        } else {
            if ($anio) {
                $distribucionMetodos->whereYear('pagos.fecha_pago', $anio);
            }
            if ($mes) {
                $mesNum = array_search($mes, ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'])+1;
                $distribucionMetodos->whereMonth('pagos.fecha_pago', $mesNum);
            }
        }
        
        if ($tipo_pago) {
            $distribucionMetodos->where('pagos.tipo_pago', $tipo_pago);
        }
        
        $resultadosMetodos = $distribucionMetodos
            ->selectRaw('detalle_pago.metodo_pago, SUM(CAST(detalle_pago.monto AS DECIMAL(10,2))) as total')
            ->groupBy('detalle_pago.metodo_pago')
            ->get();
        
        if ($resultadosMetodos->count() > 0) {
            $metodosLabels = $resultadosMetodos->pluck('metodo_pago')->map(function($m) { 
                return ucfirst(str_replace('_', ' ', $m)); 
            })->toArray();
            $metodosData = $resultadosMetodos->pluck('total')->map(function($t) { 
                return (float)$t; 
            })->toArray();
        }
        
        // ===== DATOS PARA GRÁFICO DE BARRAS APILADAS (Por Tipo de Pago) =====
        $pagosPorTipo = [];
        
        // Simplificar: solo crear datasets si hay datos
        if (count($labels) > 0 && count($data) > 0) {
            foreach ($tipos_pago as $tipo) {
                $queryTipo = DB::table('pagos')->where('pagos.estado', true)->where('pagos.tipo_pago', $tipo);
                
                // Aplicar filtros de fecha
                if ($usarRango && $fecha_inicio && $fecha_fin) {
                    $queryTipo->whereDate('fecha_pago', '>=', $fecha_inicio)
                             ->whereDate('fecha_pago', '<=', $fecha_fin);
                } else {
                    if ($anio) {
                        $queryTipo->whereYear('fecha_pago', $anio);
                    }
                    if ($mes) {
                        $mesNum = array_search($mes, ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'])+1;
                        $queryTipo->whereMonth('fecha_pago', $mesNum);
                    }
                }
                
                if ($metodo_pago) {
                    $queryTipo->join('detalle_pago', 'pagos.id_pago', '=', 'detalle_pago.id_pago')
                              ->where('detalle_pago.metodo_pago', $metodo_pago);
                }
                
                // Obtener suma total para este tipo
                $total = $queryTipo->sum('pagos.monto');
                
                if ($total > 0) {
                    // Distribuir proporcionalmente en el array según los labels
                    $pagosPorTipo[ucfirst(str_replace('_', ' ', $tipo))] = array_fill(0, count($labels), 0);
                    // Poner el total en el primer elemento para simplificar
                    if (count($labels) > 0) {
                        $pagosPorTipo[ucfirst(str_replace('_', ' ', $tipo))][0] = (float)$total;
                    }
                }
            }
        }
        
        // ===== DATOS PARA GRÁFICO DE LÍNEA DUAL (Pagos vs Deudas) =====
        $deudasData = array_fill(0, count($labels), 0);
        
        if (count($labels) > 0) {
            $queryDeudas = DB::table('deudas')->where('deudas.estado', true);
            
            // Aplicar filtros de fecha
            if ($usarRango && $fecha_inicio && $fecha_fin) {
                $queryDeudas->whereDate('created_at', '>=', $fecha_inicio)
                           ->whereDate('created_at', '<=', $fecha_fin);
            } else {
                if ($anio) {
                    $queryDeudas->whereYear('created_at', $anio);
                }
                if ($mes) {
                    $mesNum = array_search($mes, ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'])+1;
                    $queryDeudas->whereMonth('created_at', $mesNum);
                }
            }
            
            $totalDeudas = $queryDeudas->sum('monto_total');
            
            if ($totalDeudas > 0 && count($labels) > 0) {
                // Distribuir el total en el primer elemento
                $deudasData[0] = (float)$totalDeudas;
            }
        }

        // ===== DATOS PARA GRÁFICO DE EVOLUCIÓN ACUMULADA =====
        $acumuladoData = [];
        $acumulado = 0;
        foreach ($data as $monto) {
            $acumulado += $monto;
            $acumuladoData[] = $acumulado;
        }

        // ===== DATOS PARA RESUMEN GENERAL (Total Pagado, Deudas, Balance) =====
        $totalPagado = array_sum($data);
        $totalDeudas = array_sum($deudasData);
        $balance = $totalPagado - $totalDeudas;
        
        $resumenLabels = ['Total Pagado', 'Total Deudas', 'Balance'];
        $resumenData = [$totalPagado, $totalDeudas, abs($balance)];
        $resumenColors = [
            'rgba(16, 185, 129, 0.7)', // Verde para pagado
            'rgba(239, 68, 68, 0.7)',   // Rojo para deudas
            $balance >= 0 ? 'rgba(59, 130, 246, 0.7)' : 'rgba(245, 158, 11, 0.7)' // Azul si positivo, naranja si negativo
        ];

        return view('gestiones.reportes.financieros', [
            'anios' => $anios,
            'anio_actual' => $anio_actual,
            'conceptos' => $conceptos,
            'tipos_pago' => $tipos_pago,
            'metodos_pago' => $metodos_pago,
            'labels' => array_values($labels),
            'pagosPorMes' => array_values($data),
            // Datos para gráfico de dona
            'metodosLabels' => $metodosLabels,
            'metodosData' => $metodosData,
            // Datos para gráfico de barras apiladas
            'pagosPorTipo' => $pagosPorTipo,
            // Datos para gráfico de línea dual
            'deudasData' => array_values($deudasData),
            // Datos para gráfico de evolución acumulada
            'acumuladoData' => array_values($acumuladoData),
            // Datos para resumen general
            'resumenLabels' => $resumenLabels,
            'resumenData' => $resumenData,
            'resumenColors' => $resumenColors
        ]);
    }
}
