<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NivelEducativo>
 */
class NivelEducativoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_nivel' => fake()->randomElement(['INICIAL', 'PRIMARIA', 'SECUNDARIA']),
            'descripcion' => fake()->optional()->sentence()
        ];
    }

    /**
     * Estado específico para Inicial
     */
    public function inicial(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_nivel' => 'INICIAL',
            'descripcion' => 'Educación Inicial'
        ]);
    }

    /**
     * Estado específico para Primaria
     */
    public function primaria(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_nivel' => 'PRIMARIA',
            'descripcion' => 'Educación Primaria'
        ]);
    }

    /**
     * Estado específico para Secundaria
     */
    public function secundaria(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_nivel' => 'SECUNDARIA',
            'descripcion' => 'Educación Secundaria'
        ]);
    }
}
