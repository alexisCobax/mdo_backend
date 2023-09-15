<?php

namespace App\Helpers;

use App\Helpers\CalcEnvioHelper;



class CalcTotalHelper
{
    public static function calcular($subTotal,$cantidad,$descuentos)
    {
        $totalEnvio = CalcEnvioHelper::calcular($cantidad);
        $descuentos = '0.00';
        $total = $subTotal-$descuentos;
        $totalConEnvio = $total+$totalEnvio;

        return [
            'total' => $total == 0 ? '0.00' : number_format($total, 2, '.', ''),
            'descuentos'=> $descuentos == 0 ? '0.00' : number_format($descuentos, 2, '.', ''), 
            'subTotal'=> $subTotal == 0 ? '0.00' : number_format($subTotal, 2, '.', ''), 
            'totalConEnvio'=> $totalConEnvio == 0 ? '0.00' : number_format($totalConEnvio, 2, '.', ''), 
            'totalEnvio' => $totalEnvio == 0 ? '0.00' : number_format($totalEnvio, 2, '.', '')
        ];

    }


}