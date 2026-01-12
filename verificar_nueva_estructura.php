<?php
/**
 * Verificar la nueva estructura con tabla archivos
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Archivo;

echo "=== VERIFICACIÓN DE NUEVA ESTRUCTURA CON TABLA ARCHIVOS ===\n\n";

// Verificar tabla archivos
echo "1. TABLA ARCHIVOS:\n";
$archivos = Archivo::all();
echo "   Total archivos: " . $archivos->count() . "\n\n";

foreach ($archivos as $archivo) {
    echo "   - ID: {$archivo->idArchivo}\n";
    echo "     Foto: {$archivo->foto}\n";
    echo "     Usuarios asociados: " . $archivo->users()->count() . "\n\n";
}

// Verificar jeancito03
echo "2. USUARIO JEANCITO03:\n";
$jeancito03 = User::where('username', 'jeancito03')->first();

if ($jeancito03) {
    echo "   Username: {$jeancito03->username}\n";
    echo "   Tipo: {$jeancito03->tipo}\n";
    echo "   idArchivo: " . ($jeancito03->idArchivo ?? 'NULL') . "\n";

    if ($jeancito03->archivo) {
        echo "   Archivo asociado:\n";
        echo "     - idArchivo: {$jeancito03->archivo->idArchivo}\n";
        echo "     - foto: {$jeancito03->archivo->foto}\n";
        echo "     - URL: " . asset('storage/' . $jeancito03->archivo->foto) . "\n";

        $rutaCompleta = storage_path('app/public/' . $jeancito03->archivo->foto);
        echo "     - Archivo existe: " . (file_exists($rutaCompleta) ? 'SÍ ✓' : 'NO ✗') . "\n";
    } else {
        echo "   ⚠ No tiene archivo asociado\n";
    }
} else {
    echo "   ✗ Usuario no encontrado\n";
}

// Verificar usuarios con archivo
echo "\n3. USUARIOS CON ARCHIVO:\n";
$usersConArchivo = User::whereNotNull('idArchivo')->with('archivo')->get();
echo "   Total usuarios con foto: " . $usersConArchivo->count() . "\n\n";

foreach ($usersConArchivo as $user) {
    echo "   - {$user->username} ({$user->tipo})\n";
    echo "     idArchivo: {$user->idArchivo}\n";
    if ($user->archivo) {
        echo "     foto: {$user->archivo->foto}\n";
    }
    echo "\n";
}

// Verificar que no existe columna foto en users
echo "4. VERIFICACIÓN DE ESTRUCTURA:\n";
$columns = DB::select("SHOW COLUMNS FROM users");
$hasFotoColumn = false;
$hasIdArchivoColumn = false;

foreach ($columns as $column) {
    if ($column->Field === 'foto') {
        $hasFotoColumn = true;
    }
    if ($column->Field === 'idArchivo') {
        $hasIdArchivoColumn = true;
    }
}

echo "   - Columna 'foto' en users: " . ($hasFotoColumn ? "SÍ ✗ (NO DEBERÍA EXISTIR)" : "NO ✓ (CORRECTO)") . "\n";
echo "   - Columna 'idArchivo' en users: " . ($hasIdArchivoColumn ? "SÍ ✓ (CORRECTO)" : "NO ✗ (DEBERÍA EXISTIR)") . "\n";

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
