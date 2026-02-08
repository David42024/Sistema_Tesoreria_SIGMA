<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Administrativo;
use App\Models\Personal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCRUDTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica si la página de listado de usuarios se carga correctamente
     */
    public function test_index_page_loads_successfully(): void
    {
        $user = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $response = $this->actingAs($user)->get(route('usuario_view'));

        $response->assertStatus(200);
    }

    /**
     * Test que verifica si se puede acceder a la página de creación de usuario
     */
    public function test_create_page_loads_successfully(): void
    {
        $user = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $response = $this->actingAs($user)->get(route('usuario_create'));

        $response->assertStatus(200);
    }

    /**
     * Test que verifica si se puede crear un usuario de tipo PreApoderado correctamente
     */
    public function test_can_create_pre_apoderado_user(): void
    {
        $user = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userData = [
            'username' => 'testuser123',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tipo' => 'PreApoderado'
        ];

        $response = $this->actingAs($user)->put(route('usuario_createNewEntry'), $userData);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser123',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $response->assertRedirect(route('usuario_view'));
    }

    /**
     * Test que verifica si se puede crear un usuario de tipo Administrativo
     */
    public function test_can_create_administrativo_user(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        // Crear un administrativo sin usuario vinculado
        $administrativo = Administrativo::factory()->create([
            'id_usuario' => null,
            'estado' => true
        ]);

        $userData = [
            'username' => 'admin123',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tipo' => 'Administrativo',
            'id_vinculado' => $administrativo->id_administrativo
        ];

        $response = $this->actingAs($authUser)->put(route('usuario_createNewEntry'), $userData);

        $this->assertDatabaseHas('users', [
            'username' => 'admin123',
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        // Verificar que el administrativo ahora tiene un usuario vinculado
        $administrativo->refresh();
        $this->assertNotNull($administrativo->id_usuario);

        $response->assertRedirect(route('usuario_view'));
    }

    /**
     * Test que verifica si se puede crear un usuario de tipo Personal
     */
    public function test_can_create_personal_user(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        // Crear un personal sin usuario vinculado
        $personal = Personal::factory()->create([
            'id_usuario' => null,
            'estado' => true
        ]);

        $userData = [
            'username' => 'personal123',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tipo' => 'Personal',
            'id_vinculado' => $personal->codigo_personal
        ];

        $response = $this->actingAs($authUser)->put(route('usuario_createNewEntry'), $userData);

        $this->assertDatabaseHas('users', [
            'username' => 'personal123',
            'tipo' => 'Personal',
            'estado' => true
        ]);

        // Verificar que el personal ahora tiene un usuario vinculado
        $personal->refresh();
        $this->assertNotNull($personal->id_usuario);

        $response->assertRedirect(route('usuario_view'));
    }

    /**
     * Test que verifica validación de username único
     */
    public function test_username_must_be_unique(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        // Crear un usuario existente
        User::factory()->create([
            'username' => 'existinguser',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $userData = [
            'username' => 'existinguser', // Username duplicado
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tipo' => 'PreApoderado'
        ];

        $response = $this->actingAs($authUser)->put(route('usuario_createNewEntry'), $userData);

        $response->assertSessionHasErrors(['username']);
    }

    /**
     * Test que verifica validación de password confirmation
     */
    public function test_password_must_be_confirmed(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword', // No coincide
            'tipo' => 'PreApoderado'
        ];

        $response = $this->actingAs($authUser)->put(route('usuario_createNewEntry'), $userData);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test que verifica si se puede editar un usuario correctamente
     */
    public function test_can_edit_user(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userToEdit = User::factory()->create([
            'username' => 'oldusername',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $response = $this->actingAs($authUser)->get(route('usuario_edit', ['id' => $userToEdit->id_usuario]));

        $response->assertStatus(200);
        $response->assertSee('oldusername');
    }

    /**
     * Test que verifica si se puede actualizar un usuario correctamente
     */
    public function test_can_update_user(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userToEdit = User::factory()->create([
            'username' => 'oldusername',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $updateData = [
            'username' => 'newusername',
            'tipo' => 'PreApoderado',
            'estado' => '0' // Cambiar a inactivo
        ];

        $response = $this->actingAs($authUser)->patch(
            route('usuario_editEntry', ['id' => $userToEdit->id_usuario]),
            $updateData
        );

        $this->assertDatabaseHas('users', [
            'id_usuario' => $userToEdit->id_usuario,
            'username' => 'newusername',
            'estado' => false
        ]);

        $response->assertRedirect(route('usuario_view'));
    }

    /**
     * Test que verifica si se puede eliminar (desactivar) un usuario
     */
    public function test_can_delete_user(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userToDelete = User::factory()->create([
            'username' => 'usertodelete',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $response = $this->actingAs($authUser)->delete(route('usuario_delete'), [
            'id' => $userToDelete->id_usuario
        ]);

        $this->assertDatabaseHas('users', [
            'id_usuario' => $userToDelete->id_usuario,
            'estado' => false // Debe estar desactivado
        ]);

        $response->assertRedirect(route('usuario_view'));
    }

    /**
     * Test que verifica si se puede acceder a la página de cambio de contraseña
     */
    public function test_change_password_page_loads(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userToChange = User::factory()->create([
            'username' => 'usertochange',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $response = $this->actingAs($authUser)->get(
            route('usuario_change_password', ['id' => $userToChange->id_usuario])
        );

        $response->assertStatus(200);
        $response->assertSee('usertochange');
    }

    /**
     * Test que verifica si se puede cambiar la contraseña de un usuario
     */
    public function test_can_change_user_password(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $userToChange = User::factory()->create([
            'username' => 'usertochange',
            'password' => Hash::make('oldpassword'),
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $passwordData = [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->actingAs($authUser)->post(
            route('usuario_update_password', ['id' => $userToChange->id_usuario]),
            $passwordData
        );

        // Verificar que la contraseña se actualizó
        $userToChange->refresh();
        $this->assertTrue(Hash::check('newpassword123', $userToChange->password));

        $response->assertRedirect(route('usuario_view', ['password_changed' => true]));
    }

    /**
     * Test que verifica búsqueda de usuarios
     */
    public function test_can_search_users(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        // Crear varios usuarios
        User::factory()->create([
            'username' => 'john_doe',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        User::factory()->create([
            'username' => 'jane_smith',
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $response = $this->actingAs($authUser)->get(route('usuario_view', [
            'search' => 'john'
        ]));

        $response->assertStatus(200);
        $response->assertSee('john_doe');
    }

    /**
     * Test que verifica exportación de usuarios
     */
    public function test_can_export_users(): void
    {
        $authUser = User::factory()->create([
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        // Crear algunos usuarios
        User::factory()->count(3)->create([
            'tipo' => 'PreApoderado',
            'estado' => true
        ]);

        $response = $this->actingAs($authUser)->get(route('usuario_export', [
            'format' => 'xlsx'
        ]));

        $response->assertStatus(200);
    }
}
