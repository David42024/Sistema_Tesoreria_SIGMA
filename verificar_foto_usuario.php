<?php
/**
 * Script para verificar la configuración de fotos de usuarios en la BD
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== VERIFICACIÓN DE FOTOS DE USUARIOS ===\n\n";

// Buscar el usuario jeancito03
$user = User::where('username', 'jeancito03')->first();

if (!$user) {
    die("Error: Usuario 'jeancito03' no encontrado\n");
}

echo "Usuario encontrado:\n";
echo "  ID: {$user->id}\n";
echo "  Username: {$user->username}\n";
echo "  Tipo: {$user->tipo}\n";
echo "  Foto: " . ($user->foto ?? 'NULL') . "\n\n";

if ($user->foto) {
    $rutaCompleta = storage_path('app/public/' . $user->foto);
    $rutaPublica = public_path('storage/' . $user->foto);

    echo "Rutas de archivo:\n";
    echo "  Storage: $rutaCompleta\n";
    echo "  Público: $rutaPublica\n\n";

    echo "Verificación de archivos:\n";
    echo "  ¿Existe en storage?: " . (file_exists($rutaCompleta) ? 'SÍ ✓' : 'NO ✗') . "\n";
    echo "  ¿Existe en public?: " . (file_exists($rutaPublica) ? 'SÍ ✓' : 'NO ✗') . "\n";

    if (file_exists($rutaCompleta)) {
        $size = filesize($rutaCompleta);
        echo "  Tamaño: " . number_format($size / 1024, 2) . " KB\n";

        $imageInfo = @getimagesize($rutaCompleta);
        if ($imageInfo) {
            echo "  Dimensiones: {$imageInfo[0]}x{$imageInfo[1]}\n";
            echo "  Tipo MIME: {$imageInfo['mime']}\n";
        }
    }

    echo "\nURL esperada en el navegador:\n";
    echo "  " . asset('storage/' . $user->foto) . "\n";
} else {
    echo "⚠ Este usuario NO tiene foto asignada\n";
}

echo "\n--- Otros usuarios con foto ---\n";
$usersConFoto = User::whereNotNull('foto')->get();
foreach ($usersConFoto as $u) {
    echo "  {$u->username} ({$u->tipo}): {$u->foto}\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";
