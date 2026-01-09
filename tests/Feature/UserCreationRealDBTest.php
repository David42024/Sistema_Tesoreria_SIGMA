<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserCreationRealDBTest extends TestCase
{
    /** @test */
    public function test_crear_usuario_en_bd_real()
    {
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
}
