<?php

namespace App\Services\Matricula;

use App\Interfaces\IGeneraConstancia;
use App\Interfaces\IGeneraConstanciaComoResponse;
use App\Models\Matricula;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Auth;

class GeneraConstanciaMatricula implements IGeneraConstancia, IGeneraConstanciaComoResponse
{
    private string $template;

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    private function getDefaultOptions(): array
    {
        $options = [
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ];

        return $options;
    }

    private function generarPDF(Matricula $matricula, User $operador)
    {
        $alumno = $matricula->alumno;
        $grado = $matricula->grado;
        $nivel_educativo = $grado->nivelEducativo;

        $admin = $operador->administrativo;

        $pdf = SnappyPdf::loadView($this->template, [
            'operator_name' => join(' ', [$admin->apellido_paterno, $admin->primer_nombre]),
            'operator_username' => $operador->username,
            'student_dni' => $alumno->dni,
            'student_name' => join(' ', [$alumno->apellido_paterno, $alumno->apellido_materno, $alumno->primer_nombre, $alumno->otros_nombres]),
            'year' => $matricula->año_escolar,
            'level' => $nivel_educativo->nombre_nivel,
            'grade' => $grado->nombre_grado,
            'section' => $matricula->seccion->nombreSeccion,
        ]);

        $pdf->setOptions([
            'page-size' => 'A4',
            'viewport-size' => '1280x1024', // Standard viewport
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'orientation' => 'Portrait',
            // Override global settings to ensure standard A4 behavior
            'disable-smart-shrinking' => true,
            'dpi' => 96,
            'zoom' => 1,
            'enable-local-file-access' => true,
        ]);

        return $pdf;
    }

    private function obtenerMatricula(int $id): Matricula|null
    {
        return Matricula::find($id);
    }

    public function generar(int $id)
    {
        $matricula = $this->obtenerMatricula($id);
        $operador = Auth::user();

        if ($matricula === null) {
            abort(400, "La matrícula especificada no existe.");
        }

        return $this->generarPDF($matricula, $operador);
    }

    public function generarAsResponse(int $id)
    {
        return $this->generar($id)->stream('constancia_matricula_' . $id . '.pdf');
    }
}