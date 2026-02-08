<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserCreationRealDBTest extends TestCase
{
    /** @test */
    public function test_crear_usuario_en_bd_real()
    {
        if (!$this->useEnvDatabaseConnection()) {
            $this->markTestSkipped('No se pudo cargar la configuracion de BD desde .env.');
        }

        if (DB::getDriverName() === 'sqlite' || !Schema::hasTable('users')) {
            $this->markTestSkipped('Este test requiere una BD real con tablas cargadas.');
        }

        // Buscar el director real
        $director = User::where('username', 'director')->first();

        if (!$director) {
            $this->markTestSkipped('Usuario director no existe en la BD');
        }

        // Autenticarse como director
        $this->actingAs($director);

        // Generar username único para evitar conflictos
        $username = 'test_user_' . time();

        echo "\n========================================\n";
        echo "Iniciando prueba de creación de usuario\n";
        echo "========================================\n\n";

        // 1. Visitar la página de crear usuario
        echo "1. Visitando /usuarios/crear...\n";
        $response = $this->get(route('usuario_create'));
        $response->assertStatus(200);
        echo "   ✓ Página cargada correctamente (Status 200)\n\n";

        // 2. Enviar el formulario
        echo "2. Enviando formulario con datos:\n";
        echo "   - Username: {$username}\n";
        echo "   - Tipo: Personal\n";
        echo "   - Password: 123456\n";
        echo "   - Password Confirmation: 123456\n\n";

        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => $username,
            'tipo' => 'Personal',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        echo "3. Verificando respuesta...\n";

        // Verificar errores de validación
        if ($response->getSession()->has('errors')) {
            $errors = $response->getSession()->get('errors');
            echo "   ✗ ERRORES DE VALIDACIÓN ENCONTRADOS:\n";
            foreach ($errors->all() as $error) {
                echo "     - {$error}\n";
            }
            $this->fail('La validación falló');
        } else {
            echo "   ✓ Sin errores de validación\n";
        }

        // Verificar redirección
        $response->assertRedirect(route('usuario_view'));
        echo "   ✓ Redirige correctamente a /usuarios\n\n";

        // 4. Verificar en la base de datos
        echo "4. Verificando en base de datos...\n";
        $usuario = User::where('username', $username)->first();

        if (!$usuario) {
            echo "   ✗ Usuario NO encontrado en la base de datos\n";
            $this->fail('El usuario no fue creado en la BD');
        } else {
            echo "   ✓ Usuario encontrado en la BD\n";
            echo "     - ID: {$usuario->id_usuario}\n";
            echo "     - Username: {$usuario->username}\n";
            echo "     - Tipo: {$usuario->tipo}\n";
            echo "     - Estado: " . ($usuario->estado ? 'Activo' : 'Inactivo') . "\n";

            // Verificar password
            if (Hash::check('123456', $usuario->password)) {
                echo "   ✓ Password hasheada correctamente\n";
            } else {
                echo "   ✗ Password NO coincide\n";
                $this->fail('La contraseña no fue hasheada correctamente');
            }
        }

        echo "\n========================================\n";
        echo "✅ PRUEBA EXITOSA - Usuario creado correctamente\n";
        echo "========================================\n\n";

        // Limpiar: eliminar el usuario de prueba
        if ($usuario) {
            $usuario->delete();
            echo "Usuario de prueba eliminado de la BD\n\n";
        }

        $this->assertTrue(true);
    }

    private function useEnvDatabaseConnection(): bool
    {
        $envPath = base_path('.env');
        if (!is_file($envPath)) {
            return false;
        }

        $env = $this->readDotEnv($envPath);
        $connection = $env['DB_CONNECTION'] ?? null;
        if (!$connection || !config("database.connections.$connection")) {
            return false;
        }

        $connectionConfig = config("database.connections.$connection");
        $overrides = [
            'url' => 'DB_URL',
            'host' => 'DB_HOST',
            'port' => 'DB_PORT',
            'database' => 'DB_DATABASE',
            'username' => 'DB_USERNAME',
            'password' => 'DB_PASSWORD',
            'charset' => 'DB_CHARSET',
            'collation' => 'DB_COLLATION',
            'unix_socket' => 'DB_SOCKET'
        ];

        foreach ($overrides as $configKey => $envKey) {
            if (array_key_exists($envKey, $env)) {
                $connectionConfig[$configKey] = $env[$envKey];
            }
        }

        config(["database.connections.$connection" => $connectionConfig]);
        config(['database.default' => $connection]);

        DB::purge($connection);
        DB::reconnect($connection);

        return true;
    }

    private function readDotEnv(string $path): array
    {
        $values = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = substr($line, 7);
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($value !== '') {
                $first = $value[0];
                $last = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === '\'' && $last === '\'')) {
                    $value = substr($value, 1, -1);
                }
            }

            $values[$key] = $value;
        }

        return $values;
    }
}
