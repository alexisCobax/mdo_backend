<?php

namespace App\Helpers;

use App\Models\Producto;
use Illuminate\Http\Response;

class StockHelper
{
    public static function get($cantidad, $producto)
    {
        $producto = Producto::where('id', $producto)->first();

        if ($cantidad > $producto->stock) {
            return response()->json(['mensaje' => 'No hay stock suficiente', 'cantidad' => $producto->stock, 'status' => false], Response::HTTP_OK);
        } else {
            return response()->json(['mensaje' => '', 'cantidad' => $cantidad, 'status' => true], Response::HTTP_OK);
        }
    }
}
