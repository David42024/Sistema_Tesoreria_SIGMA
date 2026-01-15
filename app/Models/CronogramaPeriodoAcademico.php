<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class CronogramaPeriodoAcademico extends Model
{
    use HasCompositeKey;
    use HasFactory;

    protected $table = 'cronograma_periodo_academico';
    protected $primaryKey = ['id_periodo_academico', 'id_tipo_etapa_pa'];
    public $timestamps = false;

    protected $fillable = [
        'id_periodo_academico',
        'id_tipo_etapa_pa',
        'id_estado_etapa_pa',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function esAplicable()
    {
        return $this->estaActivo() && $this->estaEnFecha();
    }

    public function estaActivo()
    {
        return $this->estadoEtapaCronogramaPeriodoAcademico->esActivo();
    }


    public function estaEnFecha(?Carbon $fecha = null)
    {
        $fecha = $fecha ?? now();
        $fechaInicio = Carbon::parse($this->fecha_inicio);
        $fechaFin = Carbon::parse($this->fecha_fin);

        return $fecha->between($fechaInicio, $fechaFin);
    }

    public static function obtener(int $idPeriodo, int $idTipoEtapa)
    {
        return self::where('id_periodo_academico', $idPeriodo)
            ->where('id_tipo_etapa_pa', $idTipoEtapa)
            ->first();
    }

    public function periodoAcademico()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'id_periodo_academico');
    }

    public function tipoEtapaPeriodoAcademico()
    {
        return $this->belongsTo(TipoEtapaPeriodoAcademico::class, 'id_tipo_etapa_pa');
    }

    public function estadoEtapaCronogramaPeriodoAcademico()
    {
        return $this->belongsTo(EstadoEtapaCronogramaPeriodoAcademico::class, 'id_estado_etapa_pa');
    }
}
