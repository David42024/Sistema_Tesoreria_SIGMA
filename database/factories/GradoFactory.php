<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\NivelEducativo;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grado>
 */
class GradoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nivel = NivelEducativo::inRandomOrder()->first();
        
        if (!$nivel) {
            // Si no hay niveles, crear uno por defecto
            $nivel = NivelEducativo::factory()->primaria()->create();
        }
        
        // Determinar grados según el nivel
        $grados = $this->getGradosPorNivel($nivel->nombre_nivel);
        
        return [
            'id_nivel' => $nivel->id_nivel,
            'nombre_grado' => fake()->randomElement($grados),
        ];
    }

    /**
     * Obtiene los grados válidos para un nivel educativo
     */
    private function getGradosPorNivel(string $nombreNivel): array
    {
        return match(strtoupper($nombreNivel)) {
            'INICIAL' => ['3 AÑOS', '4 AÑOS', '5 AÑOS'],
            'PRIMARIA' => ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO'],
            'SECUNDARIA' => ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO'],
            default => ['PRIMERO', 'SEGUNDO', 'TERCERO'],
        };
    }

    /**
     * Estado para grados de Inicial
     */
    public function inicial(): static
    {
        return $this->state(function (array $attributes) {
            $nivel = NivelEducativo::where('nombre_nivel', 'INICIAL')->first() 
                ?? NivelEducativo::factory()->inicial()->create();
            
            return [
                'id_nivel' => $nivel->id_nivel,
                'nombre_grado' => fake()->randomElement(['3 AÑOS', '4 AÑOS', '5 AÑOS']),
            ];
        });
    }

    /**
     * Estado para grados de Primaria
     */
    public function primaria(): static
    {
        return $this->state(function (array $attributes) {
            $nivel = NivelEducativo::where('nombre_nivel', 'PRIMARIA')->first() 
                ?? NivelEducativo::factory()->primaria()->create();
            
            return [
                'id_nivel' => $nivel->id_nivel,
                'nombre_grado' => fake()->randomElement(['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO']),
            ];
        });
    }

    /**
     * Estado para grados de Secundaria
     */
    public function secundaria(): static
    {
        return $this->state(function (array $attributes) {
            $nivel = NivelEducativo::where('nombre_nivel', 'SECUNDARIA')->first() 
                ?? NivelEducativo::factory()->secundaria()->create();
            
            return [
                'id_nivel' => $nivel->id_nivel,
                'nombre_grado' => fake()->randomElement(['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO']),
            ];
        });
    }
}
