<?php

/**
 * Test de debugging: Simular exactamente el flujo del navegador
 * para identificar por qué detalle_orden_pago no se está creando
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\OrdenPago;
use App\Models\DetalleOrdenPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "\n=== TEST DEBUGGING: SIMULACIÓN FLUJO REAL ===\n\n";

try {
    // PASO 1: Simular selección de deudas (lo que hace procesarSeleccion)
    echo "PASO 1: Buscando alumno Nilo Paz...\n";
    $alumno = Alumno::where('primer_nombre', 'like', '%Nilo%')
        ->where('apellido_paterno', 'like', '%Paz%')
        ->first();

    if (!$alumno) {
        echo "❌ Alumno no encontrado\n";
        exit(1);
    }

    echo "✓ Alumno encontrado: ID {$alumno->id_alumno}\n\n";

    // PASO 2: Obtener deudas pendientes
    echo "PASO 2: Obteniendo deudas pendientes...\n";
    $deudas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true)
        ->with('conceptoPago')
        ->orderBy('fecha_limite', 'asc')
        ->limit(2)
        ->get();

    echo "✓ Deudas encontradas: " . $deudas->count() . "\n";
    echo "  IDs: " . $deudas->pluck('id_deuda')->implode(', ') . "\n\n";

    // PASO 3: Guardar IDs en "sesión" (simular lo que hace procesarSeleccion)
    echo "PASO 3: Guardando IDs de deudas (simular sesión)...\n";
    $deudasIds = $deudas->pluck('id_deuda')->toArray();
    echo "✓ IDs guardados: " . json_encode($deudasIds) . "\n\n";

    // PASO 4: Recargar deudas desde BD (lo que hace mostrarFormularioMetodo)
    echo "PASO 4: Recargando deudas desde BD (como mostrarFormularioMetodo)...\n";
    $deudasSeleccionadas = Deuda::with('conceptoPago')
        ->whereIn('id_deuda', $deudasIds)
        ->where('estado', true)
        ->get();

    echo "✓ Deudas recargadas: " . $deudasSeleccionadas->count() . "\n";
    echo "  IDs recargados: " . $deudasSeleccionadas->pluck('id_deuda')->implode(', ') . "\n";
    echo "  ¿Colección vacía?: " . ($deudasSeleccionadas->isEmpty() ? 'SÍ ❌' : 'NO ✓') . "\n\n";

    // PASO 5: Simular crearOrdenPago dentro de transacción
    echo "PASO 5: Iniciando transacción y creando orden...\n";
    DB::beginTransaction();

    // Obtener matrícula activa
    $matricula = $alumno->matriculas()
        ->where('estado', true)
        ->orderBy('id_periodo_academico', 'desc')
        ->first();

    if (!$matricula) {
        echo "❌ No hay matrícula activa\n";
        DB::rollBack();
        exit(1);
    }

    echo "✓ Matrícula activa encontrada: ID {$matricula->id_matricula}\n";

    // Calcular monto total
    $montoTotal = $deudasSeleccionadas->sum('monto_total');
    echo "✓ Monto total: S/ " . number_format($montoTotal, 2) . "\n";

    // Generar código de orden
    $anio = Carbon::now()->year;
    $ultimaOrden = OrdenPago::whereYear('created_at', $anio)->orderBy('id_orden', 'desc')->first();
    $numeroOrden = $ultimaOrden ? (intval(substr($ultimaOrden->codigo_orden, -4)) + 1) : 1;
    $codigoOrden = 'OP-' . $anio . '-' . str_pad($numeroOrden, 4, '0', STR_PAD_LEFT);
    echo "✓ Código generado: {$codigoOrden}\n\n";

    // Crear orden
    echo "PASO 6: Creando orden de pago...\n";
    $fechaOrden = Carbon::now();
    $fechaVencimiento = Carbon::now()->addDays(3);

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

    echo "✓ ORDEN CREADA:\n";
    echo "  ID: {$orden->id_orden}\n";
    echo "  Código: {$orden->codigo_orden}\n";
    echo "  Monto: S/ " . number_format($orden->monto_total, 2) . "\n";
    echo "  Número cuenta: {$orden->numero_cuenta}\n";
    echo "  Fecha orden: {$orden->fecha_orden_pago}\n";
    echo "  Fecha vencimiento: {$orden->fecha_vencimiento}\n\n";

    // PASO 7: Crear detalles (EL PROBLEMA ESTÁ AQUÍ)
    echo "PASO 7: Creando detalles de orden...\n";
    echo "  Tipo de variable \$deudasSeleccionadas: " . get_class($deudasSeleccionadas) . "\n";
    echo "  Cantidad: " . count($deudasSeleccionadas) . "\n";
    echo "  ¿isEmpty()?: " . ($deudasSeleccionadas->isEmpty() ? 'SÍ ❌' : 'NO ✓') . "\n\n";

    if ($deudasSeleccionadas->isEmpty()) {
        echo "❌ PROBLEMA CRÍTICO: La colección de deudas está VACÍA\n";
        DB::rollBack();
        exit(1);
    }

    $contador = 0;
    foreach ($deudasSeleccionadas as $index => $deuda) {
        echo "  Iteración {$index}:\n";
        echo "    - ID Orden: {$orden->id_orden}\n";
        echo "    - ID Deuda: {$deuda->id_deuda}\n";
        echo "    - ID Concepto: {$deuda->id_concepto}\n";
        echo "    - Monto: {$deuda->monto_total}\n";

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

            echo "    ✓ Detalle creado con ID: {$detalle->id_detalle}\n\n";
            $contador++;
        } catch (\Exception $e) {
            echo "    ❌ Error al crear detalle: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    echo "PASO 8: Verificando resultados...\n";
    echo "  Detalles creados en foreach: {$contador}\n";

    $detallesEnDB = DetalleOrdenPago::where('id_orden', $orden->id_orden)->count();
    echo "  Detalles en BD: {$detallesEnDB}\n\n";

    if ($detallesEnDB == 0) {
        echo "❌ ALERTA CRÍTICA: No se creó ningún detalle en la base de datos\n";
    } else {
        echo "✓ Detalles creados exitosamente\n";
    }

    // Commit de la transacción
    DB::commit();
    echo "\n✓ Transacción COMMIT realizado\n";

    // Verificar después del commit
    echo "\nPASO 9: Verificación DESPUÉS del commit...\n";
    $detallesDespuesCommit = DetalleOrdenPago::where('id_orden', $orden->id_orden)->count();
    echo "  Detalles en BD (después commit): {$detallesDespuesCommit}\n";

    // Mostrar los detalles
    $detalles = DetalleOrdenPago::where('id_orden', $orden->id_orden)->get();
    foreach ($detalles as $det) {
        echo "    - ID: {$det->id_detalle}, Deuda: {$det->id_deuda}, Monto: S/ {$det->monto_base}\n";
    }

    echo "\n✅ TEST COMPLETADO EXITOSAMENTE\n";
    echo "✅ Orden ID {$orden->id_orden} con {$detallesDespuesCommit} detalles\n";
    echo "✅ Fecha vencimiento: 3 días después de fecha_orden_pago\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR CRÍTICO:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
