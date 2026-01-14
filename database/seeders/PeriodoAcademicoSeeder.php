<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use App\Models\PeriodoAcademico;
use Illuminate\Database\Seeder;

class PeriodoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear períodos académicos
        $periodos = [
            ['nombre' => '2024', 'estado' => 0],
            ['nombre' => '2025', 'estado' => 0],
            ['nombre' => '2026', 'estado' => 1], // Activo
            ['nombre' => '2027', 'estado' => 0],
        ];

        foreach ($periodos as $periodo) {
            PeriodoAcademico::firstOrCreate(
                ['nombre' => $periodo['nombre']],
                ['estado' => $periodo['estado']]
            );
        }

        // Establecer 2026 como período actual
        $periodo2026 = PeriodoAcademico::where('nombre', '2026')->first();

        if ($periodo2026) {
            Configuracion::establecer('ID_PERIODO_ACADEMICO_ACTUAL', $periodo2026->id_periodo_academico);
        }
    }
}