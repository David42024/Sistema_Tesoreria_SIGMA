<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Administrativo>
 */
class AdministrativoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $fatherLastName = fake()->lastName();
        return [
            'id_usuario' => User::factory(),
            'apellido_paterno' => $fatherLastName,
            'apellido_materno' => fake()->lastName(),
            'primer_nombre' => $firstName,
            'otros_nombres' => fake()->randomElement([fake()->name(), fake()->name() . " " . fake()->name()]),
            'dni' => fake()->numberBetween(10000000, 99999999),
            'direccion' => fake()->address(),
            'estado_civil' => fake()->randomElement(['S', 'C', 'V', 'D']),
            'telefono' => fake()->phoneNumber(),
            'seguro_social' => fake()->numberBetween('1000000000'),
            'fecha_ingreso' => fake()->dateTimeThisDecade(),
            'cargo' => fake()->randomElement(['SECRETARIO/A', 'DECANO', 'SECRETARIO/A', 'SECRETARIO/A', 'AUDITOR']),
            'sueldo' => fake()->numberBetween(1000, 3500),
            'estado' => '1'
        ];
    }
}
