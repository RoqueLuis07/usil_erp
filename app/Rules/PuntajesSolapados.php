<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PuntajesSolapados implements Rule
{
    public function passes($attribute, $value)
    {
        $puntosMinimos = array_column($value, 'punto_minimo');
        $puntosMaximos = array_column($value, 'punto_maximo');

        $rangosExistentes = [];

        foreach ($value as $detalle) {
            $punto_minimo = $detalle['punto_minimo'];
            $punto_maximo = $detalle['punto_maximo'];

            foreach ($rangosExistentes as $rango) {
                if ($punto_minimo <= $rango['punto_maximo'] && ($punto_maximo >= $rango['punto_minimo'])) {
                    return false;
                }
            }
            $rangosExistentes[] = ['punto_minimo' => $punto_minimo, 'punto_maximo' => $punto_maximo];
        }

        $puntoMinimoCounts = array_count_values($puntosMinimos);
        $puntoMaximoCounts = array_count_values($puntosMaximos);

        foreach ($puntoMinimoCounts as $punto => $count) {
            if ($count > 1) {
                return false;
            }
        }

        foreach ($puntoMaximoCounts as $punto => $count) {
            if ($count > 1) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'Los puntajes no pueden solaparse entre sí.';
    }
}
