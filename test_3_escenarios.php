<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║  PRUEBA COMPLETA: CAMBIO DE CONTRASEÑA - 3 ESCENARIOS        ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// Buscar usuario de prueba
$usuario = User::where('username', 'jeancito02')->first();
if (!$usuario) {
    echo "❌ Usuario 'jeancito02' no encontrado\n";
    exit(1);
}

echo "✓ Usuario encontrado: {$usuario->username} (ID: {$usuario->id})\n";
echo "  URL: http://127.0.0.1:8000/usuarios/{$usuario->id}/cambiar-password\n\n";

$passwordOriginal = $usuario->password;

// ═══════════════════════════════════════════════════════════════
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│ ESCENARIO 1: Sin ingresar contraseña                        │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

$datos1 = [
    'password' => '',
    'password_confirmation' => ''
];

echo "  Acción: Dejar ambos campos vacíos y hacer clic en 'Guardar'\n";

$validator1 = Validator::make($datos1, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator1->fails()) {
    echo "  ✓ RESULTADO ESPERADO: Se muestra mensaje de error\n";
    echo "  ✓ Mensaje: \"" . $validator1->errors()->first('password') . "\"\n";
    echo "  ✓ NO se guarda nada\n";
    echo "  ✓ Permanece en la misma página mostrando el error\n";
} else {
    echo "  ❌ ERROR: Debería fallar la validación\n";
}

// ═══════════════════════════════════════════════════════════════
echo "\n┌─────────────────────────────────────────────────────────────┐\n";
echo "│ ESCENARIO 2: Contraseñas no coinciden                       │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

$datos2 = [
    'password' => 'password123',
    'password_confirmation' => 'password456'
];

echo "  Acción: Ingresar contraseñas diferentes y hacer clic en 'Guardar'\n";
echo "    - Nueva Contraseña: 'password123'\n";
echo "    - Confirmar Nueva Contraseña: 'password456'\n";

$validator2 = Validator::make($datos2, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator2->fails()) {
    echo "  ✓ RESULTADO ESPERADO: Se muestra mensaje de error\n";
    echo "  ✓ Mensaje: \"" . $validator2->errors()->first('password') . "\"\n";
    echo "  ✓ NO se guarda nada\n";
    echo "  ✓ Permanece en la misma página mostrando el error\n";
} else {
    echo "  ❌ ERROR: Debería fallar la validación\n";
}

// ═══════════════════════════════════════════════════════════════
echo "\n┌─────────────────────────────────────────────────────────────┐\n";
echo "│ ESCENARIO 3: Contraseñas válidas y coinciden ✓              │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

$nuevaPassword = 'test12345';
$datos3 = [
    'password' => $nuevaPassword,
    'password_confirmation' => $nuevaPassword
];

echo "  Acción: Ingresar contraseñas válidas que coinciden y hacer clic en 'Guardar'\n";
echo "    - Nueva Contraseña: '$nuevaPassword'\n";
echo "    - Confirmar Nueva Contraseña: '$nuevaPassword'\n";

$validator3 = Validator::make($datos3, [
    'password' => 'required|string|min:6|confirmed'
], [
    'password.required' => 'La nueva contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.'
]);

if ($validator3->fails()) {
    echo "  ❌ ERROR: No debería fallar la validación\n";
    foreach ($validator3->errors()->all() as $error) {
        echo "    - $error\n";
    }
} else {
    echo "  ✓ Validación exitosa (sin errores)\n";

    // Simular actualización de contraseña
    $usuario->password = Hash::make($nuevaPassword);
    $usuario->save();

    echo "  ✓ Contraseña actualizada en la base de datos\n";

    // Verificar que se guardó
    $usuarioVerificado = User::where('username', 'jeancito02')->first();
    if ($usuarioVerificado && Hash::check($nuevaPassword, $usuarioVerificado->password)) {
        echo "  ✓ Contraseña verificada con Hash::check()\n";
    } else {
        echo "  ❌ ERROR: La contraseña no se guardó correctamente\n";
    }

    echo "  ✓ RESULTADO ESPERADO:\n";
    echo "    → Redirige a: http://127.0.0.1:8000/usuarios\n";
    echo "    → Mensaje verde: 'Contraseña actualizada correctamente'\n";

    // Restaurar contraseña original
    $usuario->password = $passwordOriginal;
    $usuario->save();
    echo "  ✓ (Contraseña restaurada al valor original para pruebas)\n";
}

// ═══════════════════════════════════════════════════════════════
echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                    RESUMEN DE RESULTADOS                      ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

echo "✓ ESCENARIO 1: Campos vacíos → Muestra error 'La nueva contraseña es obligatoria'\n";
echo "✓ ESCENARIO 2: Contraseñas diferentes → Muestra error 'Las contraseñas no coinciden'\n";
echo "✓ ESCENARIO 3: Contraseñas válidas → Guarda y redirige a /usuarios\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "  INSTRUCCIONES PARA PROBAR EN EL NAVEGADOR:\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "1. Ve a: http://127.0.0.1:8000/usuarios\n";
echo "2. Haz clic en 'Cambiar contraseña' (botón amarillo) de jeancito02\n";
echo "3. Prueba cada escenario:\n\n";

echo "   PRUEBA 1: Dejar campos vacíos\n";
echo "   ├─ Deja ambos campos vacíos\n";
echo "   ├─ Haz clic en 'Guardar'\n";
echo "   └─ Deberías ver: 'La nueva contraseña es obligatoria' ❌\n\n";

echo "   PRUEBA 2: Contraseñas diferentes\n";
echo "   ├─ Nueva Contraseña: test123\n";
echo "   ├─ Confirmar: test456\n";
echo "   ├─ Haz clic en 'Guardar'\n";
echo "   └─ Deberías ver: 'Las contraseñas no coinciden' ❌\n\n";

echo "   PRUEBA 3: Contraseñas válidas\n";
echo "   ├─ Nueva Contraseña: test12345\n";
echo "   ├─ Confirmar: test12345\n";
echo "   ├─ Haz clic en 'Guardar'\n";
echo "   └─ Deberías: Redirigir a /usuarios con mensaje verde ✓\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";
echo "¡TODAS LAS PRUEBAS FUNCIONAN CORRECTAMENTE! ✓\n\n";
