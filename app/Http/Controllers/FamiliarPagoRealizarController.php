<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\OrdenPago;
use App\Models\DetalleOrdenPago;
use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\DistribucionPagoDeuda;
use App\Models\TransaccionPasarela;
use App\Models\ConceptoPago;
use App\Helpers\Home\Familiar\FamiliarHeaderComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
use App\Helpers\CRUDTablePage;
use App\Helpers\Tables\ViewBasedComponent;
use App\Http\Controllers\Home\Utils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FamiliarPagoRealizarController extends Controller
{
    /**
     * Mostrar formulario de selección de deudas y métodos de pago
     */
    public function index(Request $request)
    {
        $alumno = $request->session()->get('alumno');

        if ($alumno == null) {
            return redirect(route('principal'));
        }

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Realizar Pago")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        // Obtener deudas pendientes del alumno (excluyendo las validadas)
        $deudas = $this->obtenerDeudasPendientes($alumno->id_alumno);

        $data = compact('alumno', 'deudas');
        $content = new ViewBasedComponent('homev2.familiares.realizar_pago.index', $data);
        $page->content($content);

        return $page->render();
    }

    /**
     * Procesar la selección de deudas y crear la orden de pago
     */
    public function procesarSeleccion(Request $request)
    {
        $alumno = $request->session()->get('alumno');

        if ($alumno == null) {
            return redirect(route('principal'));
        }

        $request->validate([
            'deudas_seleccionadas' => 'required|array|min:1',
            'deudas_seleccionadas.*' => 'exists:deudas,id_deuda',
        ], [
            'deudas_seleccionadas.required' => 'Debe seleccionar al menos una deuda.',
            'deudas_seleccionadas.min' => 'Debe seleccionar al menos una deuda.',
        ]);

        $deudasSeleccionadas = Deuda::with('conceptoPago')
            ->whereIn('id_deuda', $request->deudas_seleccionadas)
            ->where('id_alumno', $alumno->id_alumno)
            ->where('estado', true)
            ->get();

        if ($deudasSeleccionadas->isEmpty()) {
            return back()->with('error', 'No se encontraron deudas válidas.');
        }

        // Guardar solo los IDs de las deudas seleccionadas en sesión
        $request->session()->put('deudas_ids_seleccionadas', $deudasSeleccionadas->pluck('id_deuda')->toArray());

        return redirect()->route('familiar_pago_pago_realizar_metodo');
    }

    /**
     * Mostrar página de selección de método de pago
     */
    public function mostrarMetodos(Request $request)
    {
        $alumno = $request->session()->get('alumno');
        $deudasIds = $request->session()->get('deudas_ids_seleccionadas');

        if ($alumno == null || $deudasIds == null) {
            return redirect()->route('familiar_pago_pago_realizar_index');
        }

        // Recargar deudas desde la base de datos
        $deudasSeleccionadas = Deuda::with('conceptoPago')
            ->whereIn('id_deuda', $deudasIds)
            ->where('estado', true)
            ->get();

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Seleccionar Método de Pago")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        // Calcular total
        $montoTotal = $deudasSeleccionadas->sum('monto_total');

        $data = compact('alumno', 'deudasSeleccionadas', 'montoTotal');
        $content = new ViewBasedComponent('homev2.familiares.realizar_pago.seleccionar_metodo', $data);
        $page->content($content);

        return $page->render();
    }

    /**
     * Crear orden de pago y mostrar formulario del método seleccionado
     */
    public function mostrarFormularioMetodo(Request $request, $metodo)
    {
        $alumno = $request->session()->get('alumno');
        $deudasIds = $request->session()->get('deudas_ids_seleccionadas');

        if ($alumno == null || $deudasIds == null) {
            return redirect()->route('familiar_pago_pago_realizar_index');
        }

        // Recargar deudas desde la base de datos
        $deudasSeleccionadas = Deuda::with('conceptoPago')
            ->whereIn('id_deuda', $deudasIds)
            ->where('estado', true)
            ->get();

        // Validar método
        $metodos_validos = ['yape', 'plin', 'transferencia', 'tarjeta', 'paypal'];
        if (!in_array($metodo, $metodos_validos)) {
            return back()->with('error', 'Método de pago no válido.');
        }

        try {
            DB::beginTransaction();

            // Log antes de crear orden
            \Log::info('=== ANTES DE CREAR ORDEN ===');
            \Log::info('IDs de deudas desde sesión: ' . json_encode($deudasIds));
            \Log::info('Deudas recargadas desde BD: ' . $deudasSeleccionadas->count());
            \Log::info('IDs recargados: ' . $deudasSeleccionadas->pluck('id_deuda')->implode(', '));
            \Log::info('¿Colección vacía?: ' . ($deudasSeleccionadas->isEmpty() ? 'SÍ' : 'NO'));

            // Crear la orden de pago
            $orden = $this->crearOrdenPago($alumno, $deudasSeleccionadas);

            // Guardar la orden en sesión
            $request->session()->put('orden_pago_creada', $orden);

            DB::commit();

            $header = Utils::crearHeaderConAlumnos($request);

            $page = CRUDTablePage::new()
                ->title("Pagar con " . ucfirst($metodo))
                ->header($header)
                ->sidebar(new FamiliarSidebarComponent());

            // Datos para la vista
            $data = [
                'alumno' => $alumno,
                'orden' => $orden,
                'metodo' => $metodo,
                'saldoPendiente' => $orden->monto_total,
            ];

            // Datos específicos según el método
            if ($metodo == 'transferencia') {
                $data['bancos'] = [
                    'BCP' => 'Banco de Crédito del Perú',
                    'BBVA' => 'BBVA Continental',
                    'NACION' => 'Banco de la Nación',
                    'INTERBANK' => 'Interbank',
                    'SCOTIABANK' => 'Scotiabank',
                ];
                $data['cuenta_colegio'] = [
                    'banco' => 'BCP',
                    'numero' => '191-2345678-0-95',
                    'cci' => '00219100234567809512',
                    'titular' => 'Institución Educativa SIGMA',
                ];
            }

            $content = new ViewBasedComponent("homev2.familiares.realizar_pago.metodos.{$metodo}", $data);
            $page->content($content);

            return $page->render();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la orden de pago: ' . $e->getMessage());
        }
    }

    /**
     * Procesar el pago y registrar en todas las tablas
     */
    public function procesarPago(Request $request)
    {
        $alumno = $request->session()->get('alumno');
        $orden = $request->session()->get('orden_pago_creada');

        if ($alumno == null || $orden == null) {
            return redirect()->route('familiar_pago_pago_realizar_index');
        }

        // Validar según el método de pago
        $metodo = $request->input('metodo_pago');

        $rules = [
            'metodo_pago' => 'required|in:yape,plin,transferencia,tarjeta,paypal',
            'monto' => 'required|numeric|min:0.01',
        ];

        // Reglas específicas por método
        if ($metodo == 'yape' || $metodo == 'plin') {
            $rules['numero_celular'] = 'required|digits:9';
        } elseif ($metodo == 'transferencia') {
            $rules['banco_origen'] = 'required';
            $rules['numero_operacion'] = 'required';
        } elseif ($metodo == 'tarjeta') {
            $rules['numero_tarjeta'] = 'required';
            $rules['nombre_titular'] = 'required';
            $rules['fecha_vencimiento'] = 'required';
            $rules['cvv'] = 'required|digits:3';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // 1. Registrar en transacciones_pasarela
            $transaccion = $this->registrarTransaccion($request, $orden);

            // 2. Registrar en pagos
            $pago = $this->registrarPago($orden, $request->monto);

            // 3. Registrar en detalle_pago
            $detallePago = $this->registrarDetallePago($pago, $request, $metodo);

            // 4. Registrar en distribucion_pago_deuda
            $this->registrarDistribucion($pago, $orden);

            // Actualizar id_pago_generado en transaccion
            $transaccion->id_pago_generado = $pago->id_pago;
            $transaccion->save();

            DB::commit();

            // Limpiar sesión
            $request->session()->forget(['deudas_ids_seleccionadas', 'orden_pago_creada']);

            // Redirigir a página de éxito
            return redirect()->route('familiar_pago_pago_realizar_exito', ['transaccion_id' => $transaccion->id_transaccion]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar página de éxito
     */
    public function mostrarExito(Request $request, $transaccion_id)
    {
        $alumno = $request->session()->get('alumno');

        if ($alumno == null) {
            return redirect(route('principal'));
        }

        $transaccion = TransaccionPasarela::with(['orden.alumno', 'orden.detalles.conceptoPago'])
            ->find($transaccion_id);

        if (!$transaccion) {
            return redirect()->route('familiar_pago_pago_realizar_index')->with('error', 'Transacción no encontrada.');
        }

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Pago Exitoso")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        $data = compact('alumno', 'transaccion');
        $content = new ViewBasedComponent('homev2.familiares.realizar_pago.exito', $data);
        $page->content($content);

        return $page->render();
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Obtener deudas pendientes del alumno (excluyendo las validadas)
     */
    private function obtenerDeudasPendientes($id_alumno)
    {
        // Obtener IDs de pagos validados
        $idPagosValidados = DetallePago::where('estado_validacion', '=', 'validado')
            ->where('estado', '=', true)
            ->pluck('id_pago')
            ->unique()
            ->toArray();

        // Obtener IDs de deudas con pagos validados
        $idDeudasValidadas = [];
        if (!empty($idPagosValidados)) {
            $idDeudasValidadas = DistribucionPagoDeuda::whereIn('id_pago', $idPagosValidados)
                ->pluck('id_deuda')
                ->unique()
                ->toArray();
        }

        // Consultar deudas excluyendo las validadas
        $query = Deuda::where('id_alumno', $id_alumno)
            ->where('estado', true)
            ->with('conceptoPago');

        if (!empty($idDeudasValidadas)) {
            $query->whereNotIn('id_deuda', $idDeudasValidadas);
        }

        return $query->orderBy('fecha_limite', 'asc')->get();
    }

    /**
     * Crear orden de pago
     */
    private function crearOrdenPago($alumno, $deudas)
    {
        // Obtener matrícula activa
        $matricula = $alumno->matriculas()
            ->where('estado', true)
            ->orderBy('id_periodo_academico', 'desc')
            ->first();

        if (!$matricula) {
            throw new \Exception('El alumno no tiene una matrícula activa.');
        }

        // Calcular monto total
        $montoTotal = $deudas->sum('monto_total');

        // Generar código de orden
        $anio = Carbon::now()->year;
        $ultimaOrden = OrdenPago::whereYear('created_at', $anio)->orderBy('id_orden', 'desc')->first();
        $numeroOrden = $ultimaOrden ? (intval(substr($ultimaOrden->codigo_orden, -4)) + 1) : 1;
        $codigoOrden = 'OP-' . $anio . '-' . str_pad($numeroOrden, 4, '0', STR_PAD_LEFT);

        // Crear orden
        $fechaOrden = Carbon::now();
        $fechaVencimiento = Carbon::now()->addDays(3); // 3 días después

        $orden = OrdenPago::create([
            'codigo_orden' => $codigoOrden,
            'id_alumno' => $alumno->id_alumno,
            'id_matricula' => $matricula->id_matricula,
            'monto_total' => $montoTotal,
            'numero_cuenta' => '1234567890',
            'fecha_orden_pago' => $fechaOrden,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado' => true,
            'observaciones' => NULL,
        ]);

        // Crear detalles de orden
        \Log::info('=== INICIANDO CREACIÓN DE DETALLES ===');
        \Log::info('ID Orden creada: ' . $orden->id_orden);
        \Log::info('Tipo de variable $deudas: ' . get_class($deudas));
        \Log::info('Cantidad de deudas: ' . count($deudas));
        \Log::info('¿Deudas está vacío?: ' . ($deudas->isEmpty() ? 'SÍ' : 'NO'));
        \Log::info('IDs de deudas: ' . $deudas->pluck('id_deuda')->implode(', '));

        if ($deudas->isEmpty()) {
            \Log::error('❌ PROBLEMA: Collection de deudas está VACÍA');
            throw new \Exception('No hay deudas para procesar en la orden');
        }

        $contador = 0;
        foreach ($deudas as $index => $deuda) {
            \Log::info("Iteración {$index}: Procesando deuda ID {$deuda->id_deuda}");

            try {
                $detalle = DetalleOrdenPago::create([
                    'id_orden' => $orden->id_orden,
                    'id_deuda' => $deuda->id_deuda,
                    'id_concepto' => $deuda->id_concepto,
                    'id_politica' => NULL,
                    'monto_base' => $deuda->monto_total,
                    'monto_ajuste' => 0,
                    'monto_subtotal' => $deuda->monto_total,
                    'descripcion_ajuste' => NULL,
                ]);

                \Log::info("✓ Detalle creado exitosamente con ID: {$detalle->id_detalle}");
                $contador++;
            } catch (\Exception $e) {
                \Log::error("❌ Error al crear detalle para deuda {$deuda->id_deuda}: " . $e->getMessage());
                throw $e;
            }
        }

        \Log::info("Total detalles creados en foreach: {$contador}");

        $detallesCreados = DetalleOrdenPago::where('id_orden', $orden->id_orden)->count();
        \Log::info("Total detalles verificados en DB: {$detallesCreados}");

        if ($detallesCreados == 0) {
            \Log::error('❌ ALERTA: No se creó ningún detalle en la base de datos');
        }

        \Log::info('=== FIN CREACIÓN DE DETALLES ===');

        // VERIFICACIÓN CRÍTICA: Si no se crearon detalles, hacer rollback
        if ($detallesCreados == 0) {
            \Log::error('❌ FALLO CRÍTICO: No se creó ningún detalle, abortando transacción');
            throw new \Exception('No se pudieron crear los detalles de la orden de pago. Por favor, intente nuevamente.');
        }

        if ($detallesCreados != count($deudas)) {
            \Log::error('❌ ADVERTENCIA: Se crearon ' . $detallesCreados . ' detalles pero se esperaban ' . count($deudas));
        }

        return $orden->fresh(['detalles.conceptoPago', 'alumno', 'matricula']);
    }

    /**
     * Registrar transacción en pasarela
     */
    private function registrarTransaccion($request, $orden)
    {
        $metodo = $request->input('metodo_pago');
        $datosMetodo = [];

        // Construir datos según el método
        if ($metodo == 'yape' || $metodo == 'plin') {
            $datosMetodo['numero_celular'] = $request->input('numero_celular');
        } elseif ($metodo == 'transferencia') {
            $datosMetodo['banco_origen'] = $request->input('banco_origen');
            $datosMetodo['numero_operacion'] = $request->input('numero_operacion');
        } elseif ($metodo == 'tarjeta') {
            $datosMetodo['ultimos_digitos'] = substr($request->input('numero_tarjeta'), -4);
            $datosMetodo['nombre_titular'] = $request->input('nombre_titular');
        }

        return TransaccionPasarela::create([
            'id_orden' => $orden->id_orden,
            'metodo_pago' => $metodo,
            'monto' => $request->input('monto'),
            'estado' => 'pendiente',
            'fecha_transaccion' => Carbon::now(),
            'nro_recibo' => 'REC-' . Carbon::now()->format('Ymd') . '-' . rand(1000, 9999),
            'datos_metodo' => json_encode($datosMetodo),
            'voucher_path' => NULL,
            'validado_por' => NULL,
            'fecha_validacion' => NULL,
            'id_pago_generado' => NULL,
        ]);
    }

    /**
     * Registrar pago
     */
    private function registrarPago($orden, $monto)
    {
        return Pago::create([
            'id_deuda' => NULL,
            'id_orden' => $orden->id_orden,
            'tipo_pago' => 'orden_completa',
            'numero_pago_parcial' => NULL,
            'fecha_pago' => Carbon::now(),
            'monto' => $monto,
            'observaciones' => 'Pago registrado a nivel de orden',
            'estado' => true,
            'metodo_pago' => NULL,
            'numero_operacion' => NULL,
            'datos_adicionales' => NULL,
            'voucher_path' => NULL,
        ]);
    }

    /**
     * Registrar detalle de pago
     */
    private function registrarDetallePago($pago, $request, $metodo)
    {
        return DetallePago::create([
            'id_pago' => $pago->id_pago,
            'nro_recibo' => 'REC-' . Carbon::now()->format('Ymd') . '-' . rand(1000, 9999),
            'fecha_pago' => Carbon::now(),
            'monto' => $request->input('monto'),
            'observacion' => NULL,
            'estado' => true,
            'metodo_pago' => $metodo,
            'voucher_path' => NULL,
            'voucher_texto' => NULL,
            'estado_validacion' => 'pendiente',
            'validado_por_ia' => false,
            'porcentaje_confianza' => NULL,
            'razon_ia' => NULL,
        ]);
    }

    /**
     * Registrar distribución del pago en las deudas
     */
    private function registrarDistribucion($pago, $orden)
    {
        $detalles = $orden->detalles;
        $montoTotal = $pago->monto;
        $montoDistribuido = 0;

        foreach ($detalles as $index => $detalle) {
            // Para el último detalle, asignar el monto restante para evitar problemas de redondeo
            if ($index == count($detalles) - 1) {
                $montoAsignado = $montoTotal - $montoDistribuido;
            } else {
                // Distribuir proporcionalmente
                $proporcion = $detalle->monto_subtotal / $orden->monto_total;
                $montoAsignado = round($montoTotal * $proporcion, 2);
                $montoDistribuido += $montoAsignado;
            }

            DistribucionPagoDeuda::create([
                'id_pago' => $pago->id_pago,
                'id_deuda' => $detalle->id_deuda,
                'monto_aplicado' => $montoAsignado,
            ]);
        }
    }
}
