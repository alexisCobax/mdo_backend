<?php

namespace App\Helpers;

use App\Models\Carrito;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

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
                'id' => "",
                'cliente' => $cliente->id,
                'usuario' => $user['id'],
            ];
        }

        return [
            'id' => $carrito->id,
            'cliente' => $cliente->id,
            'usuario' => $user['id'],
        ];
    }
}
