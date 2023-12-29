<?php

namespace App\Transformers\Pdf;

use App\Helpers\DateHelper;
use App\Models\Cliente;
use App\Models\Pedidodetalle;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform($pedido)
    {
        $pedidoDetalle = [];
        $detalle = Pedidodetalle::where('pedido', $pedido->id)->get();

        $cliente = Cliente::where('id', $pedido->cliente)->first();

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
                'precio' => number_format($d->precio, 2),
                'total' => number_format($precio, 2),
                'imagen' => env('URL_IMAGENES_PRODUCTOS') . optional($d->productos)->imagenPrincipal . '.jpg',
            ];
        }

        $descuentoPorcentual = $pedido->total * $pedido->DescuentoPorcentual / 100;

        return [
            'tienda' => [
                'direccion' => 'MDO INC
                2618 NW 112th AVENUE. MIAMI, FL 33172',
                'telefono' => '513 9177 / 305 424 8199',
                'numero_pedido' => $pedido->id,
                'fecha_pedido' => DateHelper::ToDateCustom($pedido->fecha),
                'email' => 'ventas@mayoristasdeopticas.com',
            ],
            'cliente' => [
                'nombre' => optional($pedido->clientes)->nombre,
                'numero' => optional($pedido->clientes)->id,
                'telefono' => optional($pedido->clientes)->telefono,
                'direccion' => optional($pedido->clientes)->direccion,
                'email' => optional($pedido->clientes)->email
            ],
            'detalle' => $pedidoDetalle,
            'pedido' => [
                'subTotal' =>  number_format($pedido->total, 2),
                'descuentoPorcentual' =>  number_format($pedido->DescuentoPorcentual, 2),
                'descuentoPorcentualTotal' =>  number_format($descuentoPorcentual, 2),
                'descuentoPromociones' =>  number_format($pedido->DescuentoPromociones, 2),
                'descuentoNeto' =>  number_format($pedido->DescuentoNeto, 2),
                'total' =>  number_format($pedido->total - $descuentoPorcentual - $pedido->DescuentoPromociones - $pedido->DescuentoNeto, 2),
                'totalEnvio' =>  number_format($pedido->TotalEnvio, 2),
                'subTotalConEnvio' =>  number_format($pedido->total - $descuentoPorcentual - $pedido->DescuentoPromociones - $pedido->DescuentoNeto + $pedido->TotalEnvio, 2),
                'creditoDisponible' =>  number_format($cliente->ctacte, 2),
                'totalAabonar' => number_format($pedido->total - $descuentoPorcentual - $pedido->DescuentoPromociones - $pedido->DescuentoNeto + $pedido->TotalEnvio + $pedido->TotalEnvio - $cliente->ctacte, 2)
            ]
        ];
    }
}
