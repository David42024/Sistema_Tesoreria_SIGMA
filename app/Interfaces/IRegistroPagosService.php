<?php

namespace App\Interfaces;

use App\Models\Alumno;
use App\Models\PeriodoAcademico;

interface IRegistroPagosService
{
    public function poseeDeudaSinPagarEnAñoAcademico(Alumno $alumno, ?PeriodoAcademico $periodoAcademico = null): bool;
    public function poseeDeudaSinPagar(Alumno $alumno): bool;
    public function obtenerDeudasSinPagarEnAñoAcademico(Alumno $alumno, ?PeriodoAcademico $periodoAcademico = null);
    public function obtenerDeudasSinPagar(Alumno $alumno, ?PeriodoAcademico $periodoAcademico = null);
}