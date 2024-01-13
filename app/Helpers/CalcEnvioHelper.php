<?php

namespace App\Helpers;

use App\Models\Cliente;
use App\Models\Zipcode;

class CalcEnvioHelper
{
    public static function calcular($cantidad)
    {

        $carrito = CarritoHelper::getCarrito(); 

        $cliente = Cliente::where('id', $carrito['cliente'])->where('tipoDeEnvio', 2)->where('paisShape', 224)->first();

        $totalEnvio = 0;

        if ($cliente) {

            $zipCode = Zipcode::where('zip', $cliente->cpShape)->first();

            if($zipCode){

            $cantidadCajas = ceil($cantidad / env('UNIDADES_X_CAJA'));
            $totalEnvio = $cantidadCajas * $zipCode->precio;

            return $totalEnvio;
            }
            return 0;
        }
    }
}
