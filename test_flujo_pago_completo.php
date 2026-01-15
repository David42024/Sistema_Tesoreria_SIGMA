<?php

/**
 * Test completo del flujo de pago para familiares
 *
 * Este test verifica:
 * 1. Que las rutas estén correctamente definidas
 * 2. Que el controlador FamiliarPagoRealizarController exista
 * 3. Que todas las vistas estén creadas
 * 4. Que las deudas no validadas se muestren correctamente
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Deuda;
use App\Models\DetallePago;
use App\Models\DistribucionPagoDeuda;

echo "\n========================================\n";
echo "TEST: FLUJO COMPLETO DE PAGO - FAMILIAR\n";
echo "========================================\n\n";

// 1. Verificar que las rutas estén definidas
echo "1. Verificando rutas...\n";
$rutasEsperadas = [
    'familiar_pago_realizar_index',
    'familiar_pago_realizar_procesar_seleccion',
    'familiar_pago_realizar_metodo',
    'familiar_pago_realizar_formulario',
    'familiar_pago_realizar_procesar',
    'familiar_pago_realizar_exito'
];

$rutasEncontradas = [];
foreach ($rutasEsperadas as $ruta) {
    try {
        $url = route($ruta, ['metodo' => 'yape', 'transaccion_id' => 1], false);
        $rutasEncontradas[] = $ruta;
        echo "   ✓ Ruta '$ruta' encontrada: $url\n";
    } catch (Exception $e) {
        echo "   ✗ Ruta '$ruta' NO encontrada\n";
    }
}

echo "\n   Total: " . count($rutasEncontradas) . "/" . count($rutasEsperadas) . " rutas correctas\n\n";

// 2. Verificar que el controlador exista
echo "2. Verificando controlador...\n";
if (class_exists('App\Http\Controllers\FamiliarPagoRealizarController')) {
    echo "   ✓ Controlador FamiliarPagoRealizarController existe\n";

    $reflection = new ReflectionClass('App\Http\Controllers\FamiliarPagoRealizarController');
    $metodos = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    echo "   Métodos disponibles:\n";
    foreach ($metodos as $metodo) {
        if ($metodo->class === 'App\Http\Controllers\FamiliarPagoRealizarController') {
            echo "      • " . $metodo->name . "\n";
        }
    }
} else {
    echo "   ✗ Controlador NO existe\n";
}

// 3. Verificar que las vistas existan
echo "\n3. Verificando vistas...\n";
$vistas = [
    'homev2.familiares.realizar_pago.index',
    'homev2.familiares.realizar_pago.seleccionar_metodo',
    'homev2.familiares.realizar_pago.metodos.yape',
    'homev2.familiares.realizar_pago.metodos.plin',
    'homev2.familiares.realizar_pago.metodos.transferencia',
    'homev2.familiares.realizar_pago.metodos.tarjeta',
    'homev2.familiares.realizar_pago.metodos.paypal',
    'homev2.familiares.realizar_pago.exito'
];

foreach ($vistas as $vista) {
    $rutaVista = str_replace('.', '/', $vista) . '.blade.php';
    $rutaCompleta = __DIR__ . '/resources/views/' . $rutaVista;

    if (file_exists($rutaCompleta)) {
        echo "   ✓ Vista '$vista' existe\n";
    } else {
        echo "   ✗ Vista '$vista' NO existe en: $rutaCompleta\n";
    }
}

// 4. Verificar deudas disponibles para un alumno de prueba
echo "\n4. Verificando deudas disponibles (sin validar)...\n";
$alumno_id = 842; // ID de ejemplo

// Obtener IDs de deudas que ya tienen pagos validados
$pagosValidadosIds = DetallePago::where('estado_validacion', 'validado')
    ->pluck('id_pago')
    ->toArray();

$deudasValidadasIds = [];
if (!empty($pagosValidadosIds)) {
    $deudasValidadasIds = DistribucionPagoDeuda::whereIn('id_pago', $pagosValidadosIds)
        ->pluck('id_deuda')
        ->unique()
        ->toArray();
}

// Obtener deudas del alumno que NO están validadas
$deudas = Deuda::with('conceptoPago')
    ->where('id_alumno', $alumno_id)
    ->where('estado', 1)
    ->whereNotIn('id_deuda', $deudasValidadasIds)
    ->get();

echo "   Alumno ID: $alumno_id\n";
echo "   Deudas disponibles para pago: " . count($deudas) . "\n\n";

if (count($deudas) > 0) {
    echo "   Detalle de deudas:\n";
    $totalDeuda = 0;
    foreach ($deudas as $deuda) {
        echo "      • ID: {$deuda->id_deuda}\n";
        echo "        Concepto: {$deuda->conceptoPago->descripcion}\n";
        echo "        Periodo: {$deuda->periodo}\n";
        echo "        Monto: S/ {$deuda->monto_total}\n";
        echo "        Fecha límite: {$deuda->fecha_limite}\n\n";
        $totalDeuda += $deuda->monto_total;
    }
    echo "   TOTAL ADEUDADO: S/ " . number_format($totalDeuda, 2) . "\n";
}

echo "\n========================================\n";
echo "TEST COMPLETADO\n";
echo "========================================\n\n";

if (count($rutasEncontradas) === count($rutasEsperadas)) {
    echo "✓ Todas las rutas están configuradas correctamente\n";
} else {
    echo "✗ Faltan algunas rutas por configurar\n";
}

echo "\n";
