<?php

namespace Database\Factories;

use App\Models\Grado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seccion>
 */
class SeccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_grado'=> Grado::factory(),
            'nombreSeccion' => fake()->randomElement(['A', 'B', 'C', 'D', 'E']),
        ];
    }

    /**
     * Estado para sección específica
     */
    public function seccion(string $letra): static
    {
        return $this->state(fn (array $attributes) => [
            'nombreSeccion' => strtoupper($letra),
        ]);
    }
}
