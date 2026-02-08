<?php

namespace Tests\Feature;

use App\Models\CronogramaPeriodoAcademico;
use App\Models\EstadoEtapaCronogramaPeriodoAcademico;
use App\Models\EstadoPeriodoAcademico;
use App\Models\PeriodoAcademico;
use App\Models\TipoEtapaPeriodoAcademico;
use App\Services\Cronograma\CronogramaAcademicoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        DB::table('estado_periodo_academico')->insert([
            'id_estado_periodo_academico' => EstadoPeriodoAcademico::PROGRAMADO,
            'nombre' => 'Programado',
        ]);

        DB::table('tipo_etapa_cronograma_periodo_academico')->insert([
            'id_tipo_etapa_pa' => TipoEtapaPeriodoAcademico::PREMATRICULA,
            'nombre' => 'Prematricula',
        ]);

        DB::table('estado_etapa_cronograma_periodo_academico')->insert([
            'id_estado_etapa_pa' => EstadoEtapaCronogramaPeriodoAcademico::ACTIVO,
            'nombre' => 'Activo',
        ]);

        $periodo = PeriodoAcademico::create([
            'nombre' => '2026',
            'id_estado_periodo_academico' => EstadoPeriodoAcademico::PROGRAMADO,
        ]);

        CronogramaPeriodoAcademico::create([
            'id_periodo_academico' => $periodo->id_periodo_academico,
            'id_tipo_etapa_pa' => TipoEtapaPeriodoAcademico::PREMATRICULA,
            'id_estado_etapa_pa' => EstadoEtapaCronogramaPeriodoAcademico::ACTIVO,
            'fecha_inicio' => now()->subDay(),
            'fecha_fin' => now()->addDay(),
        ]);

        CronogramaAcademicoService::establecerPeriodo($periodo);

        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
