<?php

namespace App\Transformers\Carrito;

use App\Models\Carritodetalle;
use League\Fractal\TransformerAbstract;
use App\Helpers\CalcHelper;
use App\Models\Color;
use App\Models\Marcaproducto;
use App\Models\Producto;
use App\Models\Tamanoproducto;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($id)
    {
        $detalle = Carritodetalle::where('carrito', $id)->get();

        $response = $detalle->map(function ($detalle) {

            $productos = Producto::find($detalle->producto);

            $marca = Marcaproducto::find($productos->marca);

            $color = Color::find($productos->color);

            $tamano = Tamanoproducto::find($productos->tamano);

            $subTotal = CalcHelper::ListProduct($detalle->precio, $detalle->precioPromocional, $detalle->cantidad);

            $producto = [
                'id' => $productos->id,
                'nombre' => isset($productos->nombre) ? $productos->nombre : "",
                "marcaNombre" => isset($marca->nombre) ? $marca->nombre : "",
                'precio' => isset($productos->precio) ? $productos->precio : "",
                'imagen' => isset($productos->imagenPrincipal) ? $productos->imagenPrincipal : "",
                'color' => isset($color->nombre) ? $color->nombre : "",
                'tamano' => isset($tamano->nombre) ? $tamano->nombre : ""
            ];

            $detalleCarrito = [
                'id' => $detalle->id,
                'carrito' => $detalle->carrito,
                'producto' => $producto,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'subTotal' => $subTotal,
            ];

            return $detalleCarrito;
        });

        $total = $response->sum('subTotal');

        return ['carrito' => $id, 'total' => $total, 'detalles' => $response->toArray()];
    }
}
