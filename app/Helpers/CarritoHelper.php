<?php

namespace App\Helpers;

use App\Models\Carrito;
use App\Models\Cliente;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CarritoHelper
{
    public static function getCarrito()
    {
        $user = Auth::user();

        if($user){
        $cliente = Cliente::where('usuario', $user['id'])->first();
        }else{
            return response()->json(['error' => 'No existe cliente logueado'], Response::HTTP_NOT_FOUND);
        }

        if($cliente->id){
        $carrito = Carrito::where('cliente', $cliente->id)
            ->where('estado', 0)
            ->first();

        if (!$carrito) {
            return [
                'id' => '',
                'cliente' => $cliente->id,
                'usuario' => $user['id'],
            ];
        }

        return [
            'id' => $carrito->id,
            'cliente' => $cliente->id,
            'usuario' => $user['id'],
        ];
    }else{
        return response()->json(['error' => 'El usuario no tiene asignado un cliente'], Response::HTTP_NOT_FOUND);
    }
    }
}
