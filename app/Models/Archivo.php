<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'archivos';
    protected $primaryKey = 'idArchivo';

    protected $fillable = [
        'foto',
    ];

    /**
     * Relación: Un archivo puede estar asociado a muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class, 'idArchivo', 'idArchivo');
    }
}
