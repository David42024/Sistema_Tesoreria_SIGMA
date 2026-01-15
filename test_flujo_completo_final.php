<?php

/**
 * TEST FINAL: Simular flujo completo de usuario
 * Simula: Login jeancito01 → Nilo Paz → Seleccionar deudas → Yape → Procesar
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

echo "\n╔════════════════════════════════════════╗\n";
echo "║  TEST FLUJO COMPLETO: NILO PAZ GUERRA  ║\n";
echo "╚════════════════════════════════════════╝\n\n";

try {
    // Buscar alumno Nilo Paz Guerra
    echo "🔍 Buscando alumno 'Nilo Paz Guerra'...\n";
    $alumno = Alumno::where('primer_nombre', 'like', '%Nilo%')
        ->where('apellido_paterno', 'like', '%Paz%')
        ->first();

    if (!$alumno) {
        echo "❌ Alumno no encontrado\n";
        exit(1);
    }

    echo "✅ Alumno: {$alumno->primer_nombre} {$alumno->apellido_paterno} (ID: {$alumno->id_alumno})\n\n";

    // Obtener deudas pendientes
    echo "📋 Obteniendo deudas pendientes...\n";
    $deudas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true)
        ->with('conceptoPago')
        ->limit(2)
        ->get();

    if ($deudas->isEmpty()) {
        echo "❌ No hay deudas pendientes\n";
        exit(1);
    }

    echo "✅ Deudas encontradas: {$deudas->count()}\n";
    foreach ($deudas as $deuda) {
        echo "   • ID {$deuda->id_deuda}: {$deuda->conceptoPago->descripcion} - S/ {$deuda->monto_total}\n";
    }
    echo "\n";

    // Simular selección (guardar IDs)
    echo "💾 Guardando IDs en 'sesión'...\n";
    $deudasIds = $deudas->pluck('id_deuda')->toArray();
    echo "✅ IDs guardados: [" . implode(', ', $deudasIds) . "]\n\n";

    // Simular mostrarFormularioMetodo (recargar deudas)
    echo "🔄 Recargando deudas desde BD...\n";
    $deudasSeleccionadas = Deuda::with('conceptoPago')
        ->whereIn('id_deuda', $deudasIds)
        ->where('estado', true)
        ->get();

    echo "✅ Deudas recargadas: {$deudasSeleccionadas->count()}\n";
    echo "   ¿Colección vacía?: " . ($deudasSeleccionadas->isEmpty() ? 'SÍ ❌' : 'NO ✅') . "\n\n";

    // INICIAR TRANSACCIÓN
    echo "🔐 Iniciando transacción...\n";
    DB::beginTransaction();

    // Obtener matrícula activa
    echo "🎓 Obteniendo matrícula activa...\n";
    $matricula = $alumno->matriculas()
        ->where('estado', true)
        ->orderBy('id_periodo_academico', 'desc')
        ->first();

    if (!$matricula) {
        throw new \Exception('No se encontró matrícula activa');
    }
    echo "✅ Matrícula ID: {$matricula->id_matricula}\n\n";

    // Calcular monto total
    $montoTotal = $deudasSeleccionadas->sum('monto_total');
    echo "💰 Monto total: S/ " . number_format($montoTotal, 2) . "\n\n";

    // Generar código de orden
    $anio = Carbon::now()->year;
    $ultimaOrden = OrdenPago::whereYear('created_at', $anio)->orderBy('id_orden', 'desc')->first();
    $numeroOrden = $ultimaOrden ? (intval(substr($ultimaOrden->codigo_orden, -4)) + 1) : 1;
    $codigoOrden = 'OP-' . $anio . '-' . str_pad($numeroOrden, 4, '0', STR_PAD_LEFT);

    // Crear orden de pago
    echo "📝 Creando orden de pago...\n";
    $fechaOrden = Carbon::now();
    $fechaVencimiento = Carbon::now()->addDays(3); // 3 DÍAS DESPUÉS

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

    echo "✅ ORDEN CREADA:\n";
    echo "   • ID: {$orden->id_orden}\n";
    echo "   • Código: {$orden->codigo_orden}\n";
    echo "   • Monto: S/ " . number_format($orden->monto_total, 2) . "\n";
    echo "   • Cuenta: {$orden->numero_cuenta}\n";
    echo "   • Fecha orden: {$fechaOrden->format('Y-m-d H:i:s')}\n";
    echo "   • Fecha vencimiento: {$fechaVencimiento->format('Y-m-d H:i:s')} (3 días después) ⏰\n\n";

    // Crear detalles de orden
    echo "📋 Creando detalles de orden...\n";

    if ($deudasSeleccionadas->isEmpty()) {
        throw new \Exception('❌ Colección de deudas vacía');
    }

    $contador = 0;
    foreach ($deudasSeleccionadas as $index => $deuda) {
        echo "   [{$index}] Procesando deuda ID {$deuda->id_deuda}...\n";

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

        echo "       ✅ Detalle ID {$detalle->id_detalle} creado\n";
        $contador++;
    }

    echo "\n✅ Total detalles creados: {$contador}/{$deudasSeleccionadas->count()}\n\n";

    // Verificar en BD
    echo "🔍 Verificando en base de datos...\n";
    $detallesEnDB = DetalleOrdenPago::where('id_orden', $orden->id_orden)->get();
    echo "✅ Detalles encontrados en BD: {$detallesEnDB->count()}\n";

    foreach ($detallesEnDB as $det) {
        echo "   • ID {$det->id_detalle}: Deuda {$det->id_deuda} - S/ {$det->monto_base}\n";
    }
    echo "\n";

    if ($detallesEnDB->count() == 0) {
        throw new \Exception('❌ No se creó ningún detalle');
    }

    // COMMIT
    DB::commit();
    echo "✅ TRANSACCIÓN COMMIT EXITOSO\n\n";

    // Verificación final
    echo "🎯 VERIFICACIÓN FINAL (después del commit)...\n";
    $ordenFinal = OrdenPago::with('detalles')->find($orden->id_orden);

    if ($ordenFinal) {
        echo "✅ Orden ID {$ordenFinal->id_orden} existe en BD\n";
        echo "✅ Detalles asociados: {$ordenFinal->detalles->count()}\n";

        $diffDias = $ordenFinal->fecha_orden_pago->diffInDays($ordenFinal->fecha_vencimiento);
        echo "✅ Diferencia de días: {$diffDias} días\n";
    }

    echo "\n╔═══════════════════════════════════╗\n";
    echo "║  ✅ TEST COMPLETADO EXITOSAMENTE  ║\n";
    echo "╚═══════════════════════════════════╝\n";
    echo "\n📊 Resumen:\n";
    echo "   • Orden ID: {$orden->id_orden}\n";
    echo "   • Código: {$orden->codigo_orden}\n";
    echo "   • Detalles: {$detallesEnDB->count()}\n";
    echo "   • Vencimiento: 3 días después ⏰\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n╔═══════════════════════╗\n";
    echo "║  ❌ ERROR DETECTADO   ║\n";
    echo "╚═══════════════════════╝\n\n";
    echo "❌ Mensaje: {$e->getMessage()}\n";
    echo "📁 Archivo: {$e->getFile()}\n";
    echo "📍 Línea: {$e->getLine()}\n\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
