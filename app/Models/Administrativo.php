<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrativo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'apellido_paterno',
        'apellido_materno',
        'primer_nombre',
        'otros_nombres',
        'dni',
        'direccion',
        'estado_civil',
        'telefono',
        'seguro_social',
        'fecha_ingreso',
        'cargo',
        'sueldo',
        'estado',
    ];

    protected $primaryKey = 'id_administrativo';

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
