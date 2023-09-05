<?php

namespace App\Transformers\Carrito;

use App\Models\Carritodetalle;
use League\Fractal\TransformerAbstract;
use App\Helpers\CalcHelper;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($id)
    {
        $detalle = Carritodetalle::where('carrito', $id)->get();

        $response = $detalle->map(function ($detalle) {

            $subTotal = CalcHelper::ListProduct($detalle->precio, $detalle->precioPromocional);

            $producto = [
                'id' => optional($detalle->productos)->id,
                'nombre' => optional($detalle->productos)->nombre,
                "marcaNombre" => optional(optional($detalle->productos)->marcas)->nombre,
                'precio' => $subTotal,
                'imagen' => optional($detalle->productos)->imagenPrincipal,
                'color' => optional(optional($detalle->productos)->colores)->nombre,
                'tamano' => optional($detalle->productos)->tamano
            ];

            $detalleCarrito = [
                'id' => $detalle->id,
                'carrito' => $detalle->carrito,
                'producto' => $producto,
                'precio' => $subTotal,
                'cantidad' => $detalle->cantidad,
                'subTotal' => $subTotal*$detalle->cantidad,
            ];

            return $detalleCarrito;
        });

        $total = $response->sum('subTotal');

        return ['carrito' => $id, 'total' => $total, 'detalles' => $response->toArray()];
    }
}
