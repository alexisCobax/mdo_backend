<?php

namespace App\Transformers\Pdf;

use App\Helpers\DateHelper;
use App\Models\Pedidodetalle;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{

    public function transform($pedido)
    {
        $pedidoDetalle = [];
        $detalle = Pedidodetalle::find($pedido->id)->get();

        foreach ($detalle as $d) {

            $precio = $d->precio * $d->cantidad;

            $pedidoDetalle[] = [
                "imagen" => "",
                "cantidad" => $d->cantidad,
                "codigo" => $d->id,
                "nombre" => $d->productos->nombre,
                // "color" => $d->colores->nombre,
                "precio" => $d->precio,
                "total" => $precio
            ];
        }

        return [
            "tienda" => [
                "direccion" => "MDO INC
                2618 NW 112th AVENUE. MIAMI, FL 33172",
                "telefono" => "513 9177 / 305 424 8199",
                "numero_pedido" => $pedido->id,
                "fecha_pedido" => DateHelper::ToDateCustom($pedido->fecha),
                "email" => "fashionglassesrd@gmail.com"
            ],
            "cliente" => [
                "nombre" => optional($pedido->clientes)->nombre,
                "numero" => optional($pedido->clientes)->id,
                "telefono" => optional($pedido->clientes)->telefono,
                "direccion" => optional($pedido->clientes)->direccion
            ],
            "detalle" => $pedidoDetalle
        ];
    }
}
