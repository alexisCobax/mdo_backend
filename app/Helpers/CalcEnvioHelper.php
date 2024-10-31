<?php

namespace App\Helpers;

use App\Models\Cliente;
use App\Models\Zipcode;

class CalcEnvioHelper
{
    public static function calcular($cantidad)
    {

        $carrito = CarritoHelper::getCarrito();

        $cliente = Cliente::where('id', $carrito['cliente'])->first();

        $cantidadCajas = ceil($cantidad / env('UNIDADES_X_CAJA'));
        $totalEnvio = 0;
        if($cliente->tipoDeEnvio == 2){
            
            $totalEnvio = env('ENVIO_USA')*$cantidadCajas;

            $zipCode = Zipcode::where('zip', $cliente->cpShape)->first();

            if ($zipCode) {

                $totalEnvio = $cantidadCajas * $zipCode->precio;

                return $totalEnvio;
            }

            return $totalEnvio;
        }
    }
}
