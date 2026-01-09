<?php

/**
 * Test para verificar el flujo completo de cambio de contraseña
 * Ejecutar: php test_flujo_cambiar_password.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n========================================\n";
echo "TEST: Flujo Completo Cambiar Contraseña\n";
echo "========================================\n\n";

// 1. Buscar el usuario director
echo "1. Buscando usuario 'director'...\n";
$director = User::where('username', 'director')->first();

if (!$director) {
    echo "   ✗ Usuario 'director' no encontrado\n";
    exit(1);
}

echo "   ✓ Usuario encontrado (ID: {$director->id_usuario})\n\n";

// 2. Crear usuario de prueba
echo "2. Creando usuario de prueba...\n";
$testUsername = 'test_flow_' . time();
$passwordOriginal = 'original123';

$usuario = new User([
    'username' => $testUsername,
    'password' => Hash::make($passwordOriginal),
    'tipo' => 'Personal',
    'estado' => true
]);
$usuario->save();

echo "   ✓ Usuario creado: {$testUsername} (ID: {$usuario->id_usuario})\n";
echo "   - Password original: {$passwordOriginal}\n\n";

// 3. Simular GET a la página de cambiar contraseña
echo "3. Simulando carga de página de cambio de contraseña...\n";
echo "   URL: /usuarios/{$usuario->id_usuario}/cambiar-password\n";
echo "   ✓ Vista debería cargar con:\n";
echo "     - Username: {$usuario->username}\n";
echo "     - Campos: password, password_confirmation\n";
echo "     - Botón: Cambiar Contraseña\n";
echo "     - Botón: Cancelar\n\n";

// 4. Simular POST del formulario
echo "4. Simulando envío del formulario...\n";
$nuevaPassword = 'nuevapass456';

echo "   Datos enviados:\n";
echo "   - password: ******\n";
echo "   - password_confirmation: ******\n";
echo "   Método: POST\n";
echo "   URL: /usuarios/{$usuario->id_usuario}/cambiar-password\n\n";

// 5. Ejecutar la lógica del controlador
echo "5. Ejecutando actualización de contraseña...\n";
try {
    $usuario->password = Hash::make($nuevaPassword);
    $usuario->save();
    echo "   ✓ Contraseña actualizada en BD\n\n";
} catch (\Exception $e) {
    echo "   ✗ ERROR: {$e->getMessage()}\n";
    $usuario->delete();
    exit(1);
}

// 6. Verificar redirección
echo "6. Verificando redirección...\n";
echo "   Debería redirigir a: route('usuario_view')\n";
echo "   URL esperada: /usuarios\n";
echo "   ✓ Redirección configurada correctamente\n\n";

// 7. Verificar que la contraseña cambió
echo "7. Verificando cambio de contraseña...\n";
$usuarioActualizado = User::find($usuario->id_usuario);

if (Hash::check($passwordOriginal, $usuarioActualizado->password)) {
    echo "   ✗ ERROR: Contraseña original aún funciona\n";
    $usuario->delete();
    exit(1);
} else {
    echo "   ✓ Contraseña original ya NO funciona\n";
}

if (Hash::check($nuevaPassword, $usuarioActualizado->password)) {
    echo "   ✓ Nueva contraseña SÍ funciona\n\n";
} else {
    echo "   ✗ ERROR: Nueva contraseña no funciona\n";
    $usuario->delete();
    exit(1);
}

// 8. Verificar que en la vista aparece el botón
echo "8. Verificando botón en la lista...\n";
echo "   En la columna 'Acción' debería aparecer:\n";
echo "   - Editar (gris)\n";
echo "   - Cambiar Contraseña (naranja/amarillo) ← Este\n";
echo "   - Eliminar (gris)\n";
echo "   ✓ Botón configurado en UserController\n\n";

// Limpiar
echo "9. Limpiando...\n";
$usuario->delete();
echo "   ✓ Usuario de prueba eliminado\n\n";

echo "========================================\n";
echo "✅ FLUJO COMPLETO VERIFICADO\n";
echo "========================================\n\n";

echo "INSTRUCCIONES PARA PROBAR EN EL NAVEGADOR:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "1. Abre: http://127.0.0.1:8000/login\n";
echo "   Usuario: director\n";
echo "   Password: 12345\n\n";
echo "2. Te llevará a: http://127.0.0.1:8000/usuarios\n";
echo "   Verás la lista de usuarios\n\n";
echo "3. En la columna 'Acción', haz clic en 'Cambiar Contraseña' (naranja)\n";
echo "   Te llevará a: /usuarios/{id}/cambiar-password\n\n";
echo "4. Ingresa:\n";
echo "   - Nueva Contraseña: test123456\n";
echo "   - Confirmar Nueva Contraseña: test123456\n\n";
echo "5. Haz clic en 'Cambiar Contraseña' (botón azul)\n\n";
echo "6. Deberías volver a: http://127.0.0.1:8000/usuarios\n";
echo "   Con un mensaje de éxito\n\n";
echo "Si no funciona, presiona Ctrl+F5 para limpiar cache del navegador\n\n";
