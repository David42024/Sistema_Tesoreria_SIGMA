<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptoPago extends Model
{
    /** @use HasFactory<\Database\Factories\ConceptoPagoFactory> */
    use HasFactory;

    protected $table = 'conceptos_pago';

    protected $primaryKey = 'id_concepto';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'descripcion',
        'escala',
        'monto',
        'estado',
    ];


    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'estado' => 'boolean',
        ];
    }

    public function detallesOrden()
    {
        return $this->hasMany(DetalleOrdenPago::class, 'id_concepto', 'id_concepto');
    }

}
