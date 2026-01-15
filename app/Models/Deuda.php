<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deuda extends Model
{
    use HasFactory;

    protected $table = 'deudas';

    protected $primaryKey = 'id_deuda';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_alumno',
        'id_concepto',
        'fecha_limite',
        'monto_total',
        'periodo',
        'monto_a_cuenta',
        'monto_adelantado',
        'observacion',
        'estado',
    ];


    protected function casts(): array
    {
        return [
            'fecha_limite' => 'date',
            'monto_total' => 'decimal:2',
            'monto_a_cuenta' => 'decimal:2',
            'monto_adelantado' => 'decimal:2',
            'estado' => 'boolean',
        ];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function concepto()
    {
        return $this->belongsTo(ConceptoPago::class, 'id_concepto');
    }

    public function conceptoPago()
    {
        return $this->belongsTo(\App\Models\ConceptoPago::class, 'id_concepto', 'id_concepto');
    }

    public function detallesOrdenPago()
    {
        return $this->hasMany(DetalleOrdenPago::class, 'id_deuda', 'id_deuda');
    }

    public function distribucionesPago()
    {
        return $this->hasMany(DistribucionPagoDeuda::class, 'id_deuda', 'id_deuda');
    }
}
