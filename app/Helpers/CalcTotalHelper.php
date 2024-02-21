<?php

namespace App\Helpers;

class CalcTotalHelper
{
    public static function calcular($subTotal, $cantidad, $descuentos)
    {
        $totalEnvio = CalcEnvioHelper::calcular($cantidad);
        $total = $subTotal - $descuentos; //subtotal - descuentos
        $totalConEnvio = $total + $totalEnvio;

        return [
            'total' => $total == 0 ? '0.00' : number_format($total, 2, '.', ''),
            'descuentos'=> $descuentos == 0 ? '0.00' : number_format($descuentos, 2, '.', ''),
            'subTotal'=> $subTotal == 0 ? '0.00' : number_format($subTotal, 2, '.', ''), //subtotal - descuentos
            'totalConEnvio'=> $totalConEnvio == 0 ? '0.00' : number_format($totalConEnvio, 2, '.', ''),
            'totalEnvio' => $totalEnvio == 0 ? '0.00' : number_format($totalEnvio, 2, '.', ''),
        ];

    }
}
