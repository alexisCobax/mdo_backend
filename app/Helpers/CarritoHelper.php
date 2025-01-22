<?php

namespace App\Helpers;

use App\Models\Carrito;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class CarritoHelper
{
    public static function getCarrito()
    {
        $user = Auth::user();
        $cliente = Cliente::where('usuario', $user['id'])->first();
        $carrito = Carrito::where('cliente', $cliente->id)
            ->where('estado', 0)
            ->first();

        if (!$carrito) {
            return [
                'id' => '',
                'cupon' => 0,
                'cliente' => $cliente->id,
                'clienteNombre' => $cliente->nombre,
                'usuario' => $user['id'],
            ];
        }

        return [
            'id' => $carrito->id,
            'cupon' => $carrito->cupon,
            'cliente' => $cliente->id,
            'clienteNombre' => $cliente->nombre,
            'usuario' => $user['id'],
        ];
    }
}
