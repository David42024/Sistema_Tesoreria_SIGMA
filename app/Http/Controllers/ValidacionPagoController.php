<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\DetallePago;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\OCRService;
use App\Services\GroqService;

class ValidacionPagoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow)
    {
        if (!isset($search)) {
            $query = Pago::where('estado', '=', '1')->paginate($maxEntriesShow);
        } else {
            $query = Pago::where('estado', '=', '1')
                ->whereAny($sqlColumns, 'LIKE', "%{$search}%")
                ->paginate($maxEntriesShow);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $sqlColumns = ['id_pago', 'id_deuda', 'fecha_pago', 'monto', 'observaciones'];
        $resource = 'administrativa';

        $maxEntriesShow = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

        // Obtener estado de IA desde Cache
        $ia_activa = Cache::get('sistema_ia_activa', false);

        if (!is_numeric($paginaActual) || $paginaActual <= 0) $paginaActual = 1;
        if (!is_numeric($maxEntriesShow) || $maxEntriesShow <= 0) $maxEntriesShow = 10;

        $query = ValidacionPagoController::doSearch($sqlColumns, $search, $maxEntriesShow);

        if ($paginaActual > $query->lastPage()) {
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $query = ValidacionPagoController::doSearch($sqlColumns, $search, $maxEntriesShow);
        }

        // Obtener estadísticas de validación basadas en DetallesPago
        $totalDetalles = DetallePago::whereHas('pago', function($query) {
            $query->where('estado', '=', '1');
        })->where('estado', 1)->count();
        
        $detallesValidados = DetallePago::whereHas('pago', function($query) {
            $query->where('estado', '=', '1');
        })->where('estado', 1)
          ->where('estado_validacion', 'validado')
          ->count();
        
        $detallesPendientes = DetallePago::whereHas('pago', function($query) {
            $query->where('estado', '=', '1');
        })->where('estado', 1)
          ->where(function($query) {
              $query->where('estado_validacion', 'pendiente')
                    ->orWhereNull('estado_validacion');
          })
          ->count();
        
        $detallesRechazados = DetallePago::whereHas('pago', function($query) {
            $query->where('estado', '=', '1');
        })->where('estado', 1)
          ->where('estado_validacion', 'rechazado')
          ->count();

        $data = [
            'titulo' => 'Validación de Pagos',
            'columnas' => [
                'ID',
                'Concepto de Pago',
                'Nombre del Alumno',
                'Fecha de Pago',
                'Monto',
                'Comprobantes',
                'Observaciones'
            ],
            'filas' => [],
            'showing' => $maxEntriesShow,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $query->lastPage(),
            'resource' => $resource,
            'view' => 'validacion_pago_view',
            'ia_activa' => $ia_activa,
            'total_detalles' => $totalDetalles,
            'detalles_validados' => $detallesValidados,
            'detalles_pendientes' => $detallesPendientes,
            'detalles_rechazados' => $detallesRechazados,
        ];

        // Procesar cada pago y construir las filas
        foreach ($query as $pago) {
            // Cargar relaciones según el tipo de pago
            if ($pago->id_deuda) {
                // Pago de deuda individual
                $pago = Pago::with(['deuda.conceptoPago', 'deuda.alumno'])->findOrFail($pago->id_pago);
                
                $concepto = $pago->deuda->conceptoPago->descripcion ?? 'Sin concepto';
                $alumno = trim(
                    ($pago->deuda->alumno->primer_nombre ?? '') . ' ' . 
                    ($pago->deuda->alumno->otros_nombres ?? '') . ' ' . 
                    ($pago->deuda->alumno->apellido_paterno ?? '') . ' ' . 
                    ($pago->deuda->alumno->apellido_materno ?? '')
                ) ?: 'Sin nombre';
                
                $montoAPagar = $pago->deuda->monto_total ?? 0;
                
            } else if ($pago->id_orden) {
                // Pago de orden completa/parcial
                $pago = Pago::with(['ordenPago.alumno'])->findOrFail($pago->id_pago);
                
                // Para órdenes, mostrar el tipo de pago como concepto
                $tipoPagoLabel = match($pago->tipo_pago) {
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
                
                $montoAPagar = $pago->ordenPago->monto_total ?? 0;
                
            } else {
                // Caso inesperado - sin deuda ni orden
                $concepto = 'Sin concepto';
                $alumno = 'Sin nombre';
                $montoAPagar = 0;
            }
            
            $fechaPago = $pago->fecha_pago instanceof Carbon
                ? $pago->fecha_pago->format('d/m/Y')
                : Carbon::parse($pago->fecha_pago)->format('d/m/Y');

            $observaciones = $pago->observaciones ?? 'Sin Observaciones';

            $cantidadDetalles = $pago->detallesPago()
                ->where('estado', 1)
                ->count();
            
            $tieneDetallesRechazados = $pago->detallesPago()
                ->where('estado_validacion', 'rechazado')
                ->exists();
            
            // Analizar DetallesPago para mostrar progreso de validación
            $detallesPago = $pago->detallesPago()->where('estado', 1)->get();
            $totalDetalles = $detallesPago->count();
            
            $detallesValidados = $detallesPago->where('estado_validacion', 'validado')->count();
            $detallesRechazados = $detallesPago->where('estado_validacion', 'rechazado')->count();
            $detallesPendientes = $detallesPago->filter(function($detalle) {
                return $detalle->estado_validacion === 'pendiente' || is_null($detalle->estado_validacion);
            })->count();
            
            // Construir mensaje de estado basado en detalles
            if ($totalDetalles === 0) {
                $estadoValidacion = 'Sin comprobantes|0|0|0|0';
            } else {
                // Formato: EstadoPrincipal|Total|Validados|Rechazados|Pendientes
                if ($detallesValidados === $totalDetalles) {
                    $estadoValidacion = "Completo|{$totalDetalles}|{$detallesValidados}|{$detallesRechazados}|{$detallesPendientes}";
                } elseif ($detallesRechazados > 0) {
                    $estadoValidacion = "Rechazado|{$totalDetalles}|{$detallesValidados}|{$detallesRechazados}|{$detallesPendientes}";
                } else {
                    $estadoValidacion = "Pendiente|{$totalDetalles}|{$detallesValidados}|{$detallesRechazados}|{$detallesPendientes}";
                }
            }

            array_push($data['filas'], [
                $pago->id_pago,
                $concepto,
                $alumno,
                $fechaPago,
                number_format($pago->monto, 2),
                $estadoValidacion,
                $observaciones,
                $cantidadDetalles,
                $montoAPagar,
                $tieneDetallesRechazados 
            ]);
        }

        return view('gestiones.validacion_pago.index', compact('data'));
    }

    public function toggleIA(Request $request)
    {
        $ia_activa = Cache::get('sistema_ia_activa', false);
        
        $nuevo_estado = !$ia_activa;
        Cache::forever('sistema_ia_activa', $nuevo_estado);

        return response()->json([
            'success' => true,
            'ia_activa' => $nuevo_estado,
            'message' => $nuevo_estado ? 'IA activada' : 'IA desactivada'
        ]);
    }

    public function validar($id_pago)
    {
        $pago = Pago::with(['detallesPago'])->findOrFail($id_pago);
        $ia_activa = Cache::get('sistema_ia_activa', false);

        // Obtener datos según el tipo de pago
        if ($pago->id_deuda) {
            // Pago de deuda individual
            $pago->load(['deuda.conceptoPago', 'deuda.alumno']);
            
            $concepto = $pago->deuda->conceptoPago->descripcion ?? 'Sin concepto';
            $alumnoObj = $pago->deuda->alumno;
            
        } else if ($pago->id_orden) {
            // Pago de orden completa/parcial
            $pago->load(['ordenPago.alumno']);
            
            // Para órdenes, mostrar el tipo de pago como concepto
            $concepto = match($pago->tipo_pago) {
                'orden_completa' => 'ORDEN COMPLETA',
                'orden_parcial' => 'ORDEN PARCIAL',
                default => 'PAGO DE ORDEN'
            };
            
            $alumnoObj = $pago->ordenPago->alumno;
            
        } else {
            // Caso inesperado
            $concepto = 'Sin concepto';
            $alumnoObj = null;
        }
        
        $alumno = $alumnoObj ? trim(
            ($alumnoObj->primer_nombre ?? '') . ' ' . 
            ($alumnoObj->otros_nombres ?? '') . ' ' . 
            ($alumnoObj->apellido_paterno ?? '') . ' ' . 
            ($alumnoObj->apellido_materno ?? '')
        ) : 'Sin nombre';

        $data = [
            'titulo' => 'Validar Pago #' . $pago->id_pago,
            'pago' => $pago,
            'concepto' => $concepto,
            'alumno' => $alumno,
            'alumno_obj' => $alumnoObj,
            'ia_activa' => $ia_activa,
        ];

        return view('gestiones.validacion_pago.validar', compact('data'));
    }

    public function validarDetalle(Request $request, $id_detalle)
    {
        try {
            // Validar que el detalle existe
            $detalle = DetallePago::findOrFail($id_detalle);
            
            // Validar el input
            $request->validate([
                'accion' => 'required|in:validado,rechazado'
            ]);
            
            $accion = $request->input('accion');
            
            // Actualizar el estado de validación
            $detalle->estado_validacion = $accion;
            $detalle->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Detalle ' . ($accion === 'validado' ? 'validado' : 'rechazado') . ' correctamente',
                'nuevo_estado' => $accion
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detalle de pago no encontrado'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la validación: ' . $e->getMessage()
            ], 500);
        }
    }

    public function guardarValidaciones(Request $request, $id_pago)
    {
        try {
            $validaciones = $request->input('validaciones');
            $actualizados = 0;
            
            // Actualizar cada detalle de pago
            foreach ($validaciones as $idDetalle => $accion) {
                // Solo actualizar si el estado es validado o rechazado
                if (in_array($accion, ['validado', 'rechazado'])) {
                    $detalle = DetallePago::where('id_detalle', $idDetalle)
                        ->where('id_pago', $id_pago)
                        ->first();
                    
                    if ($detalle) {
                        $detalle->estado_validacion = $accion;
                        $detalle->save();
                        $actualizados++;
                    }
                }
            }
            
            return redirect()->route('validacion_pago_view')
                ->with('success', "Se validaron correctamente {$actualizados} detalle(s) de pago");
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al guardar las validaciones: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function procesarIA(Request $request, $id_pago)
    {
        try {
            $request->validate(['id_detalle' => 'required|integer']);
            $idDetalle = $request->input('id_detalle');
            
            $detalle = DetallePago::where('id_detalle', $idDetalle)->where('id_pago', $id_pago)->first();
            
            if (!$detalle || !$detalle->voucher_path) {
                return response()->json(['success' => false, 'message' => 'Voucher no encontrado'], 404);
            }

            $resultado = $this->analizarVoucherConGroq($detalle);
            
            $detalle->validado_por_ia = true;
            $detalle->porcentaje_confianza = $resultado['porcentaje'];
            $detalle->razon_ia = $resultado['razon'] ?? 'Sin razón especificada';
            $detalle->save();

            return response()->json([
                'success' => true, 
                'porcentaje' => $resultado['porcentaje'], 
                'recomendacion' => $resultado['recomendacion'],
                'razon' => $resultado['razon'] ?? 'Sin razón especificada',
                'texto_extraido' => $resultado['texto'] ?? ''
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function analizarVoucherConGroq($detalle)
    {
        try {
            $rutaArchivo = storage_path('app/public/' . $detalle->voucher_path);
            if (!file_exists($rutaArchivo)) return ['porcentaje' => 0, 'recomendacion' => 'rechazado', 'texto' => ''];

            // Si ya existe texto extraído previamente, usarlo; sino, extraer con OCR
            $textoVoucher = $detalle->voucher_texto;
            
            if (empty($textoVoucher)) {
                $ocrService = new OCRService();
                $resultadoOCR = $ocrService->extraerTexto($rutaArchivo);
                
                if (!$resultadoOCR['success'] || strlen($resultadoOCR['texto']) < 10) {
                    return ['porcentaje' => 20, 'recomendacion' => 'rechazado', 'texto' => ''];
                }
                
                $textoVoucher = $resultadoOCR['texto'];
                
                // Guardar el texto extraído en la base de datos
                $detalle->voucher_texto = $textoVoucher;
                $detalle->save();
            }

            // Buscar transacción en pasarela para verificar autenticidad
            $transaccionPasarela = null;
            if ($detalle->nro_recibo) {
                $transaccionPasarela = \App\Models\TransaccionPasarela::where('numero_operacion', $detalle->nro_recibo)
                    ->where('metodo_pago', $detalle->metodo_pago)
                    ->first();
            }

            // Preparar datos adicionales para el análisis
            $datosAdicionales = [
                'numero_operacion' => $detalle->nro_recibo,
                'metodo_pago' => $detalle->metodo_pago,
                'tiene_transaccion_pasarela' => !is_null($transaccionPasarela),
            ];
            
            if ($transaccionPasarela) {
                $datosAdicionales['transaccion_monto'] = floatval($transaccionPasarela->monto);
                $datosAdicionales['transaccion_fecha'] = Carbon::parse($transaccionPasarela->fecha_transaccion)->format('d/m/Y');
            }

            $groqService = new GroqService();
            $resultado = $groqService->analizarVoucher(
                $textoVoucher, 
                floatval($detalle->monto), 
                Carbon::parse($detalle->fecha_pago)->format('d/m/Y'),
                $datosAdicionales
            );
            $resultado['texto'] = $textoVoucher;
            
            return $resultado;
        } catch (\Exception $e) {
            \Log::error('Error en analizarVoucherConGroq: ' . $e->getMessage());
            return ['porcentaje' => 30, 'recomendacion' => 'pendiente', 'texto' => ''];
        }
    }
}
