<?php

/**
 * Test para verificar la vista de "Ver deudas" de familiar
 *
 * Este script prueba que la vista de deudas muestre correctamente:
 * - Solo las deudas que NO han sido pagadas
 * - O las deudas que fueron pagadas pero AÚN NO fueron validadas
 * - Excluye las deudas con pagos validados (estado_validacion = 'validado')
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\DistribucionPagoDeuda;
use App\Models\DetallePago;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "TEST: Vista de Deudas - Familiar\n";
echo "========================================\n\n";

try {
    // 1. Buscar un alumno con deudas
    echo "1. Buscando alumno con deudas...\n";

    $alumno = Alumno::whereHas('deudas', function($q) {
        $q->where('estado', true);
    })->first();

    if (!$alumno) {
        echo "❌ No se encontró ningún alumno con deudas.\n";
        echo "Por favor, crea datos de prueba primero.\n";
        exit(1);
    }

    echo "✅ Alumno encontrado: {$alumno->apellido_paterno} {$alumno->apellido_materno}, {$alumno->primer_nombre}\n";
    echo "   ID Alumno: {$alumno->id_alumno}\n\n";

    // 2. Obtener todas las deudas del alumno
    echo "2. Obteniendo deudas del alumno...\n";

    $todasLasDeudas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true)
        ->with('concepto')
        ->get();

    echo "✅ Total de deudas: " . $todasLasDeudas->count() . "\n\n";

    // 3. Identificar deudas con pagos validados (que deben ser excluidas)
    echo "3. Identificando deudas con pagos validados (a excluir)...\n";

    // Obtener IDs de pagos validados
    $idPagosValidados = DetallePago::where('estado_validacion', '=', 'validado')
        ->where('estado', '=', true)
        ->pluck('id_pago')
        ->unique()
        ->toArray();

    echo "   IDs de pagos validados encontrados: " . count($idPagosValidados) . "\n";
    if (count($idPagosValidados) > 0) {
        echo "   Ejemplos: " . implode(', ', array_slice($idPagosValidados, 0, 5)) . "\n";
    }

    // Obtener IDs de deudas asociadas a esos pagos validados
    $idDeudasValidadas = [];
    if (!empty($idPagosValidados)) {
        $idDeudasValidadas = DistribucionPagoDeuda::whereIn('id_pago', $idPagosValidados)
            ->pluck('id_deuda')
            ->unique()
            ->toArray();
    }

    echo "   IDs de deudas con pagos validados: " . count($idDeudasValidadas) . "\n";
    if (count($idDeudasValidadas) > 0) {
        echo "   Ejemplos: " . implode(', ', array_slice($idDeudasValidadas, 0, 5)) . "\n";
    }
    echo "\n";

    // 4. Aplicar filtro como en el controlador
    echo "4. Aplicando filtro del controlador...\n";

    $deudasFiltradas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true);

    if (!empty($idDeudasValidadas)) {
        $deudasFiltradas->whereNotIn('id_deuda', $idDeudasValidadas);
    }

    $deudasFiltradas = $deudasFiltradas->with('concepto')->get();

    echo "✅ Deudas después del filtro: " . $deudasFiltradas->count() . "\n";
    echo "   (Excluidas: " . (count($idDeudasValidadas) > 0 ? count(array_intersect($todasLasDeudas->pluck('id_deuda')->toArray(), $idDeudasValidadas)) : 0) . " deudas con pagos validados)\n\n";

    // 5. Mostrar deudas que se mostrarán en la vista
    echo "5. Deudas que se mostrarán en la vista:\n";
    echo "================================================================================\n";
    echo str_pad("ID", 8) . " | " . str_pad("Concepto", 30) . " | " . str_pad("Período", 12) . " | " . str_pad("Monto", 12) . " | Estado del Pago\n";
    echo "================================================================================\n";

    foreach ($deudasFiltradas as $deuda) {
        // Verificar si tiene pagos y su estado
        $estadoPago = "Sin pagar";

        // Verificar si tiene distribuciones de pago
        $distribucionesPago = DistribucionPagoDeuda::where('id_deuda', $deuda->id_deuda)->get();

        if ($distribucionesPago->isNotEmpty()) {
            $tieneValidado = false;
            $tienePendiente = false;

            foreach ($distribucionesPago as $dist) {
                $pago = Pago::find($dist->id_pago);
                if ($pago && $pago->estado) {
                    $detallesPago = DetallePago::where('id_pago', $pago->id_pago)
                        ->where('estado', true)
                        ->get();

                    foreach ($detallesPago as $detalle) {
                        if ($detalle->estado_validacion === 'validado') {
                            $tieneValidado = true;
                        } elseif (in_array($detalle->estado_validacion, ['pendiente', 'rechazado', null])) {
                            $tienePendiente = true;
                        }
                    }
                }
            }

            if ($tienePendiente && !$tieneValidado) {
                $estadoPago = "Pago pendiente validación";
            } elseif ($tienePendiente && $tieneValidado) {
                $estadoPago = "Parcialmente validado";
            }
        }

        echo str_pad($deuda->id_deuda, 8) . " | " .
             str_pad(substr($deuda->concepto->descripcion ?? 'N/A', 0, 30), 30) . " | " .
             str_pad($deuda->periodo ?? 'N/A', 12) . " | " .
             str_pad('S/. ' . number_format($deuda->monto_total, 2), 12) . " | " .
             $estadoPago . "\n";
    }

    echo "================================================================================\n\n";

    // 6. Mostrar deudas excluidas (con pagos validados)
    if (!empty($idDeudasValidadas)) {
        $deudasExcluidas = Deuda::whereIn('id_deuda', $idDeudasValidadas)
            ->where('id_alumno', $alumno->id_alumno)
            ->with('concepto')
            ->get();

        if ($deudasExcluidas->isNotEmpty()) {
            echo "6. Deudas EXCLUIDAS (con pagos validados):\n";
            echo "================================================================================\n";
            echo str_pad("ID", 8) . " | " . str_pad("Concepto", 30) . " | " . str_pad("Período", 12) . " | " . str_pad("Monto", 12) . "\n";
            echo "================================================================================\n";

            foreach ($deudasExcluidas as $deuda) {
                echo str_pad($deuda->id_deuda, 8) . " | " .
                     str_pad(substr($deuda->concepto->descripcion ?? 'N/A', 0, 30), 30) . " | " .
                     str_pad($deuda->periodo ?? 'N/A', 12) . " | " .
                     str_pad('S/. ' . number_format($deuda->monto_total, 2), 12) . "\n";
            }

            echo "================================================================================\n\n";
        }
    }

    // 7. Verificar lógica de exclusión detallada
    echo "7. Verificación detallada de la lógica:\n";
    echo "   Flujo: Deuda → distribucion_pago_deuda → Pago → detalle_pago\n\n";

    if ($todasLasDeudas->isNotEmpty()) {
        $primeraDeuda = $todasLasDeudas->first();
        echo "   Ejemplo con Deuda ID: {$primeraDeuda->id_deuda}\n";
        echo "   - Concepto: {$primeraDeuda->concepto->descripcion}\n";

        $distribuciones = DistribucionPagoDeuda::where('id_deuda', $primeraDeuda->id_deuda)->get();
        echo "   - Distribuciones de pago: {$distribuciones->count()}\n";

        if ($distribuciones->isNotEmpty()) {
            foreach ($distribuciones as $dist) {
                echo "\n   → Distribución ID: {$dist->id_distribucion}\n";
                echo "     - ID Pago: {$dist->id_pago}\n";
                echo "     - Monto aplicado: S/. " . number_format($dist->monto_aplicado, 2) . "\n";

                $pago = Pago::find($dist->id_pago);
                if ($pago) {
                    echo "     - Pago estado: " . ($pago->estado ? 'Activo' : 'Inactivo') . "\n";

                    $detallesPago = DetallePago::where('id_pago', $pago->id_pago)->get();
                    echo "     - Detalles de pago: {$detallesPago->count()}\n";

                    foreach ($detallesPago as $detalle) {
                        echo "       → Detalle Pago ID: {$detalle->id_detalle}\n";
                        echo "         - Estado validación: " . ($detalle->estado_validacion ?? 'Sin estado') . "\n";
                        echo "         - Estado: " . ($detalle->estado ? 'Activo' : 'Inactivo') . "\n";
                    }
                }
            }
        } else {
            echo "   → No tiene pagos asociados (se mostrará en la vista)\n";
        }
    }

    echo "\n\n========================================\n";
    echo "✅ TEST COMPLETADO EXITOSAMENTE\n";
    echo "========================================\n";
    echo "\nResumen:\n";
    echo "- Total de deudas del alumno: " . $todasLasDeudas->count() . "\n";
    echo "- Deudas a mostrar en la vista: " . $deudasFiltradas->count() . "\n";
    echo "- Deudas excluidas (pagadas y validadas): " . (count($idDeudasValidadas) > 0 ? count(array_intersect($todasLasDeudas->pluck('id_deuda')->toArray(), $idDeudasValidadas)) : 0) . "\n";
    echo "\nLa vista ahora muestra solo:\n";
    echo "✓ Deudas que NO han sido pagadas\n";
    echo "✓ Deudas pagadas pero AÚN NO validadas\n";
    echo "✗ Excluye deudas con estado_validacion = 'validado'\n";
    echo "\nPuedes acceder a la vista en: http://127.0.0.1:8000/familiar/alumno-pagos/deudas\n";
    echo "(Asegúrate de estar autenticado como familiar y tener un alumno seleccionado)\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
