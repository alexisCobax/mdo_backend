<?php

namespace App\Transformers\Pdf;

use App\Models\Cliente;
use App\Helpers\DateHelper;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform($pedido)
    {
        $pedidoDetalle = [];
        $cliente = Cliente::find($pedido->cliente);
        $detalle = Pedidodetalle::with('productos.colores')->orWhere('pedido', $pedido->id)->get();
        $detalleNn = Pedidodetallenn::orWhere('pedido', $pedido->id)->get();

        $cantidadTotal = 0;
        $descuentoPorcentual = $pedido->total * $pedido->DescuentoPorcentual / 100;
        $totalDescuentos = $descuentoPorcentual + $pedido->DescuentoPromociones + $pedido->DescuentoNeto;
        $totalEnvio = $pedido->TotalEnvio;

        foreach ($detalle as $d) {
            $precio = $d->precio * $d->cantidad;
            $cantidadTotal += $d->cantidad;
            $pedidoDetalle[] = [
                'cantidad' => $d->cantidad,
                'codigo' => $d->id,
                'nombreProducto' => optional($d->productos)->nombre ?? '',
                'producto' => optional($d->productos)->id ?? 0,
                'nombreColor' => optional($d->productos)->color ?? '',
                'color' => optional($d->productos->colores)->id ?? '',
                'precio' => number_format($d->precio, 2),
                'total' => number_format($precio, 2),
                'imagen' => env('URL_IMAGENES_PRODUCTOS') . optional($d->productos)->imagenPrincipal ?? '',
            ];
        }

        foreach ($detalleNn as $dNn) {
            $cantidadTotal += $dNn->cantidad;
            $pedidoDetalle[] = [
                'cantidad' => $dNn->cantidad,
                'codigo' => $dNn->id,
                'nombreProducto' => $dNn->descripcion,
                'producto' => 0,
                'nombreColor' => '',
                'color' => '',
                'precio' => number_format($dNn->precio, 2),
                'total' => number_format($dNn->precio * $dNn->cantidad, 2),
                'imagen' => env('URL_IMAGENES_PRODUCTOS') . 0,
            ];
        }

        return [
            'tienda' => [
                'direccion' => 'MDO INC 2618 NW 112th AVENUE. MIAMI, FL 33172',
                'telefono' => '513 9177 / 305 424 8199',
                'numero_pedido' => $pedido->id,
                'fecha_pedido' => DateHelper::ToDateCustom($pedido->fecha),
                'email' => 'ventas@mayoristasdeopticas.com',
            ],
            'cliente' => [
                'nombre' => optional($cliente)->nombre ?? '',
                'numero' => optional($cliente)->id ?? '',
                'telefono' => optional($cliente)->telefono ?? '',
                'direccion' => optional($cliente)->direccion ?? '',
                'email' => optional($cliente)->email ?? '',
            ],
            'detalle' => $pedidoDetalle,
            'pedido' => [
                'subTotal' => number_format($pedido->total, 2),
                'descuentoPorcentual' => number_format($pedido->DescuentoPorcentual, 2),
                'descuentoPorcentualTotal' => number_format($descuentoPorcentual, 2),
                'descuentoPromociones' => number_format($pedido->DescuentoPromociones, 2),
                'descuentoNeto' => number_format($pedido->DescuentoNeto, 2),
                'total' => number_format($pedido->total - $totalDescuentos, 2),
                'totalEnvio' => number_format($totalEnvio, 2),
                'subTotalConEnvio' => number_format($pedido->total - $totalDescuentos + $totalEnvio, 2),
                'creditoDisponible' => number_format($cliente->ctacte ?? 0, 2),
                'totalAabonar' => number_format($pedido->total - $totalDescuentos + $totalEnvio * 2 - ($cliente->ctacte ?? 0), 2),
                'cantidad' => $cantidadTotal,
            ],
        ];
    }
}
