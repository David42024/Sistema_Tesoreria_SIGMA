<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'tipo',
        'password',
        'estado',
        'idArchivo',
    ];

    protected $primaryKey = 'id_usuario';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function administrativo()
    {
        return $this->hasOne(Administrativo::class, 'id_usuario', 'id_usuario');
    }

    public function personal()
    {
        return $this->hasOne(Personal::class, 'id_usuario', 'id_usuario');
    }

    public function solicitudPrematricula()
    {
        return $this->hasOne(SolicitudPrematricula::class, 'id_usuario', 'id_usuario');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivo::class, 'idArchivo', 'idArchivo');
    }

    public function getNameAttribute()
    {
        if ($this->tipo === 'Administrativo' && $this->administrativo) {
            return trim($this->administrativo->primer_nombre . ' ' .
                       ($this->administrativo->otros_nombres ?? '') . ' ' .
                       $this->administrativo->apellido_paterno . ' ' .
                       $this->administrativo->apellido_materno);
        } elseif ($this->tipo === 'Personal' && $this->personal) {
            return trim($this->personal->primer_nombre . ' ' .
                       ($this->personal->otros_nombres ?? '') . ' ' .
                       $this->personal->apellido_paterno . ' ' .
                       $this->personal->apellido_materno);
        } elseif ($this->tipo === 'PreApoderado' && $this->solicitudPrematricula) {
            return $this->solicitudPrematricula->nombre_completo_apoderado;
        }

        return ucwords(str_replace('_', ' ', $this->username));
    }

    public function getNombrePreApoderadoAttribute()
    {
        if ($this->tipo === 'PreApoderado' && $this->solicitudPrematricula) {
            return $this->solicitudPrematricula->nombre_completo_apoderado;
        }

        return $this->username;
    }
}
