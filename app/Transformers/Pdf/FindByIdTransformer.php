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
        $detalle = Pedidodetalle::where('pedido', $pedido->id)->get();

        foreach ($detalle as $d) {

            $precio = $d->precio * $d->cantidad;

            $pedidoDetalle[] = [
                'imagen' => '',
                'cantidad' => $d->cantidad,
                'codigo' => $d->id,
                'nombreProducto' => optional($d->productos)->nombre,
                'producto' => $d->productos->id,
                'nombreColor' => optional($d->productos->colores)->nombre,
                'color' => optional($d->productos->colores)->id,
                'precio' => $d->precio,
                'total' => $precio,
                'imagen' => env('URL_IMAGENES_PRODUCTOS') . optional($d->productos)->imagenPrincipal . '.' . env('EXTENSION_IMAGEN_PRODUCTO'),
            ];
        }

        return [
            'tienda' => [
                'direccion' => 'MDO INC
                2618 NW 112th AVENUE. MIAMI, FL 33172',
                'telefono' => '513 9177 / 305 424 8199',
                'numero_pedido' => $pedido->id,
                'fecha_pedido' => DateHelper::ToDateCustom($pedido->fecha),
                'email' => 'fashionglassesrd@gmail.com',
            ],
            'cliente' => [
                'nombre' => optional($pedido->clientes)->nombre,
                'numero' => optional($pedido->clientes)->id,
                'telefono' => optional($pedido->clientes)->telefono,
                'direccion' => optional($pedido->clientes)->direccion,
            ],
            'detalle' => $pedidoDetalle,
        ];
    }
}
