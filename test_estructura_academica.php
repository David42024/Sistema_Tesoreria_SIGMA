<?php

/**
 * Script de prueba para verificar la estructura académica
 * Uso: php test_estructura_academica.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\Seccion;

echo "\n===========================================\n";
echo "   TEST DE ESTRUCTURA ACADÉMICA\n";
echo "===========================================\n\n";

// 1. Verificar Niveles Educativos
echo "📚 NIVELES EDUCATIVOS:\n";
echo "-------------------------------------------\n";
$niveles = NivelEducativo::where('estado', 1)->orderBy('id_nivel')->get();

$nivelesEsperados = ['INICIAL', 'PRIMARIA', 'SECUNDARIA'];
$nivelesEncontrados = $niveles->pluck('nombre_nivel')->map(fn($n) => strtoupper($n))->toArray();

echo "Niveles en DB: " . $niveles->count() . "\n";
foreach ($niveles as $nivel) {
    echo "  ✓ ID: {$nivel->id_nivel} - {$nivel->nombre_nivel}\n";
    echo "    Descripción: {$nivel->descripcion}\n";
}

// Validar que solo existan los 3 niveles esperados
$nivelesExtras = array_diff($nivelesEncontrados, $nivelesEsperados);
$nivelesFaltantes = array_diff($nivelesEsperados, $nivelesEncontrados);

if (count($nivelesExtras) > 0) {
    echo "\n⚠️  ADVERTENCIA: Se encontraron niveles NO esperados:\n";
    foreach ($nivelesExtras as $extra) {
        echo "  - $extra\n";
    }
}

if (count($nivelesFaltantes) > 0) {
    echo "\n❌ ERROR: Faltan niveles esperados:\n";
    foreach ($nivelesFaltantes as $faltante) {
        echo "  - $faltante\n";
    }
}

if (count($nivelesExtras) == 0 && count($nivelesFaltantes) == 0) {
    echo "\n✅ Los niveles educativos son correctos!\n";
}

// 2. Verificar Grados por Nivel
echo "\n\n📖 GRADOS POR NIVEL:\n";
echo "-------------------------------------------\n";

$gradosEsperados = [
    'INICIAL' => ['3 AÑOS', '4 AÑOS', '5 AÑOS'],
    'PRIMARIA' => ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO'],
    'SECUNDARIA' => ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO'],
];

foreach ($niveles as $nivel) {
    $nombreNivel = strtoupper($nivel->nombre_nivel);
    echo "\n{$nivel->nombre_nivel}:\n";
    
    $grados = Grado::where('id_nivel', $nivel->id_nivel)
        ->where('estado', 1)
        ->orderBy('id_grado')
        ->get();
    
    echo "  Total de grados: {$grados->count()}\n";
    
    foreach ($grados as $grado) {
        echo "    ✓ ID: {$grado->id_grado} - {$grado->nombre_grado}\n";
    }
    
    // Validar grados esperados
    if (isset($gradosEsperados[$nombreNivel])) {
        $gradosDB = $grados->pluck('nombre_grado')->map(fn($g) => strtoupper($g))->toArray();
        $esperados = array_map('strtoupper', $gradosEsperados[$nombreNivel]);
        
        $gradosExtras = array_diff($gradosDB, $esperados);
        $gradosFaltantes = array_diff($esperados, $gradosDB);
        
        if (count($gradosExtras) > 0) {
            echo "\n  ⚠️  ADVERTENCIA: Grados NO esperados en $nombreNivel:\n";
            foreach ($gradosExtras as $extra) {
                echo "    - $extra\n";
            }
        }
        
        if (count($gradosFaltantes) > 0) {
            echo "\n  ❌ ERROR: Faltan grados en $nombreNivel:\n";
            foreach ($gradosFaltantes as $faltante) {
                echo "    - $faltante\n";
            }
        }
        
        if (count($gradosExtras) == 0 && count($gradosFaltantes) == 0) {
            echo "  ✅ Grados correctos para $nombreNivel!\n";
        }
    }
}

// 3. Verificar Secciones por Grado
echo "\n\n📋 SECCIONES POR GRADO:\n";
echo "-------------------------------------------\n";

$seccionesEsperadas = ['A', 'B', 'C', 'D', 'E'];
$todosLosGrados = Grado::where('estado', 1)->with('nivelEducativo')->get();

$problemasEnSecciones = false;

foreach ($todosLosGrados as $grado) {
    $secciones = Seccion::where('id_grado', $grado->id_grado)
        ->where('estado', 1)
        ->orderBy('nombreSeccion')
        ->get();
    
    echo "\n{$grado->nivelEducativo->nombre_nivel} - {$grado->nombre_grado}:\n";
    echo "  Secciones: ";
    
    $seccionesDB = $secciones->pluck('nombreSeccion')->map(fn($s) => strtoupper($s))->toArray();
    echo implode(', ', $seccionesDB) . "\n";
    
    $seccionesExtras = array_diff($seccionesDB, $seccionesEsperadas);
    
    if (count($seccionesExtras) > 0) {
        echo "  ⚠️  ADVERTENCIA: Secciones NO esperadas (solo debe ser A-E):\n";
        foreach ($seccionesExtras as $extra) {
            echo "    - $extra\n";
        }
        $problemasEnSecciones = true;
    }
}

if (!$problemasEnSecciones) {
    echo "\n✅ Todas las secciones están en el rango A-E!\n";
}

// 4. Resumen Final
echo "\n\n===========================================\n";
echo "   RESUMEN\n";
echo "===========================================\n";
echo "Niveles Educativos: {$niveles->count()} (esperados: 3)\n";
echo "Total de Grados (registros): " . Grado::where('estado', 1)->count() . " (esperados: 14)\n";
echo "  - Los grados se crean por separado para cada nivel\n";
echo "  - PRIMERO-QUINTO aparecen en PRIMARIA y SECUNDARIA (no se reutilizan)\n";
echo "Total de Secciones (registros): " . Seccion::where('estado', 1)->count() . " (esperados: 70 = 14 grados × 5 secciones)\n";
echo "  - Cada grado tiene sus propias secciones A-E\n";

// Mostrar grados únicos por nombre
$nombresGradosUnicos = Grado::where('estado', 1)
    ->pluck('nombre_grado')
    ->unique()
    ->sort()
    ->values();

echo "\nNombres de grados únicos: {$nombresGradosUnicos->count()} (esperados: 9)\n";
echo "  " . $nombresGradosUnicos->implode(', ') . "\n";

echo "\n";

$estructuraCorrecta = (
    $niveles->count() == 3 &&
    Grado::where('estado', 1)->count() == 14 &&
    Seccion::where('estado', 1)->count() == 70 &&
    $nombresGradosUnicos->count() == 9 &&
    count($nivelesExtras) == 0 &&
    count($nivelesFaltantes) == 0 &&
    !$problemasEnSecciones
);

if ($estructuraCorrecta) {
    echo "🎉 ¡LA ESTRUCTURA ACADÉMICA ES CORRECTA!\n";
    echo "    - 3 niveles educativos ✓\n";
    echo "    - 14 registros de grados ✓\n";
    echo "    - 9 nombres de grados únicos ✓\n";
    echo "    - 70 registros de secciones (14×5) ✓\n";
    echo "    - Secciones solo A-E ✓\n";
} else {
    echo "⚠️  HAY PROBLEMAS EN LA ESTRUCTURA QUE DEBEN CORREGIRSE\n";
}

echo "\n===========================================\n\n";
