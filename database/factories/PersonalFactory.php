<?php

namespace Database\Factories;

use App\Models\DepartamentoAcademico;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personal>
 */
class PersonalFactory extends Factory
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
            'codigo_personal' => fake()->unique()->numberBetween(1000, 9999),
            'apellido_paterno' => $fatherLastName,
            'apellido_materno' => fake()->lastName(),
            'primer_nombre' => $firstName,
            'otros_nombres' => fake()->randomElement([fake()->name(), fake()->name() . " " . fake()->name()]),
            'dni' => fake()->numberBetween(10000000, 99999999),
            'direccion' => fake()->optional()->address(),
            'estado_civil' => fake()->randomElement(['S', 'C', 'V', 'D']),
            'telefono' => fake()->optional()->phoneNumber(),
            'seguro_social' => fake()->optional()->numberBetween(1000000000, 9999999999),
            'fecha_ingreso' => fake()->dateTimeThisDecade(),
            'departamento' => fake()->randomElement(['DIRECCION', 'PRIMARIA', 'SECUNDARIA']),
            'categoria' => fake()->randomElement(['ADMINISTRATIVO', 'DOCENTE', 'DIRECTOR']),
            'id_departamento' => DepartamentoAcademico::factory(),
            'estado' => '1',
        ];
    }
}
