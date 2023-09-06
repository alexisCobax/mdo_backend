<?php

namespace App\Helpers;

class NumbersToLetters
{
    /**
     * getDateCustom.
     *
     * @param  mixed $numero
     * @return int
     *
     * Response Example A
     */
    public static function ToLetters($numero)
    {
        $letras = [
            'a', 'b', 'c', 'd', 'e',
            'f', 'g', 'h', 'i', 'j',
            'k', 'l', 'm', 'n',
        ];

        if ($numero >= 1 && $numero <= count($letras)) {
            return strtoupper($letras[$numero]);
        } else {
            return 'Número fuera del rango de conversión';
        }
    }
}
