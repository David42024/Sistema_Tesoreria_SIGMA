<?php

namespace App\Observers\Traits;

use App\Models\RegistroHistorico;
use App\Models\ConceptoAccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait LogsActions
{
    // El registro de acciones solo debe ser deshabilitado en el uso de seeders.
    public static $disabled = false;

    /**
     * Helper para registrar la acción en la tabla 'registro_historico'.
     *
     * @param \Illuminate\Database\Eloquent\Model $model        El modelo afectado por la acción (e.g., Producto, Cliente).
     * @param string $actionName                  El nombre de la acción (e.g., 'Creó', 'Modificó', 'Eliminó').
     * @param \Illuminate\Database\Eloquent\Model|null $subject El modelo sujeto de la acción (normalmente el mismo $model).
     * @param string|null $observacion            Observación inicial (opcional).
     * @return void
     */

    public static function disable(){
        static::$disabled = true;
    }

    public static function enable(){
        static::$disabled = false;
    }

    protected function logAction(Model $model, string $actionName, ?Model $subject = null, ?string $observacion = null): void
    {
        if (LogsActions::$disabled || !Auth::check()) {
            return;
        }

        $conceptoAccion = ConceptoAccion::where('accion', '=', $actionName)
            ->where('estado', '=', '1')
            ->get()->first();

        if ($conceptoAccion) {
            $data = [
                'id_concepto_accion' => $conceptoAccion->id_concepto_accion,
                'id_autor' => Auth::id(),
                'fecha_accion' => now(),
                'observacion' => $observacion,
                'estado' => true,
            ];

            if ($subject) {
                $data['id_entidad_afectada'] = $subject->getKey();
                $data['tipo_entidad_afectada'] = get_class($subject);
            } else {
                $data['id_entidad_afectada'] = $model->getKey();
                $data['tipo_entidad_afectada'] = get_class($model);
            }

            RegistroHistorico::create($data);
        }
    }
}