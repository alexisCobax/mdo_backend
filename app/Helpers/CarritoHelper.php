<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Carrito;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class CarritoHelper
{

    static function getCarrito()
    {
        $user = Auth::user();
        $cliente = Cliente::where('usuario', $user['id'])->first();
        $carrito = Carrito::where('cliente', $cliente->id)
            ->where('estado', 0)
            ->first();

        return [
            'id' => $carrito->id,
            'cliente' => $cliente->id,
            'usuario' => $user['id']
        ];
    }
}
