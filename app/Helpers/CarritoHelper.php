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

        return [
            'id' => $carrito->id,
            'cliente' => $cliente->id,
            'usuario' => $user['id'],
        ];
    }
}
