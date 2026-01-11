<?php

/**
 * Test para verificar el usuario jeancito02 y sus permisos como Familiar
 * Ejecutar: php test_apoderado_jeancito02.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Familiar;
use Illuminate\Support\Facades\Hash;

echo "\n========================================\n";
echo "TEST: VERIFICACIÓN USUARIO JEANCITO02\n";
echo "========================================\n\n";

// Buscar usuario jeancito02
echo "📝 Paso 1: Buscando usuario 'jeancito02'...\n";
$user = User::where('username', 'jeancito02')->first();

if (!$user) {
    echo "   ❌ Usuario 'jeancito02' NO encontrado\n";
    echo "   ℹ️  Creando usuario...\n\n";

    $user = new User([
        'username' => 'jeancito02',
        'password' => Hash::make('jeanmarko'),
        'tipo' => 'Familiar',
        'estado' => true
    ]);
    $user->save();

    echo "   ✅ Usuario creado: ID {$user->id_usuario}\n\n";
} else {
    echo "   ✅ Usuario encontrado:\n";
    echo "      - ID: {$user->id_usuario}\n";
    echo "      - Username: {$user->username}\n";
    echo "      - Tipo: {$user->tipo}\n";
    echo "      - Estado: " . ($user->estado ? 'Activo' : 'Inactivo') . "\n";

    // Verificar/actualizar contraseña
    if (!Hash::check('jeanmarko', $user->password)) {
        echo "   ⚠️  Actualizando contraseña a 'jeanmarko'...\n";
        $user->password = Hash::make('jeanmarko');
        $user->save();
        echo "   ✅ Contraseña actualizada\n";
    } else {
        echo "   ✅ Contraseña correcta\n";
    }
    echo "\n";
}

// Verificar que sea de tipo Familiar
echo "📝 Paso 2: Verificando tipo de usuario...\n";
if ($user->tipo !== 'Familiar') {
    echo "   ⚠️  Usuario NO es tipo Familiar (actual: {$user->tipo})\n";
    echo "   ℹ️  Cambiando a tipo 'Familiar'...\n";
    $user->tipo = 'Familiar';
    $user->save();
    echo "   ✅ Tipo actualizado a 'Familiar'\n\n";
} else {
    echo "   ✅ Usuario es de tipo 'Familiar'\n\n";
}

// Verificar registro en tabla familiares
echo "📝 Paso 3: Verificando registro en tabla familiares...\n";
$familiar = Familiar::where('id_usuario', $user->id_usuario)->first();

if (!$familiar) {
    echo "   ⚠️  No existe registro en tabla familiares\n";
    echo "   ℹ️  Creando registro...\n";

    $familiar = new Familiar([
        'id_usuario' => $user->id_usuario,
        'dni' => '81713042',
        'apellido_paterno' => 'Flores',
        'apellido_materno' => 'Flores',
        'primer_nombre' => 'Yan',
        'otros_nombres' => '',
        'numero_contacto' => '999888777',
        'correo_electronico' => 'yan.flores@example.com',
        'estado' => true
    ]);
    $familiar->save();

    echo "   ✅ Familiar creado: ID {$familiar->idFamiliar}\n\n";
} else {
    echo "   ✅ Familiar encontrado:\n";
    echo "      - ID: {$familiar->idFamiliar}\n";
    echo "      - DNI: {$familiar->dni}\n";
    echo "      - Nombre: {$familiar->primer_nombre} {$familiar->otros_nombres}\n";
    echo "      - Apellidos: {$familiar->apellido_paterno} {$familiar->apellido_materno}\n";
    echo "      - Estado: " . ($familiar->estado ? 'Activo' : 'Inactivo') . "\n\n";
}

// Verificar permisos
echo "📝 Paso 4: Verificando permisos configurados...\n";
$permissions = config('familiar-permissions');

$recursosEsperados = ['datos', 'matriculas', 'pagos'];
$todosOk = true;

foreach ($recursosEsperados as $recurso) {
    if (isset($permissions[$recurso]) && isset($permissions[$recurso]['view'])) {
        if (in_array('Familiar', $permissions[$recurso]['view'])) {
            echo "   ✅ Permiso '{$recurso}' configurado correctamente\n";
        } else {
            echo "   ❌ Permiso '{$recurso}' NO tiene 'Familiar' en view\n";
            $todosOk = false;
        }
    } else {
        echo "   ❌ Permiso '{$recurso}' NO existe\n";
        $todosOk = false;
    }
}

if ($todosOk) {
    echo "   ✅ Todos los permisos están correctos\n\n";
} else {
    echo "   ⚠️  Algunos permisos tienen problemas\n\n";
}

// Información de las rutas
echo "📝 Paso 5: Verificando rutas disponibles...\n";
$routeCollection = Route::getRoutes();
$routesFound = [];

foreach ($routeCollection as $route) {
    $name = $route->getName();
    if ($name && (
        strpos($name, 'familiar_dato_') !== false ||
        strpos($name, 'familiar_matricula_') !== false ||
        strpos($name, 'familiar_pago_') !== false
    )) {
        $routesFound[] = [
            'name' => $name,
            'uri' => $route->uri()
        ];
    }
}

if (count($routesFound) > 0) {
    echo "   ✅ Rutas de Familiar encontradas: " . count($routesFound) . "\n";
    echo "   Principales:\n";
    $principales = ['familiar_dato_view', 'familiar_matricula_view', 'familiar_pago_view_pagos'];
    foreach ($principales as $ruta) {
        $encontrada = false;
        foreach ($routesFound as $r) {
            if ($r['name'] === $ruta) {
                echo "      - {$r['name']}: /{$r['uri']}\n";
                $encontrada = true;
                break;
            }
        }
        if (!$encontrada) {
            echo "      ⚠️  {$ruta} NO encontrada\n";
        }
    }
    echo "\n";
}

echo "========================================\n";
echo "✅ VERIFICACIÓN COMPLETADA\n";
echo "========================================\n\n";

echo "🎯 Instrucciones para probar:\n";
echo "   1. Inicia el servidor si no está corriendo:\n";
echo "      php artisan serve\n\n";
echo "   2. Abre tu navegador en:\n";
echo "      http://127.0.0.1:8000/login\n\n";
echo "   3. Inicia sesión con:\n";
echo "      Username: jeancito02\n";
echo "      Password: jeanmarko\n\n";
echo "   4. Deberías ver en el sidebar SOLO:\n";
echo "      - Datos personales\n";
echo "      - Matrículas\n";
echo "      - Pagos\n\n";
echo "   5. Si no ves el menú correcto:\n";
echo "      - Limpia la caché: php artisan cache:clear\n";
echo "      - Limpia config: php artisan config:clear\n";
echo "      - Cierra sesión y vuelve a entrar\n\n";
