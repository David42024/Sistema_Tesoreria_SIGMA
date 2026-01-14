<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    public $incrementing = false;

    protected $fillable = [
        'id_grado',
        'nombreSeccion',
        'capacidad_maxima',
        'estado'
    ];

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    public function niveleducativo(){
        return $this->hasOneThrough(
            NivelEducativo::class,
            Grado::class,
            'id_grado',
            'id_nivel',
            'id_grado',
            'id_nivel'
        );
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'id_grado', 'id_grado')
                    ->where('nombreSeccion', $this->nombreSeccion);
    }

    public function catedras()
    {
        return $this->hasMany(Catedra::class, 'id_grado', 'id_grado')
                    ->where('secciones_nombreSeccion', $this->nombreSeccion);
    }
    
    public static function findByCompositeKeyOrFail($idGrado, $nombreSeccion)
    {
        return self::where('id_grado', $idGrado)
                  ->where('nombreSeccion', $nombreSeccion)
                  ->firstOrFail();
    }

    /**
     * Obtener el número de alumnos matriculados en esta sección para un periodo específico
     */
    public function getAlumnosMatriculadosCount($idPeriodoAcademico)
    {
        return Matricula::where('id_grado', $this->id_grado)
                        ->where('nombreSeccion', $this->nombreSeccion)
                        ->where('id_periodo_academico', $idPeriodoAcademico)
                        ->where('estado', 1)
                        ->count();
    }

    /**
     * Obtener las vacantes disponibles para un periodo específico
     */
    public function getVacantesDisponibles($idPeriodoAcademico)
    {
        $matriculados = $this->getAlumnosMatriculadosCount($idPeriodoAcademico);
        return $this->capacidad_maxima - $matriculados;
    }

    /**
     * Verificar si la sección tiene vacantes disponibles
     */
    public function tieneVacantes($idPeriodoAcademico)
    {
        return $this->getVacantesDisponibles($idPeriodoAcademico) > 0;
    }

    //public function alumnos()
    //{
     //   return $this->hasMany(Alumno::class)
    //}

}
