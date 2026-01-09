<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Administrativo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserCreationManualTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function director_puede_crear_usuario_con_password()
    {
        // 1. Crear el director con credenciales exactas
        $director = User::create([
            'username' => 'director',
            'password' => Hash::make('12345'),
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        $adminDirector = Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        // 2. Autenticarse como director
        $this->actingAs($director);

        // 3. Visitar la página de crear usuario
        $response = $this->get(route('usuario_create'));
        $response->assertStatus(200);
        $response->assertSee('Nuevo Usuario');
        $response->assertSee('Contraseña');

        echo "\n✓ Página de creación cargada correctamente\n";

        // 4. Intentar crear un nuevo usuario (simulando el formulario exacto)
        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => 'jeancito',
            'tipo' => 'Personal',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        // Verificar que redirige correctamente
        $response->assertRedirect(route('usuario_view'));
        $response->assertSessionHasNoErrors();

        echo "✓ Usuario creado sin errores de validación\n";

        // 5. Verificar que el usuario fue creado en la base de datos
        $this->assertDatabaseHas('users', [
            'username' => 'jeancito',
            'tipo' => 'Personal',
            'estado' => 1
        ]);

        echo "✓ Usuario 'jeancito' existe en la base de datos\n";

        // 6. Verificar que la contraseña fue hasheada correctamente
        $nuevoUsuario = User::where('username', 'jeancito')->first();
        $this->assertNotNull($nuevoUsuario);
        $this->assertTrue(Hash::check('123456', $nuevoUsuario->password));

        echo "✓ Contraseña hasheada correctamente\n";

        echo "\n✅ TEST PASADO: El usuario se creó correctamente con todos sus datos\n";
    }

    /** @test */
    public function valida_password_cuando_falta()
    {
        $director = User::create([
            'username' => 'director',
            'password' => Hash::make('12345'),
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        // Intentar crear sin password
        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => 'sin_password',
            'tipo' => 'Personal'
        ]);

        $response->assertSessionHasErrors('password');
        echo "\n✓ Valida correctamente cuando falta la contraseña\n";
    }

    /** @test */
    public function valida_password_confirmation()
    {
        $director = User::create([
            'username' => 'director',
            'password' => Hash::make('12345'),
            'tipo' => 'Administrativo',
            'estado' => true
        ]);

        Administrativo::factory()->create([
            'id_usuario' => $director->id_usuario,
            'cargo' => 'Director',
            'dni' => '12345678'
        ]);

        $this->actingAs($director);

        // Intentar crear con passwords diferentes
        $response = $this->put(route('usuario_createNewEntry'), [
            'username' => 'passwords_diferentes',
            'tipo' => 'Personal',
            'password' => '123456',
            'password_confirmation' => '654321'
        ]);

        $response->assertSessionHasErrors('password');
        echo "\n✓ Valida correctamente cuando las contraseñas no coinciden\n";
    }
}
