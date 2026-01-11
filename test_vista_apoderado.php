<?php

/**
 * Test para verificar la vista de apoderado con sidebar dinámico
 * Ejecutar: php test_vista_apoderado.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Familiar;
use App\Models\Alumno;
use App\Models\ComposicionFamiliar;
use Illuminate\Support\Facades\Hash;

echo "\n========================================\n";
echo "TEST: VISTA APODERADO CON SIDEBAR\n";
echo "========================================\n\n";

// Verificar usuario jeancito02
echo "📝 Paso 1: Verificando usuario jeancito02...\n";
$user = User::where('username', 'jeancito02')->first();

if (!$user || $user->tipo !== 'Familiar') {
    echo "   ❌ Usuario no configurado correctamente\n";
    exit(1);
}

echo "   ✅ Usuario encontrado: {$user->username} (Tipo: {$user->tipo})\n\n";

// Verificar familiar
echo "📝 Paso 2: Verificando familiar asociado...\n";
$familiar = Familiar::where('id_usuario', $user->id_usuario)->where('estado', true)->first();

if (!$familiar) {
    echo "   ❌ Familiar no encontrado\n";
    exit(1);
}

echo "   ✅ Familiar encontrado: ID {$familiar->idFamiliar}\n";
echo "      Nombre: {$familiar->primer_nombre} {$familiar->apellido_paterno}\n\n";

// Verificar alumnos vinculados
echo "📝 Paso 3: Verificando alumnos vinculados...\n";
$alumnos = $familiar->alumnos;

if ($alumnos->count() == 0) {
    echo "   ⚠️  No hay alumnos vinculados a este familiar\n";
    echo "   ℹ️  Esto significa que el combobox estará vacío\n\n";
} else {
    echo "   ✅ Alumnos vinculados: {$alumnos->count()}\n";
    foreach ($alumnos as $alumno) {
        echo "      - {$alumno->apellido_paterno} {$alumno->apellido_materno} {$alumno->primer_nombre} | DNI: {$alumno->dni}\n";
    }
    echo "\n";
}

// Verificar rutas
echo "📝 Paso 4: Verificando rutas disponibles...\n";
$routeCollection = Route::getRoutes();
$routasEsperadas = [
    'principal' => false,
    'familiar_dato_view' => false,
    'familiar_matricula_view' => false,
    'familiar_pago_view_pagos' => false
];

foreach ($routeCollection as $route) {
    $name = $route->getName();
    if (isset($routasEsperadas[$name])) {
        $routasEsperadas[$name] = true;
    }
}

$todasOk = true;
foreach ($routasEsperadas as $ruta => $encontrada) {
    if ($encontrada) {
        echo "   ✅ Ruta '{$ruta}' disponible\n";
    } else {
        echo "   ❌ Ruta '{$ruta}' NO encontrada\n";
        $todasOk = false;
    }
}

if ($todasOk) {
    echo "   ✅ Todas las rutas están disponibles\n\n";
} else {
    echo "   ⚠️  Algunas rutas no están disponibles\n\n";
}

echo "========================================\n";
echo "✅ VERIFICACIÓN COMPLETADA\n";
echo "========================================\n\n";

echo "🎯 Flujo esperado de la aplicación:\n\n";
echo "1️⃣ INICIO DE SESIÓN\n";
echo "   - Abre: http://127.0.0.1:8000/login\n";
echo "   - Username: jeancito02\n";
echo "   - Password: jeanmarko\n\n";

echo "2️⃣ PÁGINA INICIAL (Sin alumno seleccionado)\n";
echo "   - Solo verás el header con el combobox de selección de alumno\n";
echo "   - El sidebar estará vacío (no muestra nada)\n";
echo "   - El contenido principal estará en blanco\n\n";

echo "3️⃣ SELECCIONAR ALUMNO DEL COMBOBOX\n";
echo "   - Click en el combobox del header\n";
echo "   - Selecciona uno de los alumnos:\n";
foreach ($alumnos as $alumno) {
    echo "      • {$alumno->apellido_paterno} {$alumno->primer_nombre} | DNI: {$alumno->dni}\n";
}
echo "\n";

echo "4️⃣ SIDEBAR APARECE CON INFORMACIÓN\n";
echo "   El sidebar ahora mostrará:\n";
echo "   ┌─────────────────────────────┐\n";
echo "   │    [Foto del alumno]        │\n";
echo "   │                             │\n";
echo "   │  Nombre del Alumno          │\n";
echo "   │  DNI: XXXXXXXX             │\n";
echo "   │  Código: XXXX               │\n";
echo "   │      [Ver más]              │\n";
echo "   ├─────────────────────────────┤\n";
echo "   │  MATRÍCULAS                 │\n";
echo "   ├─────────────────────────────┤\n";
echo "   │  PAGOS                      │\n";
echo "   └─────────────────────────────┘\n\n";

echo "5️⃣ HACER CLICK EN 'VER MÁS'\n";
echo "   - Se abre la vista completa de datos del alumno\n";
echo "   - Muestra toda la información detallada\n";
echo "   - Aparece botón 'Volver' en la parte superior\n\n";

echo "6️⃣ HACER CLICK EN 'VOLVER'\n";
echo "   - Regresa a la vista en blanco\n";
echo "   - El sidebar sigue mostrando la información del alumno\n";
echo "   - Puedes navegar a MATRÍCULAS o PAGOS desde el sidebar\n\n";

echo "📌 NOTAS IMPORTANTES:\n";
echo "   • El sidebar SOLO aparece cuando hay un alumno seleccionado\n";
echo "   • Si no seleccionas alumno, solo ves el combobox\n";
echo "   • El botón 'Ver más' te lleva a la vista detallada\n";
echo "   • El botón 'Volver' regresa a la vista en blanco pero con sidebar\n\n";

if ($alumnos->count() > 0) {
    echo "✅ Todo está listo para probar\n\n";
} else {
    echo "⚠️  ADVERTENCIA: No hay alumnos vinculados\n";
    echo "   Necesitas vincular alumnos a este familiar primero\n\n";
}
