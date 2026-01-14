<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';   
    protected $primaryKey = 'id_matricula';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'id_alumno',
        'fecha_matricula',
        'escala',
        'observaciones',
        'id_grado',
        'nombreSeccion',
        'tipo', 
        'estado',
        'id_periodo_academico'
    ];

    public function periodoAcademico()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'id_periodo_academico', 'id_periodo_academico');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'nombreSeccion', 'nombreSeccion');
    }

    protected function casts(): array
    {
        return [
            'fecha_matricula' => 'date',    
            'estado' => 'boolean',          
        ];
    }

    public function generarDeudas()
    {
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SETIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];

        // Obtener el año del periodo académico
        $anioEscolar = $this->periodoAcademico ? (int)$this->periodoAcademico->nombre : Carbon::now()->year;
        $escala = $this->escala;
        $idAlumno = $this->id_alumno;

        $anioActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;

        // Determinar el mes de inicio según las reglas del negocio:
        // 1. Las clases empiezan en marzo
        // 2. Si estamos matriculando para un año futuro, generar todas las deudas (marzo-diciembre)
        // 3. Si estamos en enero o febrero del mismo año, empezar desde marzo
        // 4. Si estamos en marzo-diciembre del mismo año, empezar desde el mes actual
        if ($anioEscolar > $anioActual) {
            // Matrícula para año futuro: generar todas las deudas desde marzo
            $mesInicio = 3;
        } elseif ($mesActual < 3) {
            // Estamos en enero o febrero: empezar desde marzo
            $mesInicio = 3;
        } else {
            // Estamos en marzo-diciembre: empezar desde el mes actual
            $mesInicio = $mesActual;
        }

        for ($mes = $mesInicio; $mes <= 12; $mes++) {
            $nombreMes = $meses[$mes];

            // Buscar concepto de pago con el formato correcto: "OCTUBRE 2025" y escala "A"
            $concepto = ConceptoPago::where('descripcion', "$nombreMes $anioEscolar")
                ->where('escala', $escala)
                ->where('estado', true)
                ->first();

            if (!$concepto) {
                throw new \Exception("No existe concepto de pago para $nombreMes $anioEscolar escala $escala");
            }

            $fechaLimite = Carbon::createFromDate($anioEscolar, $mes, 1)->endOfMonth()->format('Y-m-d');

            Deuda::create([
                'id_alumno' => $idAlumno,
                'id_concepto' => $concepto->id_concepto,
                'fecha_limite' => $fechaLimite,
                'monto_total' => $concepto->monto,
                'periodo' => "$nombreMes $anioEscolar",
                'monto_a_cuenta' => 0,
                'monto_adelantado' => 0,
                'observacion' => null,
                'estado' => true
            ]);
        }
    }


}
