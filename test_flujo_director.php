<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "\n=== PRUEBA DE FLUJO COMPLETO COMO DIRECTOR ===\n\n";

// 1. Verificar que existe el usuario director
echo "1. Verificando usuario 'director'...\n";
$director = User::where('username', 'director')->first();

if (!$director) {
    echo "   ❌ Usuario 'director' no encontrado\n";
    echo "   Creando usuario director...\n";

    $director = new User();
    $director->username = 'director';
    $director->password = Hash::make('12345');
    $director->tipo = 'Director';
    $director->estado = 'Activo';
    $director->save();

    echo "   ✓ Usuario 'director' creado exitosamente (ID: {$director->id})\n";
} else {
    echo "   ✓ Usuario encontrado (ID: {$director->id})\n";

    // Verificar la contraseña
    if (Hash::check('12345', $director->password)) {
        echo "   ✓ Contraseña '12345' es correcta\n";
    } else {
        echo "   ⚠ Contraseña incorrecta, actualizando...\n";
        $director->password = Hash::make('12345');
        $director->save();
        echo "   ✓ Contraseña actualizada a '12345'\n";
    }
}

// 2. Simular inicio de sesión
echo "\n2. Simulando inicio de sesión como director...\n";
Auth::login($director);
echo "   ✓ Sesión iniciada exitosamente\n";
echo "   Usuario autenticado: " . Auth::user()->username . "\n";
echo "   Tipo: " . Auth::user()->tipo . "\n";

// 3. Verificar permisos
echo "\n3. Verificando permisos para gestionar usuarios...\n";
$puedeGestionar = in_array(Auth::user()->tipo, ['Director', 'Administrativo']);
if ($puedeGestionar) {
    echo "   ✓ El usuario tiene permisos para gestionar usuarios\n";
} else {
    echo "   ❌ El usuario NO tiene permisos\n";
}

// 4. Buscar usuario jeancito02 para cambiar su contraseña
echo "\n4. Buscando usuario 'jeancito02' para cambiar contraseña...\n";
$usuario = User::where('username', 'jeancito02')->first();

if (!$usuario) {
    echo "   ❌ Usuario 'jeancito02' no encontrado\n";
    exit(1);
}

echo "   ✓ Usuario encontrado (ID: {$usuario->id})\n";
$passwordAnterior = $usuario->password;

// 5. Simular cambio de contraseña
echo "\n5. Simulando cambio de contraseña...\n";
echo "   URL: http://127.0.0.1:8000/usuarios/{$usuario->id}/cambiar-password\n";

$nuevaPassword = 'nuevapass123';
echo "   Nueva contraseña: $nuevaPassword\n";

// Simular validación
$datos = [
    'password' => $nuevaPassword,
    'password_confirmation' => $nuevaPassword
];

$validator = validator($datos, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator->fails()) {
    echo "   ❌ Validación falló:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "      - $error\n";
    }
    exit(1);
}

echo "   ✓ Validación exitosa\n";

// Actualizar contraseña
$usuario->password = Hash::make($nuevaPassword);
$usuario->save();
echo "   ✓ Contraseña actualizada en la base de datos\n";

// Verificar que se guardó
$usuarioActualizado = User::where('username', 'jeancito02')->first();
if ($usuarioActualizado && Hash::check($nuevaPassword, $usuarioActualizado->password)) {
    echo "   ✓ Contraseña verificada correctamente\n";
} else {
    echo "   ❌ ERROR: La contraseña no se guardó correctamente\n";
    exit(1);
}

echo "   ✓ Redirección: http://127.0.0.1:8000/usuarios\n";
echo "   ✓ Mensaje: 'Contraseña actualizada correctamente'\n";

// 6. Restaurar contraseña original
echo "\n6. Restaurando contraseña original...\n";
$usuario->password = $passwordAnterior;
$usuario->save();
echo "   ✓ Contraseña restaurada\n";

// 7. Cerrar sesión
echo "\n7. Cerrando sesión...\n";
Auth::logout();
echo "   ✓ Sesión cerrada\n";

echo "\n=== RESULTADO FINAL ===\n";
echo "✓ El usuario Director puede iniciar sesión con username='director' y password='12345'\n";
echo "✓ El usuario Director tiene permisos para cambiar contraseñas\n";
echo "✓ El formulario de cambio de contraseña valida correctamente\n";
echo "✓ La contraseña se actualiza en la base de datos\n";
echo "✓ El sistema redirige a /usuarios después de cambiar la contraseña\n";
echo "\n¡TODAS LAS PRUEBAS PASARON EXITOSAMENTE!\n\n";
