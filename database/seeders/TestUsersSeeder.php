<?php

namespace Database\Seeders;

use App\Models\Administrativo;
use App\Models\ComposicionFamiliar;
use App\Models\Familiar;
use App\Models\User;
use App\Observers\Traits\LogsActions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deshabilitamos temporalmente el registro de acciones, ya que estamos ejecutando un seeder.
        LogsActions::disable();

        // Verificar si ya existen los usuarios antes de crearlos
        if (!User::where('username', 'director')->exists()) {
            Administrativo::create([
                'id_usuario' => User::factory([
                    'username' => 'director',
                    'password' => bcrypt("12345"),
                    'tipo' => 'Administrativo',
                ])->create()->id_usuario,

                'apellido_paterno' => fake()->lastName(),
                'apellido_materno' => fake()->lastName(),
                'primer_nombre' => fake()->firstName(),
                'otros_nombres' => fake()->randomElement([fake()->name(), fake()->name() . " " . fake()->name()]),
                'dni' => fake()->numberBetween(10000000, 99999999),
                'direccion' => fake()->address(),
                'estado_civil' => fake()->randomElement(['S', 'C', 'V', 'D']),
                'telefono' => fake()->phoneNumber(),
                'seguro_social' => fake()->numberBetween('1000000000'),
                'fecha_ingreso' => fake()->dateTimeThisDecade(),
                'cargo' => 'Director',
                'sueldo' => fake()->numberBetween(5000, 10000),
                'estado' => '1'
            ]);
        }

        if (!User::where('username', 'secretaria')->exists()) {
            Administrativo::create([
                'id_usuario' => User::factory([
                    'username' => 'secretaria',
                    'password' => bcrypt("12345"),
                    'tipo' => 'Administrativo',
                ])->create()->id_usuario,

                'apellido_paterno' => fake()->lastName(),
                'apellido_materno' => fake()->lastName(),
                'primer_nombre' => fake()->firstName(),
                'otros_nombres' => fake()->randomElement([fake()->name(), fake()->name() . " " . fake()->name()]),
                'dni' => fake()->numberBetween(10000000, 99999999),
                'direccion' => fake()->address(),
                'estado_civil' => fake()->randomElement(['S', 'C', 'V', 'D']),
                'telefono' => fake()->phoneNumber(),
                'seguro_social' => fake()->numberBetween('1000000000'),
                'fecha_ingreso' => fake()->dateTimeThisDecade(),
                'cargo' => 'Secretaria',
                'sueldo' => fake()->numberBetween(1500, 3500),
                'estado' => '1'
            ]);
        }

        ComposicionFamiliar::factory([
            'id_familiar' => Familiar::factory([
                'id_usuario' => User::factory([
                    'username' => 'familiar',
                    'password' => bcrypt("12345"),
                    'tipo' => 'Administrativo',
                ])->create()->id_usuario
            ])->create()->id_familiar
        ]);

        // Restablecemos el registro de acciones.
        LogsActions::enable();
    }
}
