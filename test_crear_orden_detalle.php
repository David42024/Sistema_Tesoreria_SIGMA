<?php

/**
 * Test para verificar la creación de orden de pago y sus detalles
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\OrdenPago;
use App\Models\DetalleOrdenPago;
use Carbon\Carbon;

echo "\n========================================\n";
echo "TEST: CREACIÓN DE ORDEN Y DETALLES\n";
echo "========================================\n\n";

// ID de alumno de prueba
$alumno_id = 842;

echo "1. Buscando alumno ID: $alumno_id...\n";
$alumno = Alumno::with('matriculas')->find($alumno_id);

if (!$alumno) {
    echo "   ✗ Alumno no encontrado\n";
    exit(1);
}

echo "   ✓ Alumno encontrado: {$alumno->primer_nombre} {$alumno->apellido_paterno}\n\n";

// Buscar deudas pendientes
echo "2. Buscando deudas pendientes...\n";
$deudas = Deuda::where('id_alumno', $alumno_id)
    ->where('estado', true)
    ->with('conceptoPago')
    ->limit(2)
    ->get();

echo "   Deudas encontradas: " . count($deudas) . "\n";
foreach ($deudas as $deuda) {
    echo "      - ID: {$deuda->id_deuda}, Concepto: {$deuda->conceptoPago->descripcion}, Monto: S/ {$deuda->monto_total}\n";
}

if ($deudas->isEmpty()) {
    echo "   ✗ No hay deudas para probar\n";
    exit(1);
}

echo "\n3. Verificando datos de las deudas...\n";
foreach ($deudas as $index => $deuda) {
    echo "   Deuda $index:\n";
    echo "      - id_deuda: {$deuda->id_deuda}\n";
    echo "      - id_concepto: {$deuda->id_concepto}\n";
    echo "      - monto_total: {$deuda->monto_total}\n";
    echo "      - Tipo de objeto: " . get_class($deuda) . "\n";
}

// Obtener matrícula
echo "\n4. Buscando matrícula activa...\n";
$matricula = $alumno->matriculas()
    ->where('estado', true)
    ->orderBy('id_periodo_academico', 'desc')
    ->first();

if (!$matricula) {
    echo "   ✗ No hay matrícula activa\n";
    exit(1);
}

echo "   ✓ Matrícula encontrada ID: {$matricula->id_matricula}\n\n";

// Calcular monto total
$montoTotal = $deudas->sum('monto_total');
echo "5. Monto total calculado: S/ {$montoTotal}\n\n";

// Iniciar transacción
echo "6. Iniciando transacción de prueba...\n";
DB::beginTransaction();

try {
    // Generar código de orden
    $anio = Carbon::now()->year;
    $ultimaOrden = OrdenPago::whereYear('created_at', $anio)->orderBy('id_orden', 'desc')->first();
    $numeroOrden = $ultimaOrden ? (intval(substr($ultimaOrden->codigo_orden, -4)) + 1) : 1;
    $codigoOrden = 'OP-' . $anio . '-' . str_pad($numeroOrden, 4, '0', STR_PAD_LEFT);

    echo "   Código de orden generado: $codigoOrden\n\n";

    // Crear orden
    echo "7. Creando orden de pago...\n";
    $orden = OrdenPago::create([
        'codigo_orden' => $codigoOrden,
        'id_alumno' => $alumno->id_alumno,
        'id_matricula' => $matricula->id_matricula,
        'monto_total' => $montoTotal,
        'numero_cuenta' => '1234567890',
        'fecha_orden_pago' => Carbon::now(),
        'fecha_vencimiento' => Carbon::now()->addDays(7),
        'estado' => true,
        'observaciones' => NULL,
    ]);

    echo "   ✓ Orden creada con ID: {$orden->id_orden}\n";
    echo "   Código: {$orden->codigo_orden}\n";
    echo "   Monto: S/ {$orden->monto_total}\n";
    echo "   Número cuenta: {$orden->numero_cuenta}\n\n";

    // Crear detalles
    echo "8. Creando detalles de orden...\n";
    $detallesCreados = 0;

    foreach ($deudas as $index => $deuda) {
        echo "   Procesando deuda $index (ID: {$deuda->id_deuda})...\n";

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

            echo "      ✓ Detalle creado con ID: {$detalle->id_detalle}\n";
            $detallesCreados++;
        } catch (\Exception $e) {
            echo "      ✗ ERROR al crear detalle: " . $e->getMessage() . "\n";
            echo "      Datos intentados:\n";
            echo "         - id_orden: {$orden->id_orden}\n";
            echo "         - id_deuda: {$deuda->id_deuda}\n";
            echo "         - id_concepto: {$deuda->id_concepto}\n";
            echo "         - monto_base: {$deuda->monto_total}\n";
        }
    }

    echo "\n   Total detalles creados: $detallesCreados/" . count($deudas) . "\n\n";

    // Verificar en la base de datos
    echo "9. Verificando detalles en la base de datos...\n";
    $detallesDB = DetalleOrdenPago::where('id_orden', $orden->id_orden)->get();
    echo "   Detalles encontrados en DB: " . count($detallesDB) . "\n";

    foreach ($detallesDB as $detalle) {
        echo "      - ID: {$detalle->id_detalle}, Deuda: {$detalle->id_deuda}, Monto: S/ {$detalle->monto_subtotal}\n";
    }

    // Rollback para no afectar la base de datos
    DB::rollBack();
    echo "\n10. ✓ Transacción revertida (rollback) - Base de datos no modificada\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n   ✗ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "TEST COMPLETADO\n";
echo "========================================\n\n";

if ($detallesCreados > 0) {
    echo "✓ El código funciona correctamente\n";
    echo "  Se crearon $detallesCreados detalles de orden exitosamente\n\n";
} else {
    echo "✗ Hay un problema en la creación de detalles\n";
    echo "  Revisa los mensajes de error arriba\n\n";
}
