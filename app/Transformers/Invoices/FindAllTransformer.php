<?php

namespace App\Transformers\Invoices;

use App\Models\Invoice;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Invoice $invoice)
    {
        return [
            'id' => $invoice->id,
            'fecha' => $invoice->fecha,
            'cliente' => $invoice->cliente,
            'clienteNombre' => optional($invoice->clientes)->nombre,
            'total' => $invoice->total,
            'formaDePago' => $invoice->formaDePago,
            'estado' => $invoice->estado,
            'observaciones' => $invoice->observaciones,
            'anulada' => $invoice->anulada,
            'billTo' => $invoice->billTo,
            'shipTo' => $invoice->shipTo,
            'shipVia' => $invoice->shipVia,
            'FOB' => $invoice->FOB,
            'Terms' => $invoice->Terms,
            'fechaOrden' => $invoice->fechaOrden,
            'salesPerson' => $invoice->salesPerson,
            'orden' => $invoice->orden,
            'peso' => $invoice->peso,
            'cantidad' => $invoice->cantidad,
            'DescuentoNeto' => $invoice->DescuentoNeto,
            'DescuentoPorcentual' => $invoice->DescuentoPorcentual,
            'UPS' => $invoice->UPS,
            'TotalEnvio' => $invoice->TotalEnvio,
            'codigoUPS' => $invoice->codigoUPS,
            'subTotal' => $invoice->subTotal,
            'DescuentoPorPromociones' => $invoice->DescuentoPorPromociones,
            'IdActiveCampaign' => $invoice->IdActiveCampaign
        ];
    }
}
