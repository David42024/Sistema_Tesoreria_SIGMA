<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptoAccion extends Model
{
    use HasFactory;

    protected $table = 'concepto_accion';

    protected $primaryKey = 'id_concepto_accion';
    public $incrementing = true;
    protected $keyType = 'int';

    public const ID_VER = 1;
    public const ID_EDITAR = 2;
    public const ID_ELIMINAR = 3;
    public const ID_RESTAURAR = 4;
    public const ID_CREAR = 5;

    protected $fillable = [
        'accion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public static function getAccionById(int $id): ConceptoAccion
    {
        return match ($id) {
            self::ID_VER => self::accionVer(),
            self::ID_EDITAR => self::accionEditar(),
            self::ID_ELIMINAR => self::accionEliminar(),
            self::ID_RESTAURAR => self::accionRestaurar(),
            self::ID_CREAR => self::accionCrear(),
            default => throw new \Exception('Acción no encontrada'),
        };
    }

    public static function accionVer(): ConceptoAccion
    {
        return self::find(self::ID_VER);
    }

    public static function accionEditar(): ConceptoAccion
    {
        return self::find(self::ID_EDITAR);
    }

    public static function accionEliminar(): ConceptoAccion
    {
        return self::find(self::ID_ELIMINAR);
    }

    public static function accionRestaurar(): ConceptoAccion
    {
        return self::find(self::ID_RESTAURAR);
    }

    public static function accionCrear(): ConceptoAccion
    {
        return self::find(self::ID_CREAR);
    }

    public function esAccionVer(): bool
    {
        return $this->getKey() === self::ID_VER;
    }

    public function esAccionEditar(): bool
    {
        return $this->getKey() === self::ID_EDITAR;
    }

    public function esAccionEliminar(): bool
    {
        return $this->getKey() === self::ID_ELIMINAR;
    }

    public function esAccionRestaurar(): bool
    {
        return $this->getKey() === self::ID_RESTAURAR;
    }

    public function esAccionCrear(): bool
    {
        return $this->getKey() === self::ID_CREAR;
    }
}
