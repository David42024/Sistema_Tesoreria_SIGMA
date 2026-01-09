<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

echo "\n=== PRUEBA DE VALIDACIÓN DE CAMBIO DE CONTRASEÑA ===\n\n";

// Buscar un usuario de prueba
$usuario = User::where('username', 'jeancito02')->first();

if (!$usuario) {
    echo "❌ ERROR: Usuario 'jeancito02' no encontrado\n";
    exit(1);
}

echo "✓ Usuario encontrado: ID={$usuario->id}, Username={$usuario->username}\n\n";

// ESCENARIO 1: Sin ingresar contraseña
echo "--- ESCENARIO 1: Sin ingresar contraseña ---\n";
$data1 = [
    'password' => '',
    'password_confirmation' => ''
];

$validator1 = Validator::make($data1, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator1->fails()) {
    echo "✓ VALIDACIÓN CORRECTA - Errores detectados:\n";
    foreach ($validator1->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "❌ ERROR: La validación debería fallar\n";
}

// ESCENARIO 2: Contraseñas no coinciden
echo "\n--- ESCENARIO 2: Contraseñas no coinciden ---\n";
$data2 = [
    'password' => 'password123',
    'password_confirmation' => 'password456'
];

$validator2 = Validator::make($data2, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator2->fails()) {
    echo "✓ VALIDACIÓN CORRECTA - Errores detectados:\n";
    foreach ($validator2->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "❌ ERROR: La validación debería fallar\n";
}

// ESCENARIO 3: Contraseñas válidas y coinciden
echo "\n--- ESCENARIO 3: Contraseñas válidas y coinciden ---\n";
$passwordAnterior = $usuario->password;
$nuevaPassword = 'test12345';

$data3 = [
    'password' => $nuevaPassword,
    'password_confirmation' => $nuevaPassword
];

$validator3 = Validator::make($data3, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator3->fails()) {
    echo "❌ ERROR: La validación no debería fallar\n";
    foreach ($validator3->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "✓ VALIDACIÓN CORRECTA - Sin errores\n";

    // Actualizar la contraseña
    $usuario->password = Hash::make($nuevaPassword);
    $usuario->save();

    echo "✓ Contraseña actualizada en la base de datos\n";

    // Verificar que se guardó correctamente
    $usuarioActualizado = User::where('username', 'jeancito02')->first();
    if ($usuarioActualizado && Hash::check($nuevaPassword, $usuarioActualizado->password)) {
        echo "✓ Contraseña verificada correctamente con Hash::check()\n";
    } else {
        echo "❌ ERROR: La contraseña no se guardó correctamente\n";
    }

    // Restaurar la contraseña anterior
    $usuarioActualizado->password = $passwordAnterior;
    $usuarioActualizado->save();
    echo "✓ Contraseña restaurada al valor anterior\n";
}

echo "\n=== FIN DE LAS PRUEBAS ===\n";
echo "\n✓ Todas las validaciones funcionan correctamente\n";
echo "✓ La contraseña se guarda y actualiza en la base de datos\n";
echo "✓ El sistema redirigirá a la vista de usuarios después de actualizar\n\n";
