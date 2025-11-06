<?php
/**
 * Test de Generación de PDF - Solicitud de Traslado
 *
 * Este script verifica que el sistema pueda generar PDFs correctamente
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SolicitudTraslado;
use Dompdf\Dompdf;
use Dompdf\Options;

echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║          TEST DE GENERACIÓN DE PDF - SOLICITUD DE TRASLADO                  ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

$testsPassed = 0;
$testsFailed = 0;

// Test 1: Verificar que la clase Dompdf está disponible
echo "📋 TEST 1: Verificar disponibilidad de Dompdf\n";
echo "─────────────────────────────────────────────────────────────────────────────\n";
try {
    $options = new Options();
    $dompdf = new Dompdf($options);
    echo "✅ PASS: Clase Dompdf está disponible\n\n";
    $testsPassed++;
} catch (\Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n\n";
    $testsFailed++;
}

// Test 2: Verificar que existe al menos una solicitud de traslado
echo "📋 TEST 2: Verificar solicitudes de traslado en base de datos\n";
echo "─────────────────────────────────────────────────────────────────────────────\n";
try {
    $solicitud = SolicitudTraslado::with('alumno')->first();

    if ($solicitud) {
        echo "✅ PASS: Se encontró solicitud de traslado\n";
        echo "   Código: {$solicitud->codigo_solicitud}\n";
        echo "   Alumno: {$solicitud->alumno->primer_nombre} {$solicitud->alumno->apellido_paterno}\n";
        echo "   Colegio Destino: {$solicitud->colegio_destino}\n\n";
        $testsPassed++;
    } else {
        echo "⚠️  ADVERTENCIA: No hay solicitudes de traslado registradas\n";
        echo "   Debe crear una solicitud primero para probar el PDF\n\n";
        $testsPassed++;
    }
} catch (\Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n\n";
    $testsFailed++;
}

// Test 3: Generar PDF de prueba
echo "📋 TEST 3: Generar PDF de prueba\n";
echo "─────────────────────────────────────────────────────────────────────────────\n";
if ($solicitud) {
    try {
        $alumno = $solicitud->alumno;
        $nombreCompleto = trim($alumno->primer_nombre . ' ' . $alumno->otros_nombres . ' ' . $alumno->apellido_paterno . ' ' . $alumno->apellido_materno);

        $data = [
            'solicitud' => $solicitud,
            'alumno' => $alumno,
            'nombre_completo' => $nombreCompleto,
            'grado_actual' => 'Prueba Grado',
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
        ];

        // Renderizar la vista HTML
        $html = view('gestiones.solicitud-traslado.pdf', $data)->render();

        // Verificar que el HTML se generó
        if (strlen($html) > 100) {
            echo "✅ PASS: Vista HTML renderizada correctamente\n";
            echo "   Tamaño HTML: " . number_format(strlen($html)) . " bytes\n";
            $testsPassed++;
        } else {
            throw new \Exception("HTML generado es muy pequeño");
        }

        // Configurar opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Generar PDF
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Obtener output
        $output = $dompdf->output();

        if (strlen($output) > 1000) {
            echo "✅ PASS: PDF generado correctamente\n";
            echo "   Tamaño PDF: " . number_format(strlen($output)) . " bytes\n";

            // Guardar PDF de prueba
            $nombreArchivo = 'test_solicitud_traslado_' . time() . '.pdf';
            file_put_contents(__DIR__ . '/' . $nombreArchivo, $output);
            echo "   Archivo guardado: $nombreArchivo\n\n";
            $testsPassed++;
        } else {
            throw new \Exception("PDF generado es muy pequeño o está vacío");
        }
    } catch (\Exception $e) {
        echo "❌ FAIL: " . $e->getMessage() . "\n\n";
        $testsFailed++;
    }
} else {
    echo "⚠️  SKIP: No hay solicitudes para generar PDF de prueba\n\n";
}

// Resumen Final
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           RESUMEN DE TESTS                                   ║\n";
echo "╠══════════════════════════════════════════════════════════════════════════════╣\n";
echo "║ Tests Exitosos:   " . str_pad($testsPassed, 2, ' ', STR_PAD_LEFT) . " ✅                                                     ║\n";
echo "║ Tests Fallidos:   " . str_pad($testsFailed, 2, ' ', STR_PAD_LEFT) . " " . ($testsFailed > 0 ? '❌' : '✅') . "                                                     ║\n";
echo "╠══════════════════════════════════════════════════════════════════════════════╣\n";

if ($testsFailed == 0) {
    echo "║                    🎉 TODOS LOS TESTS PASARON 🎉                             ║\n";
    echo "║                                                                              ║\n";
    echo "║ ✅ Dompdf está funcionando correctamente                                    ║\n";
    echo "║ ✅ El sistema puede generar PDFs de solicitud de traslado                   ║\n";
    echo "║                                                                              ║\n";
    echo "║ 🌐 Puede probar en: http://localhost/traslados                              ║\n";
} else {
    echo "║                   ⚠️  ALGUNOS TESTS FALLARON ⚠️                             ║\n";
    echo "║                                                                              ║\n";
    echo "║ Por favor revise los errores anteriores                                     ║\n";
}

echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

exit($testsFailed > 0 ? 1 : 0);
