<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Administrativo;
use App\Models\Personal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserEditTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true; // Seedear automáticamente

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario director para autenticación con username único por test
        $director = User::factory()->create([
            'username' => 'director_' . uniqid(),
            'password' => bcrypt('12345'),
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $adminDirector = Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => uniqid('dni_')
        ]);

        $this->actingAs($director);
    }

    /** @test */
    public function puede_cambiar_estado_de_usuario()
    {
        // Crear usuario activo
        $usuario = User::factory()->create([
            'username' => 'usuario_test',
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $admin = Administrativo::factory()->create([
            'id_usuario' => $usuario->id_usuario,
            'cargo' => 'Secretaria',
            'dni' => '87654321'
        ]);

        // Cambiar estado a inactivo
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => $usuario->username,
            'tipo' => 'Administrativo',
            'estado' => '0', // Inactivo
            'id_vinculado' => $admin->id_administrativo
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'estado' => 0
        ]);
    }

    /** @test */
    public function puede_cambiar_tipo_de_usuario_administrativo_a_personal()
    {
        // Crear usuario administrativo
        $usuario = User::factory()->create([
            'username' => 'admin_test',
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $admin = Administrativo::factory()->create([
            'id_usuario' => $usuario->id_usuario,
            'cargo' => 'Secretaria',
            'dni' => '11111111'
        ]);

        // Crear personal para vincular
        $personal = Personal::factory()->create([
            'codigo_personal' => 'PER001',
            'dni' => '22222222'
        ]);

        // Cambiar tipo a Personal
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => $usuario->username,
            'tipo' => 'Personal',
            'estado' => '1',
            'id_vinculado' => $personal->codigo_personal
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'tipo' => 'Personal'
        ]);

        $this->assertDatabaseHas('personal', [
            'codigo_personal' => $personal->codigo_personal,
            'id_usuario' => $usuario->id_usuario
        ]);
    }

    /** @test */
    public function puede_cambiar_username()
    {
        // Crear usuario
        $usuario = User::factory()->create([
            'username' => 'usuario_viejo',
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $admin = Administrativo::factory()->create([
            'id_usuario' => $usuario->id_usuario,
            'cargo' => 'Secretaria',
            'dni' => '33333333'
        ]);

        // Cambiar username
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => 'usuario_nuevo',
            'tipo' => 'Administrativo',
            'estado' => '1',
            'id_vinculado' => $admin->id_administrativo
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'username' => 'usuario_nuevo'
        ]);
    }

    /** @test */
    public function puede_editar_y_activar_usuario_inactivo()
    {
        // Crear usuario inactivo
        $usuario = User::factory()->create([
            'username' => 'usuario_inactivo',
            'tipo' => 'Administrativo',
            'estado' => false
        ]);

        $admin = Administrativo::factory()->create([
            'id_usuario' => $usuario->id_usuario,
            'cargo' => 'Secretaria',
            'dni' => '44444444'
        ]);

        // Activar usuario
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => $usuario->username,
            'tipo' => 'Administrativo',
            'estado' => '1', // Activar
            'id_vinculado' => $admin->id_administrativo
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'estado' => 1
        ]);
    }

    /** @test */
    public function puede_cambiar_vinculacion_entre_administrativos()
    {
        // Crear usuario administrativo
        $usuario = User::factory()->create([
            'username' => 'admin_cambio',
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $adminViejo = Administrativo::factory()->create([
            'id_usuario' => $usuario->id_usuario,
            'cargo' => 'Secretaria',
            'dni' => '55555555'
        ]);

        // Crear nuevo administrativo para vincular
        $adminNuevo = Administrativo::factory()->create([
            'cargo' => 'Coordinador',
            'dni' => '66666666'
        ]);

        // Cambiar vinculación
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => $usuario->username,
            'tipo' => 'Administrativo',
            'estado' => '1',
            'id_vinculado' => $adminNuevo->id_administrativo
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('administrativos', [
            'id_administrativo' => $adminNuevo->id_administrativo,
            'id_usuario' => $usuario->id_usuario
        ]);
    }

    /** @test */
    public function validacion_username_unico_al_editar()
    {
        // Crear dos usuarios
        $usuario1 = User::factory()->create(['username' => 'usuario1']);
        $usuario2 = User::factory()->create(['username' => 'usuario2']);

        $admin1 = Administrativo::factory()->create([
            'id_usuario' => $usuario1->id_usuario,
            'dni' => '77777777'
        ]);

        // Intentar cambiar username de usuario1 al de usuario2
        $response = $this->patch(route('usuario_editEntry', $usuario1->id_usuario), [
            'username' => 'usuario2', // Ya existe
            'tipo' => 'Administrativo',
            'estado' => '1',
            'id_vinculado' => $admin1->id_administrativo
        ]);

        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function puede_mantener_mismo_username_al_editar_otros_campos()
    {
        // Crear usuario
        $usuario = User::factory()->create([
            'username' => 'usuario_mismo',
            'estado' => true
        ]);

        $admin = Administrativo::factory()->create([
            'id_usuario' => $usuario->id_usuario,
            'dni' => '88888888'
        ]);

        // Editar manteniendo el mismo username
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => 'usuario_mismo', // Mismo username
            'tipo' => 'Administrativo',
            'estado' => '0', // Cambiar solo el estado
            'id_vinculado' => $admin->id_administrativo
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'username' => 'usuario_mismo',
            'estado' => 0
        ]);
    }
}
