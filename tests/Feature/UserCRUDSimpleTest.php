<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCRUDSimpleTest extends TestCase
{
    /**
     * Test básico que verifica si existe la ruta de usuarios
     */
    public function test_usuarios_route_exists(): void
    {
        // Crear un usuario directamente sin factory
        $user = User::where('estado', true)->first();

        if (!$user) {
            $user = new User();
            $user->username = 'test_admin_' . time();
            $user->password = Hash::make('password');
            $user->tipo = 'Administrativo';
            $user->estado = true;
            $user->save();
        }

        $response = $this->actingAs($user)->get('/usuarios');

        // La respuesta debe ser 200 (OK) o 403 (Forbidden por permisos)
        $this->assertContains($response->status(), [200, 403]);
    }

    /**
     * Test que verifica si existe la ruta de creación
     */
    public function test_create_route_exists(): void
    {
        $user = User::where('estado', true)->first();

        if (!$user) {
            $user = new User();
            $user->username = 'test_admin_' . time();
            $user->password = Hash::make('password');
            $user->tipo = 'Administrativo';
            $user->estado = true;
            $user->save();
        }

        $response = $this->actingAs($user)->get('/usuarios/crear');

        // La respuesta debe ser 200 (OK) o 403 (Forbidden por permisos)
        $this->assertContains($response->status(), [200, 403]);
    }

    /**
     * Test que verifica que el controlador UserController existe
     */
    public function test_user_controller_exists(): void
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\UserController::class));
    }

    /**
     * Test que verifica que el modelo User tiene los campos necesarios
     */
    public function test_user_model_has_required_fields(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('tipo', $fillable);
        $this->assertContains('estado', $fillable);
    }

    /**
     * Test que verifica que existen las vistas necesarias
     */
    public function test_user_views_exist(): void
    {
        $this->assertTrue(view()->exists('gestiones.usuario.create'));
        $this->assertTrue(view()->exists('gestiones.usuario.edit'));
        $this->assertTrue(view()->exists('gestiones.usuario.change_password'));
    }

    /**
     * Test que verifica que las rutas están registradas
     */
    public function test_routes_are_registered(): void
    {
        $this->assertTrue(route('usuario_view') !== null);
        $this->assertTrue(route('usuario_create') !== null);
        $this->assertTrue(route('usuario_createNewEntry') !== null);
    }
}
