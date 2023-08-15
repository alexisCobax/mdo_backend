<?php

namespace App\Transformers\Cotizacion;

use App\Models\Cotizacion;
use App\Models\Cotizaciondetalle;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform(Cotizacion $cotizacion)
    {

        $detalle = Cotizaciondetalle::with('productos')
            ->where('cotizacion', $cotizacion->id)
            ->get(['id', 'cotizacion', 'producto', 'precio', 'cantidad']);

        $detalleDatos = $detalle->map(function ($detalles) {
            return [
                'id' => $detalles->id,
                'cotizacion' => $detalles->cotizacion,
                'idProducto' => $detalles->producto,
                'producto' => $detalles->productos->nombre,
                'precio' => $detalles->precio,
                'cantidad' => $detalles->cantidad,
                "codigo" =>$detalles->productos->codigo,
                "marca"=> optional($detalles->productos->marcas)->nombre,
                "color"=> optional($detalles->productos->colores)->nombre
            ];
        })->toArray();

        return [
            'id' => $cotizacion->id,
            'fecha' => $cotizacion->fecha,
            'cliente' => $cotizacion->cliente,
            'clienteNombre' => optional($cotizacion->clientes)->nombre,
            'total' => $cotizacion->total,
            'estado' => $cotizacion->estado,
            'IdActiveCampaign' => $cotizacion->IdActiveCampaign,
            'descuento' => $cotizacion->descuento,
            'subTotal' => $cotizacion->subTotal,
            'detalle' => $detalleDatos
        ];
    }
}
