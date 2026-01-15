<?php

/**
 * Test adicional: Verificar alumno con deudas validadas
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Deuda;
use App\Models\DistribucionPagoDeuda;
use App\Models\DetallePago;
use App\Models\Alumno;

echo "\n========================================\n";
echo "TEST: Buscar alumno con deudas validadas\n";
echo "========================================\n\n";

// Buscar pagos validados
$idPagosValidados = DetallePago::where('estado_validacion', '=', 'validado')
    ->where('estado', '=', true)
    ->pluck('id_pago')
    ->unique()
    ->toArray();

echo "Pagos validados encontrados: " . count($idPagosValidados) . "\n";

if (empty($idPagosValidados)) {
    echo "No hay pagos validados en el sistema.\n";
    exit(0);
}

// Buscar deudas asociadas a esos pagos
$idDeudasValidadas = DistribucionPagoDeuda::whereIn('id_pago', $idPagosValidados)
    ->pluck('id_deuda')
    ->unique()
    ->toArray();

echo "Deudas con pagos validados: " . count($idDeudasValidadas) . "\n\n";

if (empty($idDeudasValidadas)) {
    echo "No hay deudas con pagos validados.\n";
    exit(0);
}

// Buscar un alumno que tenga deudas validadas
$deuda = Deuda::whereIn('id_deuda', $idDeudasValidadas)->with('alumno')->first();

if ($deuda && $deuda->alumno) {
    $alumno = $deuda->alumno;

    echo "✅ Alumno encontrado con deudas validadas:\n";
    echo "   Nombre: {$alumno->primer_nombre} {$alumno->apellido_paterno}\n";
    echo "   ID Alumno: {$alumno->id_alumno}\n\n";

    // Contar deudas del alumno
    $todasDeudas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true)
        ->get();

    $deudasValidadasAlumno = $todasDeudas->whereIn('id_deuda', $idDeudasValidadas);

    echo "Total de deudas del alumno: {$todasDeudas->count()}\n";
    echo "Deudas validadas (a excluir): {$deudasValidadasAlumno->count()}\n";
    echo "Deudas a mostrar: " . ($todasDeudas->count() - $deudasValidadasAlumno->count()) . "\n\n";

    // Aplicar filtro como en el controlador
    $deudasFiltradas = Deuda::where('id_alumno', $alumno->id_alumno)
        ->where('estado', true)
        ->whereNotIn('id_deuda', $idDeudasValidadas)
        ->get();

    echo "✅ Resultado del filtro del controlador:\n";
    echo "   Deudas después del filtro: {$deudasFiltradas->count()}\n";
    echo "   (Excluidas {$deudasValidadasAlumno->count()} deudas con pagos validados)\n\n";

    echo "Deudas validadas (EXCLUIDAS de la vista):\n";
    foreach ($deudasValidadasAlumno as $d) {
        echo "  - ID: {$d->id_deuda} | {$d->concepto->descripcion} | {$d->periodo}\n";
    }

    if ($deudasFiltradas->count() > 0) {
        echo "\nDeudas a mostrar (SÍ aparecen en la vista):\n";
        foreach ($deudasFiltradas as $d) {
            echo "  - ID: {$d->id_deuda} | {$d->concepto->descripcion} | {$d->periodo}\n";
        }
    }

} else {
    echo "No se encontró alumno con deudas validadas.\n";
}

echo "\n========================================\n";
echo "✅ VERIFICACIÓN COMPLETADA\n";
echo "========================================\n\n";
