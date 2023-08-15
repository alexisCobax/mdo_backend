<?php

namespace App\Transformers\Invoices;

use App\Models\Encargadodeventa;
use League\Fractal\TransformerAbstract;

class CreateTransformer extends TransformerAbstract
{
    public function transform($pedido, $cantidad, $request)
    {
        $subTotal = $pedido->total-$pedido->descuentoNeto;
        $vendedor = Encargadodeventa::find($pedido->vendedor)->first();

        return [
            'fecha'=>NOW(),
            'cliente'=>$pedido->cliente,
            'total'=>$pedido->total,
            'formaDePago'=>$pedido->formaDePago,
            'estado'=>1,
            'observaciones' =>"",
            'anulada' =>0,
            'billTo' =>optional($pedido->clientes)->direccionBill,
            'shipTo' =>optional($pedido->clientes)->nombre,
            'shipVia' =>"",
            'FOB' =>"",
            'Terms' =>"",
            'fechaOrden' =>$pedido->fecha,
            'salesPerson' => $vendedor->nombre,
            'orden' =>$pedido->id,
            'peso' =>0,
            'cantidad' => $cantidad[0]->suma_cantidad,
            'DescuentoNeto' =>$pedido->descuentoNeto,
            'DescuentoPorcentual' =>$pedido->descuentoPorcentual,
            'UPS' =>"",
            'TotalEnvio' =>$pedido->totalEnvio,
            'codigoUPS' =>$request->codigoUPS,
            'subTotal' =>$subTotal,
            'DescuentoPorPromociones' =>0,
            'IdActiveCampaign' =>0
        ];
    }
}
