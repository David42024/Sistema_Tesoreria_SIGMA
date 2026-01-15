<?php

namespace App\Services\Contable;

use App\Interfaces\ICronogramaAcademicoService;
use App\Interfaces\IRegistroPagosService;
use App\Models\Alumno;
use App\Models\Deuda;
use App\Models\PeriodoAcademico;

class RegistroPagosService implements IRegistroPagosService
{
    protected ICronogramaAcademicoService $cronogramaService;

    public function __construct(ICronogramaAcademicoService $cronogramaService)
    {
        $this->cronogramaService = $cronogramaService;
    }

    public function poseeDeudaSinPagarEnAñoAcademico(Alumno $alumno, ?PeriodoAcademico $periodoAcademico = null): bool
    {
        throw new \Exception("No implementado");
    }

    public function poseeDeudaSinPagar(Alumno $alumno): bool
    {
        return $this->obtenerDeudasSinPagar($alumno)->isNotEmpty();
    }

    public function obtenerDeudasSinPagarEnAñoAcademico(Alumno $alumno, ?PeriodoAcademico $periodoAcademico = null)
    {
        throw new \Exception("No implementado");
    }

    public function obtenerDeudasSinPagar(Alumno $alumno, ?PeriodoAcademico $periodoAcademico = null)
    {
        return Deuda::where('id_alumno', '=', $alumno->getKey())
            ->where('monto_total', '<>', 'monto_adelantado')
            ->get();
    }
}