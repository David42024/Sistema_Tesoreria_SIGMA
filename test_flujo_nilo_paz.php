<?php

/**
 * Test del flujo completo de pago para Nilo Paz Guerra
 * Simula: Login con jeancito01 → Seleccionar alumno Nilo → Ver deudas → Registrar pago
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

echo "=== TEST FLUJO NILO PAZ GUERRA ===\n\n";

try {
    DB::beginTransaction();

    // 1. Buscar alumno "Nilo Paz Guerra"
    echo "1. Buscando alumno 'Nilo Paz'...\n";
    $alumno = Alumno::where('primer_nombre', 'like', '%Nilo%')
        ->orWhere('apellido_paterno', 'like', '%Paz%')
        ->first();

    if (!$alumno) {
        echo "❌ No se encontró alumno Nilo Paz\n";
        echo "Buscando alumnos disponibles:\n";
        $alumnos = Alumno::select('id_alumno', 'primer_nombre', 'otros_nombres', 'apellido_paterno', 'apellido_materno')
            ->limit(5)
            ->get();
        foreach ($alumnos as $a) {
            echo "  - ID: {$a->id_alumno}, Nombre: {$a->primer_nombre} {$a->otros_nombres} {$a->apellido_paterno} {$a->apellido_materno}\n";
        }
        exit(1);
    }

    echo "✓ Alumno encontrado: {$alumno->primer_nombre} {$alumno->otros_nombres} {$alumno->apellido_paterno} {$alumno->apellido_materno}\n";
    echo "  ID: {$alumno->id_alumno}\n\n";

    // 2. Obtener deudas pendientes
    echo "2. Obteniendo deudas pendientes...\n";
    $deudas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true)
        ->with('conceptoPago')
        ->orderBy('fecha_limite', 'asc')
        ->get();

    echo "Deudas encontradas: " . $deudas->count() . "\n";

    if ($deudas->isEmpty()) {
        echo "❌ No hay deudas pendientes para este alumno\n";
        DB::rollBack();
        exit(1);
    }

    // Mostrar primeras 3 deudas
    $deudasSeleccionadas = $deudas->take(2);
    foreach ($deudasSeleccionadas as $index => $deuda) {
        echo "  - ID: {$deuda->id_deuda}, Concepto: {$deuda->conceptoPago->descripcion}, Monto: S/ " . number_format($deuda->monto_total, 2) . "\n";
    }
    echo "\n";

    // 3. Obtener matrícula activa
    echo "3. Obteniendo matrícula activa...\n";
    $matricula = $alumno->matriculas()
        ->where('estado', true)
        ->orderBy('id_periodo_academico', 'desc')
        ->first();

    if (!$matricula) {
        echo "❌ No se encontró matrícula activa\n";
        DB::rollBack();
        exit(1);
    }

    echo "✓ Matrícula encontrada ID: {$matricula->id_matricula}\n\n";

    // 4. Calcular monto total
    $montoTotal = $deudasSeleccionadas->sum('monto_total');
    echo "4. Monto total calculado: S/ " . number_format($montoTotal, 2) . "\n\n";

    // 5. Generar código de orden
    echo "5. Generando código de orden...\n";
    $anio = Carbon::now()->year;
    $ultimaOrden = OrdenPago::whereYear('created_at', $anio)->orderBy('id_orden', 'desc')->first();
    $numeroOrden = $ultimaOrden ? (intval(substr($ultimaOrden->codigo_orden, -4)) + 1) : 1;
    $codigoOrden = 'OP-' . $anio . '-' . str_pad($numeroOrden, 4, '0', STR_PAD_LEFT);
    echo "✓ Código generado: {$codigoOrden}\n\n";

    // 6. Crear orden de pago
    echo "6. Creando orden de pago...\n";

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

    echo "✓ Orden creada con ID: {$orden->id_orden}\n";
    echo "  Código: {$orden->codigo_orden}\n";
    echo "  Monto: S/ " . number_format($orden->monto_total, 2) . "\n";
    echo "  Número cuenta: {$orden->numero_cuenta}\n";
    echo "  Fecha orden: {$orden->fecha_orden_pago}\n";
    echo "  Fecha vencimiento: {$orden->fecha_vencimiento} (3 días después)\n\n";

    // 7. Crear detalles de orden
    echo "7. Creando detalles de orden...\n";
    echo "Deudas a procesar: " . count($deudasSeleccionadas) . "\n";
    echo "Tipo de colección: " . get_class($deudasSeleccionadas) . "\n\n";

    $detallesCreados = 0;

    foreach ($deudasSeleccionadas as $index => $deuda) {
        echo "Procesando deuda {$index} (ID: {$deuda->id_deuda})...\n";
        echo "  - id_orden: {$orden->id_orden}\n";
        echo "  - id_deuda: {$deuda->id_deuda}\n";
        echo "  - id_concepto: {$deuda->id_concepto}\n";
        echo "  - monto_base: {$deuda->monto_total}\n";

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

        echo "  ✓ Detalle creado con ID: {$detalle->id_detalle_orden}\n\n";
        $detallesCreados++;
    }

    echo "Total detalles creados: {$detallesCreados}/" . count($deudasSeleccionadas) . "\n\n";

    // 8. Verificar en base de datos
    echo "8. Verificando en base de datos...\n";
    $detallesEnDB = DetalleOrdenPago::where('id_orden', $orden->id_orden)->get();
    echo "Detalles encontrados en DB: " . $detallesEnDB->count() . "\n";

    foreach ($detallesEnDB as $detalle) {
        echo "  - ID: {$detalle->id_detalle_orden}, Deuda: {$detalle->id_deuda}, Monto: S/ " . number_format($detalle->monto_base, 2) . "\n";
    }

    echo "\n";

    // 9. Verificar relación
    echo "9. Verificando relación orden->detalles...\n";
    $ordenConDetalles = OrdenPago::with('detalles')->find($orden->id_orden);
    echo "Orden ID: {$ordenConDetalles->id_orden}\n";
    echo "Detalles cargados: " . $ordenConDetalles->detalles->count() . "\n\n";

    // Rollback para no afectar la BD
    DB::rollBack();
    echo "✓ Transacción revertida (rollback)\n\n";

    if ($detallesCreados == count($deudasSeleccionadas) && $detallesEnDB->count() == $detallesCreados) {
        echo "✅ EL CÓDIGO FUNCIONA CORRECTAMENTE\n";
        echo "✅ Fecha de vencimiento configurada: 3 días después de fecha_orden_pago\n";
    } else {
        echo "❌ HAY UN PROBLEMA CON LA CREACIÓN DE DETALLES\n";
        exit(1);
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
