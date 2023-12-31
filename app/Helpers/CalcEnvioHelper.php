<?php

namespace App\Helpers;

use App\Models\Cliente;
use App\Models\Zipcode;

class CalcEnvioHelper
{
    public static function calcular($cantidad)
    {

        $carrito = CarritoHelper::getCarrito();

        $cliente = Cliente::where('id', $carrito['cliente'])->where('tipoDeEnvio', 3)->where('paisShape', 'USA')->first();

        $totalEnvio = 0;

        if ($cliente) {

            $zipCode = Zipcode::where('zip', $cliente->cpShape)->first();

            $cantidadCajas = ceil($cantidad / env('UNIDADES_X_CAJA'));
            $totalEnvio = $cantidadCajas * $zipCode->precio;

            return $totalEnvio;
        }
    }
}
