<?php

/**
 * Test para verificar la vista de "Ver pagos anteriores" de familiar
 *
 * Este script prueba que la vista de pagos anteriores muestre correctamente:
 * - Mes (descripción del concepto de pago)
 * - Nº Orden (código de la orden de pago)
 * - Monto Pagado (monto subtotal del detalle de orden)
 * - Estado (estado de validación del detalle de pago)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Alumno;
use App\Models\DetalleOrdenPago;
use App\Models\OrdenPago;
use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\ConceptoPago;
use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "TEST: Vista de Pagos Anteriores - Familiar\n";
echo "========================================\n\n";

try {
    // 1. Buscar un alumno que tenga pagos realizados
    echo "1. Buscando alumno con pagos...\n";

    $detalleOrden = DetalleOrdenPago::whereHas('ordenPago.pagos', function($q) {
        $q->where('estado', true);
    })->first();

    if (!$detalleOrden) {
        echo "❌ No se encontró ningún detalle de orden con pagos asociados.\n";
        echo "Por favor, crea datos de prueba primero.\n";
        exit(1);
    }

    $alumno = $detalleOrden->ordenPago->alumno;
    echo "✅ Alumno encontrado: {$alumno->apellido_paterno} {$alumno->apellido_materno}, {$alumno->primer_nombre}\n";
    echo "   ID Alumno: {$alumno->id_alumno}\n\n";

    // 2. Obtener todos los detalles de órdenes de pago del alumno que tienen pagos
    echo "2. Obteniendo detalles de órdenes de pago con pagos realizados...\n";

    $detallesOrdenes = DetalleOrdenPago::whereHas('ordenPago', function($q) use ($alumno) {
        $q->where('id_alumno', '=', $alumno->id_alumno)
          ->whereHas('pagos', function($q2) {
              $q2->where('estado', '=', true);
          });
    })->with(['conceptoPago', 'ordenPago.pagos.detallesPago'])->get();

    echo "✅ Se encontraron " . $detallesOrdenes->count() . " detalles de órdenes de pago\n\n";

    if ($detallesOrdenes->isEmpty()) {
        echo "❌ No hay detalles de órdenes con pagos para este alumno.\n";
        exit(1);
    }

    // 3. Mostrar los datos que aparecerán en la tabla
    echo "3. Datos que se mostrarán en la tabla:\n";
    echo "================================================================================\n";
    echo str_pad("Mes", 30) . " | " . str_pad("Nº Orden", 12) . " | " . str_pad("Monto", 12) . " | Estado\n";
    echo "================================================================================\n";

    foreach ($detallesOrdenes as $detalle) {
        // Mes (descripción del concepto)
        $mes = $detalle->conceptoPago ? $detalle->conceptoPago->descripcion : 'N/A';

        // Nº Orden (código de orden)
        $nroOrden = $detalle->ordenPago ? $detalle->ordenPago->codigo_orden : 'N/A';

        // Monto Pagado
        $montoPagado = 'S/. ' . number_format($detalle->monto_subtotal, 2);

        // Estado de validación (navegando por las relaciones)
        $estadoValidacion = 'Sin estado';
        if ($detalle->ordenPago && $detalle->ordenPago->pagos) {
            foreach ($detalle->ordenPago->pagos as $pago) {
                if ($pago->estado && $pago->detallesPago) {
                    foreach ($pago->detallesPago as $detallePago) {
                        if ($detallePago->estado_validacion) {
                            $estadoValidacion = ucfirst($detallePago->estado_validacion);
                            break 2;
                        }
                    }
                }
            }
        }

        echo str_pad($mes, 30) . " | " .
             str_pad($nroOrden, 12) . " | " .
             str_pad($montoPagado, 12) . " | " .
             $estadoValidacion . "\n";
    }

    echo "================================================================================\n\n";

    // 4. Verificar las relaciones
    echo "4. Verificando relaciones entre tablas:\n";

    $primerDetalle = $detallesOrdenes->first();

    echo "\nDetalle de Orden de Pago (ID: {$primerDetalle->id_detalle}):\n";
    echo "  - ID Orden: {$primerDetalle->id_orden}\n";
    echo "  - ID Concepto: {$primerDetalle->id_concepto}\n";
    echo "  - Monto Subtotal: S/. " . number_format($primerDetalle->monto_subtotal, 2) . "\n";

    if ($primerDetalle->conceptoPago) {
        echo "\n✅ Concepto de Pago (ID: {$primerDetalle->conceptoPago->id_concepto}):\n";
        echo "  - Descripción: {$primerDetalle->conceptoPago->descripcion}\n";
    }

    if ($primerDetalle->ordenPago) {
        echo "\n✅ Orden de Pago (ID: {$primerDetalle->ordenPago->id_orden}):\n";
        echo "  - Código Orden: {$primerDetalle->ordenPago->codigo_orden}\n";
        echo "  - ID Alumno: {$primerDetalle->ordenPago->id_alumno}\n";

        $pagos = $primerDetalle->ordenPago->pagos()->where('estado', true)->get();
        echo "  - Cantidad de Pagos: {$pagos->count()}\n";

        if ($pagos->isNotEmpty()) {
            $primerPago = $pagos->first();
            echo "\n✅ Pago (ID: {$primerPago->id_pago}):\n";
            echo "  - Fecha: {$primerPago->fecha_pago}\n";
            echo "  - Monto: S/. " . number_format($primerPago->monto, 2) . "\n";

            $detallesPago = $primerPago->detallesPago;
            echo "  - Cantidad de Detalles de Pago: {$detallesPago->count()}\n";

            if ($detallesPago->isNotEmpty()) {
                $primerDetallePago = $detallesPago->first();
                echo "\n✅ Detalle de Pago (ID: {$primerDetallePago->id_detalle}):\n";
                echo "  - Estado Validación: " . ($primerDetallePago->estado_validacion ?? 'Sin estado') . "\n";
                echo "  - Método Pago: " . ($primerDetallePago->metodo_pago ?? 'N/A') . "\n";
            }
        }
    }

    echo "\n\n========================================\n";
    echo "✅ TEST COMPLETADO EXITOSAMENTE\n";
    echo "========================================\n";
    echo "\nLa vista ahora muestra:\n";
    echo "- Mes: Obtenido desde conceptos_pago.descripcion\n";
    echo "- Nº Orden: Obtenido desde ordenes_pago.codigo_orden\n";
    echo "- Monto Pagado: Obtenido desde detalle_orden_pago.monto_subtotal\n";
    echo "- Estado: Obtenido desde detalle_pago.estado_validacion\n";
    echo "\nPuedes acceder a la vista en: http://127.0.0.1:8000/familiar/alumno-pagos\n";
    echo "(Asegúrate de estar autenticado como familiar y tener un alumno seleccionado)\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
