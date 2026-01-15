<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    
    use HasFactory;

    protected $table = 'pagos'; 

    protected $primaryKey = 'id_pago';
    public $incrementing = true; 
    protected $keyType = 'int';

    protected $fillable = [
        'id_deuda',
        'id_orden',
        'tipo_pago',
        'fecha_pago',
        'monto',
        'observaciones',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'datetime', 
            'monto' => 'decimal:2',     
            'estado' => 'boolean',      
        ];
    }

    public function deuda()
    {
        return $this->belongsTo(\App\Models\Deuda::class, 'id_deuda', 'id_deuda');
    }

    public function ordenPago()
    {
        return $this->belongsTo(OrdenPago::class, 'id_orden', 'id_orden');
    }

    public function detallesPago()
    {
        return $this->hasMany(DetallePago::class, 'id_pago', 'id_pago');
    }

    public function distribuciones()
    {
        return $this->hasMany(DistribucionPagoDeuda::class, 'id_pago', 'id_pago');
    }

    public function conceptoPago()
    {
        return $this->hasOneThrough(
            ConceptoPago::class,
            Deuda::class,
            'id_deuda',      // Foreign key en deuda
            'id_concepto',   // Foreign key en concepto_pago
            'id_deuda',      // Local key en pago
            'id_concepto'    // Local key en deuda
        );
    }

    public function alumno()
    {
        // Relación a través de deuda
        return $this->hasOneThrough(
            Alumno::class,
            Deuda::class,
            'id_deuda',    // Foreign key en deuda
            'id_alumno',   // Foreign key en alumno
            'id_deuda',    // Local key en pago
            'id_alumno'    // Local key en deuda
        );
    }
}