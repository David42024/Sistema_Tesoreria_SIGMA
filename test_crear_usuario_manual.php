<?php

/**
 * Script de prueba manual para verificar la creación de usuarios
 * Ejecutar desde la terminal: php test_crear_usuario_manual.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

echo "\n========================================\n";
echo "PRUEBA MANUAL: Creación de Usuario\n";
echo "========================================\n\n";

// 1. Verificar que el director existe
echo "1. Verificando usuario 'director'...\n";
$director = User::where('username', 'director')->first();

if (!$director) {
    echo "   ✗ Usuario 'director' no encontrado en la BD\n";
    echo "   Por favor ejecuta el seeder o crea el usuario director primero\n\n";
    exit(1);
}

echo "   ✓ Usuario 'director' encontrado (ID: {$director->id_usuario})\n\n";

// 2. Simular los datos del formulario
$username = 'test_manual_' . time();
$datosFormulario = [
    'username' => $username,
    'password' => '123456',
    'password_confirmation' => '123456',
    'tipo' => 'Personal'
];

echo "2. Datos del formulario:\n";
foreach ($datosFormulario as $campo => $valor) {
    if ($campo === 'password' || $campo === 'password_confirmation') {
        echo "   - {$campo}: ******\n";
    } else {
        echo "   - {$campo}: {$valor}\n";
    }
}
echo "\n";

// 3. Validar los datos
echo "3. Validando datos...\n";
$validator = Validator::make($datosFormulario, [
    'username' => 'required|string|max:50|unique:users,username',
    'password' => 'required|string|min:6|confirmed',
    'tipo' => 'required|in:Administrativo,Personal,PreApoderado'
], [
    'username.required' => 'El nombre de usuario es obligatorio.',
    'username.unique' => 'El nombre de usuario ya existe.',
    'username.max' => 'El nombre de usuario no puede superar los 50 caracteres.',
    'password.required' => 'La contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.',
    'tipo.required' => 'El tipo de usuario es obligatorio.',
    'tipo.in' => 'El tipo de usuario no es válido.'
]);

if ($validator->fails()) {
    echo "   ✗ VALIDACIÓN FALLIDA:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "     - {$error}\n";
    }
    echo "\n";
    exit(1);
}

echo "   ✓ Validación exitosa\n\n";

// 4. Crear el usuario
echo "4. Creando usuario en la base de datos...\n";
try {
    $usuario = new User([
        'username' => $datosFormulario['username'],
        'password' => Hash::make($datosFormulario['password']),
        'tipo' => $datosFormulario['tipo'],
        'estado' => true
    ]);

    $usuario->save();

    echo "   ✓ Usuario creado exitosamente\n";
    echo "     - ID: {$usuario->id_usuario}\n";
    echo "     - Username: {$usuario->username}\n";
    echo "     - Tipo: {$usuario->tipo}\n";
    echo "     - Estado: " . ($usuario->estado ? 'Activo' : 'Inactivo') . "\n\n";

} catch (\Exception $e) {
    echo "   ✗ ERROR al crear usuario:\n";
    echo "     {$e->getMessage()}\n\n";
    exit(1);
}

// 5. Verificar que existe en la BD
echo "5. Verificando en base de datos...\n";
$verificacion = User::where('username', $username)->first();

if (!$verificacion) {
    echo "   ✗ Usuario NO encontrado en la BD después de crearlo\n\n";
    exit(1);
}

echo "   ✓ Usuario confirmado en la BD\n";

// 6. Verificar password
echo "6. Verificando contraseña hasheada...\n";
if (Hash::check('123456', $verificacion->password)) {
    echo "   ✓ Password correcta\n\n";
} else {
    echo "   ✗ Password NO coincide\n\n";
    exit(1);
}

// 7. Limpiar
echo "7. Limpiando (eliminando usuario de prueba)...\n";
$verificacion->delete();
echo "   ✓ Usuario de prueba eliminado\n\n";

echo "========================================\n";
echo "✅ TODAS LAS PRUEBAS PASARON\n";
echo "========================================\n\n";
echo "El sistema de creación de usuarios está funcionando correctamente.\n";
echo "Puedes crear usuarios desde el navegador en:\n";
echo "http://127.0.0.1:8000/usuarios/crear\n\n";
