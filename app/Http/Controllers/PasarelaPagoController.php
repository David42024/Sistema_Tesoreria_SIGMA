<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenPago;
use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\DistribucionPagoDeuda;
use App\Models\TransaccionPasarela;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class PasarelaPagoController extends Controller
{
    /**
     * Página principal: formulario para ingresar código de orden
     */
    public function index()
    {
        return view('pasarela.index');
    }

    /**
     * Mostrar detalles de la orden de pago y métodos disponibles
     */
    public function mostrarOrden($codigo_orden)
    {
        // Buscar la orden por código
        $orden = OrdenPago::with([
            'alumno.matriculas.grado', 
            'matricula.grado', 
            'matricula.seccion',
            'detalles.conceptoPago',
            'detalles.deuda.conceptoPago'
        ])
            ->where('codigo_orden', $codigo_orden)
            ->first();

        // Validar si existe la orden
        if (!$orden) {
            return redirect()->route('pasarela.index')
                ->with('error', 'No se encontró ninguna orden con el código: ' . $codigo_orden);
        }

        // Calcular monto total pagado (sin importar si está validado)
        $montoPagado = TransaccionPasarela::where('id_orden', $orden->id_orden)
            ->sum('monto');
        
        // Calcular saldo pendiente
        $saldoPendiente = $orden->monto_total - $montoPagado;
        
        // Validar si la orden ya fue completamente pagada
        if ($saldoPendiente <= 0) {
            return redirect()->route('pasarela.index')
                ->with('success', '¡Esta orden ya ha sido pagada completamente! Monto total: S/ ' . number_format((float)$orden->monto_total, 2));
        }

        // Validar si la orden está vencida
        if ($orden->fecha_vencimiento < Carbon::now()) {
            // Si la orden está vencida PERO tiene pagos parciales, permitir continuar pagando
            if ($montoPagado > 0) {
                // Mostrar advertencia pero permitir pago
                return view('pasarela.orden', compact('orden', 'montoPagado', 'saldoPendiente'))
                    ->with('advertencia_vencida', 'Esta orden de pago venció el ' . Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') . ', pero puede completar el pago del saldo pendiente.');
            }
            
            // Si NO tiene pagos (orden vencida sin pagar), bloquear
            return view('pasarela.orden_vencida', compact('orden', 'montoPagado', 'saldoPendiente'));
        }

        return view('pasarela.orden', compact('orden', 'montoPagado', 'saldoPendiente'));
    }

    /**
     * Mostrar formulario del método de pago seleccionado
     */
    public function mostrarMetodoPago($codigo_orden, $metodo)
    {
        // Buscar la orden
        $orden = OrdenPago::with(['alumno', 'matricula.grado', 'matricula.seccion'])
            ->where('codigo_orden', $codigo_orden)
            ->first();

        if (!$orden) {
            return redirect()->route('pasarela.index')
                ->with('error', 'Orden no encontrada.');
        }

        // Calcular monto total pagado (sin importar si está validado)
        $montoPagado = TransaccionPasarela::where('id_orden', $orden->id_orden)
            ->sum('monto');
        
        $saldoPendiente = $orden->monto_total - $montoPagado;

        // Validar método de pago
        $metodos_validos = ['yape', 'plin', 'transferencia', 'tarjeta', 'paypal'];
        if (!in_array($metodo, $metodos_validos)) {
            return redirect()->route('pasarela.orden', $codigo_orden)
                ->with('error', 'Método de pago no válido.');
        }

        // Determinar vista según método
        $vista = "pasarela.metodos.{$metodo}";
        
        // Datos adicionales según el método
        $data = [
            'orden' => $orden,
            'metodo' => $metodo,
            'montoPagado' => $montoPagado,
            'saldoPendiente' => $saldoPendiente,
        ];

        // Para transferencia, agregar lista de bancos
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

        return view($vista, $data);
    }

    /**
     * Procesar el pago y registrarlo en la base de datos
     */
    public function procesarPago(Request $request, $codigo_orden)
    {
        // Buscar la orden
        $orden = OrdenPago::with(['detalles.deuda'])
            ->where('codigo_orden', $codigo_orden)
            ->first();

        if (!$orden) {
            return back()->with('error', 'Orden no encontrada.');
        }

        // Validar método de pago
        $metodo = $request->input('metodo_pago');
        $metodos_validos = ['yape', 'plin', 'transferencia', 'tarjeta', 'paypal'];
        
        if (!in_array($metodo, $metodos_validos)) {
            return back()->with('error', 'Método de pago no válido.');
        }

        // Validar campos según método de pago
        $this->validarCamposMetodoPago($request, $metodo);

        // Obtener y validar el monto del pago
        $monto_pago = $request->input('monto_pago');
        
        // Calcular cuánto se ha pagado ya (suma de todas las transacciones)
        $montoPagado = TransaccionPasarela::where('id_orden', $orden->id_orden)
            ->sum('monto');
        
        $saldoPendiente = $orden->monto_total - $montoPagado;
        
        // Validar que el monto no exceda el saldo pendiente
        if ($monto_pago > $saldoPendiente) {
            return back()->with('error', 'El monto del pago (S/ ' . number_format($monto_pago, 2) . ') excede el saldo pendiente (S/ ' . number_format($saldoPendiente, 2) . ').');
        }

        if ($monto_pago <= 0) {
            return back()->with('error', 'El monto del pago debe ser mayor a cero.');
        }

        try {
            DB::beginTransaction();

            // Generar número de operación automático
            $numero_operacion = $this->generarNumeroOperacion($metodo);

            // Crear la transacción de la pasarela (NO es un pago confirmado aún)
            $transaccion = TransaccionPasarela::create([
                'id_orden' => $orden->id_orden,
                'metodo_pago' => $metodo,
                'numero_operacion' => $numero_operacion,
                'fecha_transaccion' => Carbon::now(),
                'monto' => $monto_pago,
                'datos_adicionales' => $this->obtenerDatosAdicionales($request, $metodo),
                'estado' => 'pendiente',
                'voucher_path' => null, // Se genera después
                'observaciones' => $this->generarObservaciones($request, $metodo, $numero_operacion, $monto_pago),
            ]);

            // Actualizar estado de la orden a "En proceso de validación"
            $orden->update(['estado' => '1']); // 1 = Pendiente

            DB::commit();

            return redirect()->route('pasarela.comprobante', [
                'codigo_orden' => $codigo_orden,
                'transaccion_id' => $transaccion->id_transaccion
            ])->with('success', '¡Transacción registrada exitosamente! Tu pago está siendo validado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar comprobante de pago
     */
    public function mostrarComprobante($codigo_orden, $transaccion_id)
    {
        $orden = OrdenPago::with([
            'alumno', 
            'matricula.grado', 
            'matricula.seccion',
            'detalles.deuda.conceptoPago'
        ])
            ->where('codigo_orden', $codigo_orden)
            ->first();

        $transaccion = TransaccionPasarela::with('ordenPago')
            ->findOrFail($transaccion_id);

        // Por ahora pasamos la transacción como 'transaccion' 
        // Más adelante crearemos una vista específica para transacciones
        return view('pasarela.comprobante', compact('orden', 'transaccion'));
    }
    // ============================================
    // MÉTODOS PRIVADOS AUXILIARES
    // ============================================

    /**
     * Generar número de operación según el método de pago
     */
    private function generarNumeroOperacion($metodo)
    {
        switch ($metodo) {
            case 'yape':
            case 'plin':
                // Yape/Plin: 6-8 dígitos numéricos
                return (string) random_int(100000, 99999999);

            case 'transferencia':
                // Transferencia: 11 dígitos numéricos
                return (string) random_int(10000000000, 99999999999);

            case 'tarjeta':
                // Tarjeta: 13 dígitos numéricos
                return (string) random_int(1000000000000, 9999999999999);

            case 'paypal':
                // PayPal: 17 caracteres alfanuméricos
                $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $codigo = '';
                for ($i = 0; $i < 17; $i++) {
                    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
                }
                return $codigo;

            default:
                return (string) random_int(100000, 999999);
        }
    }

    /**
     * Validar campos específicos según método de pago
     */
    private function validarCamposMetodoPago(Request $request, $metodo)
    {
        $reglas = [
            'metodo_pago' => 'required|in:yape,plin,transferencia,tarjeta,paypal',
            'monto_pago' => 'required|numeric|min:1',
        ];

        switch ($metodo) {
            case 'yape':
            case 'plin':
                $reglas['celular'] = 'required|digits:9';
                // NO requerimos voucher, el sistema lo genera
                break;

            case 'transferencia':
                $reglas['banco'] = 'required|string';
                $reglas['numero_cuenta'] = 'required|string';
                // NO requerimos voucher, el sistema lo genera
                break;

            case 'tarjeta':
                $reglas['numero_tarjeta'] = 'required|digits:16';
                $reglas['fecha_vencimiento'] = 'required|regex:/^(0[1-9]|1[0-2])\/\d{2}$/';
                $reglas['cvv'] = 'required|digits:3';
                $reglas['nombre_titular'] = 'required|string|max:100';
                break;

            case 'paypal':
                $reglas['email_paypal'] = 'required|email';
                $reglas['password_paypal'] = 'required|string';
                break;
        }

        $request->validate($reglas);
    }

    /**
     * Guardar voucher en el storage
     */
    private function guardarVoucher($file, $codigo_orden, $numero_operacion)
    {
        $extension = $file->getClientOriginalExtension();
        $nombreArchivo = "voucher_{$codigo_orden}_{$numero_operacion}." . $extension;
        $ruta = $file->storeAs('vouchers', $nombreArchivo, 'public');
        
        return $ruta;
    }

    /**
     * Generar observaciones del pago
     */
    private function generarObservaciones($request, $metodo, $numero_operacion, $monto_pago)
    {
        $metodos = [
            'yape' => 'Yape',
            'plin' => 'Plin',
            'transferencia' => 'Transferencia Bancaria',
            'tarjeta' => 'Tarjeta de Crédito/Débito',
            'paypal' => 'PayPal',
        ];
        
        $nombre_metodo = $metodos[$metodo] ?? 'Otros';
        
        $obs = "Pago realizado mediante {$nombre_metodo}. ";
        $obs .= "Número de operación: {$numero_operacion}. ";
        $obs .= "Monto pagado: S/ " . number_format($monto_pago, 2) . ". ";
        
        switch ($metodo) {
            case 'yape':
            case 'plin':
                $obs .= "Celular: " . $request->input('celular');
                break;
            case 'transferencia':
                $obs .= "Banco: " . $request->input('banco');
                break;
            case 'tarjeta':
                $obs .= "Tarjeta: **** **** **** " . substr($request->input('numero_tarjeta'), -4);
                break;
            case 'paypal':
                $obs .= "Email PayPal: " . $request->input('email_paypal');
                break;
        }

        return $obs;
    }

    /**
     * Obtener datos adicionales del pago para JSON
     */
    private function obtenerDatosAdicionales($request, $metodo)
    {
        $datos = [
            'metodo' => $metodo,
            'fecha_registro' => Carbon::now()->toDateTimeString(),
            'ip' => $request->ip(),
        ];

        switch ($metodo) {
            case 'yape':
            case 'plin':
                $datos['celular'] = $request->input('celular');
                break;
            case 'transferencia':
                $datos['banco'] = $request->input('banco');
                $datos['numero_cuenta_origen'] = $request->input('numero_cuenta');
                break;
            case 'tarjeta':
                $datos['ultimos_4_digitos'] = substr($request->input('numero_tarjeta'), -4);
                $datos['nombre_titular'] = $request->input('nombre_titular');
                break;
            case 'paypal':
                $datos['email'] = $request->input('email_paypal');
                break;
        }

        return $datos;
    }

    /**
     * Generar y descargar voucher como PDF
     */
    public function descargarVoucherPDF($transaccion_id)
    {
        $transaccion = TransaccionPasarela::with([
            'ordenPago.alumno.matriculas.grado',
            'ordenPago.alumno.matriculas.seccion',
            'ordenPago.matricula.grado',
            'ordenPago.matricula.seccion'
        ])->findOrFail($transaccion_id);

        $datos = $this->prepararDatosVoucher($transaccion);
        
        // Renderizar la vista a HTML
        $html = view('pasarela.voucher_pdf', $datos)->render();
        
        // Configurar opciones de DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        // Crear instancia de DomPDF
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait'); // Tamaño A4 para que quepa en 1 página
        $dompdf->render();
        
        $nombreArchivo = 'voucher_' . $transaccion->numero_operacion . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"'
        ]);
    }

    /**
     * Mostrar voucher en HTML para captura como imagen
     */
    public function mostrarVoucherHTML($transaccion_id)
    {
        $transaccion = TransaccionPasarela::with([
            'ordenPago.alumno.matriculas.grado',
            'ordenPago.alumno.matriculas.seccion',
            'ordenPago.matricula.grado',
            'ordenPago.matricula.seccion'
        ])->findOrFail($transaccion_id);

        $datos = $this->prepararDatosVoucher($transaccion);
        
        return view('pasarela.voucher_generado', $datos);
    }

    /**
     * Preparar datos para el voucher
     */
    private function prepararDatosVoucher($transaccion)
    {
        $datos_adicionales = $transaccion->datos_adicionales ?? [];
        
        $alumno = $transaccion->ordenPago->alumno;
        $nombreCompleto = trim("{$alumno->primer_nombre} {$alumno->otros_nombres} {$alumno->apellido_paterno} {$alumno->apellido_materno}");
        
        // Obtener grado y sección desde la orden o desde la matrícula activa del alumno
        $matricula = $transaccion->ordenPago->matricula;
        
        // Si no hay matrícula en la orden, buscar la matrícula activa del alumno
        if (!$matricula) {
            $matricula = $alumno->matriculas()
                ->where('estado', true)
                ->with(['grado', 'seccion'])
                ->orderBy('id_periodo_academico', 'desc')
                ->first();
        }
        
        $grado = 'N/A';
        $seccion = 'N/A';
        
        if ($matricula) {
            // Obtener el nombre del grado
            if ($matricula->grado) {
                $grado = $matricula->grado->nombre_grado;
            }
            
            // Obtener el nombre de la sección directamente del campo nombreSeccion
            if ($matricula->nombreSeccion) {
                $seccion = $matricula->nombreSeccion;
            }
        }

        return [
            'pago_id' => $transaccion->id_transaccion,
            'metodo' => $transaccion->metodo_pago,
            'numero_operacion' => $transaccion->numero_operacion,
            'monto' => $transaccion->monto,
            'fecha' => Carbon::parse($transaccion->fecha_transaccion),
            'codigo_orden' => $transaccion->ordenPago->codigo_orden,
            'nombre_alumno' => $nombreCompleto,
            'grado' => $grado,
            'seccion' => $seccion,
            'datos_adicionales' => $datos_adicionales,
        ];
    }
}
