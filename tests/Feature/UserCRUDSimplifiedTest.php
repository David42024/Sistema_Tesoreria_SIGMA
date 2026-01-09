<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Administrativo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserCRUDSimplifiedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_usuario_sin_vinculacion()
    {
        // Crear usuario director para autenticación
        $director = User::factory()->create([
            'username' => 'director_test',
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        // Crear nuevo usuario sin vinculación
        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => 'nuevo_usuario',
            'password' => '123456',
            'password_confirmation' => '123456',
            'tipo' => 'Personal'
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'username' => 'nuevo_usuario',
            'tipo' => 'Personal',
            'estado' => 1
        ]);
    }

    /** @test */
    public function puede_editar_usuario_sin_vinculacion()
    {
        // Crear usuario director
        $director = User::factory()->create([
            'username' => 'director_test',
            'tipo' => 'Administrativo'
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        // Crear usuario a editar
        $usuario = User::factory()->create([
            'username' => 'usuario_editar',
            'tipo' => 'Personal',
            'estado' => true
        ]);

        // Editar usuario
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => 'usuario_editado',
            'tipo' => 'Administrativo',
            'estado' => '0'
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'username' => 'usuario_editado',
            'tipo' => 'Administrativo',
            'estado' => 0
        ]);
    }

    /** @test */
    public function valida_username_unico_al_crear()
    {
        $director = User::factory()->create([
            'username' => 'director_test',
            'tipo' => 'Administrativo'
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        // Crear un usuario
        User::factory()->create(['username' => 'usuario_existente']);

        // Intentar crear otro con el mismo username
        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => 'usuario_existente',
            'password' => '123456',
            'password_confirmation' => '123456',
            'tipo' => 'Personal'
        ]);

        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function valida_password_confirmation()
    {
        $director = User::factory()->create([
            'username' => 'director_test',
            'tipo' => 'Administrativo'
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        // Intentar crear usuario con contraseñas diferentes
        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => 'nuevo_usuario',
            'password' => '123456',
            'password_confirmation' => '654321',
            'tipo' => 'Personal'
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function puede_cambiar_estado_de_usuario()
    {
        $director = User::factory()->create([
            'username' => 'director_test',
            'tipo' => 'Administrativo'
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        $usuario = User::factory()->create([
            'username' => 'usuario_test',
            'estado' => true
        ]);

        // Cambiar a inactivo
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => 'usuario_test',
            'tipo' => $usuario->tipo,
            'estado' => '0'
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'estado' => 0
        ]);
    }

    /** @test */
    public function puede_cambiar_tipo_de_usuario()
    {
        $director = User::factory()->create([
            'username' => 'director_test',
            'tipo' => 'Administrativo'
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        $usuario = User::factory()->create([
            'username' => 'usuario_test',
            'tipo' => 'Personal',
            'estado' => true
        ]);

        // Cambiar tipo
        $response = $this->patch(route('usuario_editEntry', $usuario->id_usuario), [
            'username' => 'usuario_test',
            'tipo' => 'Administrativo',
            'estado' => '1'
        ]);

        $response->assertRedirect(route('usuario_view'));

        $this->assertDatabaseHas('users', [
            'id_usuario' => $usuario->id_usuario,
            'tipo' => 'Administrativo'
        ]);
    }
}
