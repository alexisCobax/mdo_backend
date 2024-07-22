<?php

namespace App\Transformers\Invoices;

use App\Models\Encargadodeventa;
use League\Fractal\TransformerAbstract;

class CreateTransformer extends TransformerAbstract
{
    public function transform($pedido, $cantidad, $request)
    {
        $subTotal = $pedido->total - $pedido->DescuentoNeto;
        $vendedorNombre = $pedido->vendedor ? optional($pedido->vendedores)->nombre : '';

        return [
            'cliente' => $pedido->cliente,
            'total' => $pedido->total,
            'formaDePago' => $pedido->formaDePago,
            'estado' => 1,
            'observaciones' => '',
            'anulada' => 0,
            'billTo' => optional($pedido->cliente)->direccionBill,
            'shipTo' => optional($pedido->cliente)->nombre,
            'shipVia' => '',
            'FOB' => '',
            'Terms' => '',
            'fechaOrden' => $pedido->fecha,
            'salesPerson' => $vendedorNombre,
            'orden' => $pedido->id,
            'peso' => 0,
            'cantidad' => $cantidad,
            'DescuentoNeto' => $pedido->DescuentoNeto,
            'DescuentoPorcentual' => $pedido->DescuentoPorcentual,
            'UPS' => '',
            'TotalEnvio' => $pedido->TotalEnvio,
            'codigoUPS' => $request->codigoUPS,
            'subTotal' => $subTotal,
            'DescuentoPorPromociones' => $pedido->DescuentoPromociones,
            'IdActiveCampaign' => 0,
        ];
    }
}
