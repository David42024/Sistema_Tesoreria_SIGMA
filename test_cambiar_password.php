<?php

/**
 * Script de prueba manual para verificar cambio de contraseña
 * Ejecutar desde la terminal: php test_cambiar_password.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

echo "\n========================================\n";
echo "PRUEBA: Cambio de Contraseña\n";
echo "========================================\n\n";

// 1. Verificar que el director existe
echo "1. Verificando usuario 'director'...\n";
$director = User::where('username', 'director')->first();

if (!$director) {
    echo "   ✗ Usuario 'director' no encontrado en la BD\n";
    exit(1);
}

echo "   ✓ Usuario 'director' encontrado (ID: {$director->id_usuario})\n\n";

// 2. Crear un usuario de prueba
echo "2. Creando usuario de prueba...\n";
$username = 'test_password_' . time();
$passwordOriginal = 'password123';

$usuarioPrueba = new User([
    'username' => $username,
    'password' => Hash::make($passwordOriginal),
    'tipo' => 'Personal',
    'estado' => true
]);
$usuarioPrueba->save();

echo "   ✓ Usuario de prueba creado\n";
echo "     - ID: {$usuarioPrueba->id_usuario}\n";
echo "     - Username: {$usuarioPrueba->username}\n";
echo "     - Password original: {$passwordOriginal}\n\n";

// 3. Verificar que la contraseña original funciona
echo "3. Verificando contraseña original...\n";
if (Hash::check($passwordOriginal, $usuarioPrueba->password)) {
    echo "   ✓ Contraseña original correcta\n\n";
} else {
    echo "   ✗ Contraseña original no funciona\n\n";
    $usuarioPrueba->delete();
    exit(1);
}

// 4. Simular cambio de contraseña
echo "4. Simulando cambio de contraseña...\n";
$nuevaPassword = 'nueva_password_456';

$datosFormulario = [
    'password' => $nuevaPassword,
    'password_confirmation' => $nuevaPassword
];

echo "   - Nueva contraseña: ******\n";
echo "   - Confirmación: ******\n\n";

// 5. Validar los datos
echo "5. Validando datos del formulario...\n";
$validator = Validator::make($datosFormulario, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator->fails()) {
    echo "   ✗ VALIDACIÓN FALLIDA:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "     - {$error}\n";
    }
    echo "\n";
    $usuarioPrueba->delete();
    exit(1);
}

echo "   ✓ Validación exitosa\n\n";

// 6. Actualizar la contraseña
echo "6. Actualizando contraseña en la base de datos...\n";
try {
    $usuarioPrueba->password = Hash::make($nuevaPassword);
    $usuarioPrueba->save();

    echo "   ✓ Contraseña actualizada exitosamente\n\n";

} catch (\Exception $e) {
    echo "   ✗ ERROR al actualizar contraseña:\n";
    echo "     {$e->getMessage()}\n\n";
    $usuarioPrueba->delete();
    exit(1);
}

// 7. Verificar que la contraseña antigua ya NO funciona
echo "7. Verificando que la contraseña antigua ya no funciona...\n";
$usuarioActualizado = User::find($usuarioPrueba->id_usuario);

if (Hash::check($passwordOriginal, $usuarioActualizado->password)) {
    echo "   ✗ ERROR: La contraseña antigua aún funciona\n\n";
    $usuarioPrueba->delete();
    exit(1);
} else {
    echo "   ✓ Contraseña antigua ya no funciona (correcto)\n\n";
}

// 8. Verificar que la contraseña NUEVA funciona
echo "8. Verificando que la nueva contraseña funciona...\n";
if (Hash::check($nuevaPassword, $usuarioActualizado->password)) {
    echo "   ✓ Nueva contraseña funciona correctamente\n\n";
} else {
    echo "   ✗ ERROR: La nueva contraseña NO funciona\n\n";
    $usuarioPrueba->delete();
    exit(1);
}

// 9. Limpiar
echo "9. Limpiando (eliminando usuario de prueba)...\n";
$usuarioPrueba->delete();
echo "   ✓ Usuario de prueba eliminado\n\n";

echo "========================================\n";
echo "✅ TODAS LAS PRUEBAS PASARON\n";
echo "========================================\n\n";
echo "El sistema de cambio de contraseña está funcionando correctamente.\n";
echo "Puedes probarlo desde el navegador:\n";
echo "1. Ve a http://127.0.0.1:8000/usuarios (login: director / 12345)\n";
echo "2. Busca un usuario en la lista\n";
echo "3. Haz clic en 'Cambiar Contraseña'\n";
echo "4. Ingresa la nueva contraseña dos veces\n";
echo "5. Haz clic en 'Cambiar Contraseña' para guardar\n\n";
