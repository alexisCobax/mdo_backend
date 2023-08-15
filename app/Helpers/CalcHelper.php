<?php

namespace App\Helpers;

class CalcHelper
{
    public static function ListProduct($sugerido, $promocional)
    {

        if ($sugerido > $promocional and $promocional > 0) {
            $precio = $promocional;
        } else {
            $precio = $sugerido;
        }

        return $precio;
    }
}
