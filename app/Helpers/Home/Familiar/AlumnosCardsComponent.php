<?php

namespace App\Helpers\Home\Familiar;

use App\Helpers\Tables\Component;

class AlumnosCardsComponent implements Component {
    public array $alumnos = [];

    public function __construct(array $alumnos = []) {
        $this->alumnos = $alumnos;
    }

    public function render() {
        return view('components.familiar.alumnos-cards-content', [
            'alumnos' => $this->alumnos
        ]);
    }
}
