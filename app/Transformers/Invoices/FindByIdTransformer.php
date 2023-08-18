<?php

namespace App\Transformers\Invoices;

use App\Models\Invoicedetalle;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform($invoice,$request)
    {
        $invoiceDetalle = Invoicedetalle::where('invoice', $request->id)->get()->ToArray();

        $detalle = [];

        foreach ($invoiceDetalle as $id) {
            $detalle[] = [
                "id" => $id['id'],
                "qordered" => $id['qordered'],
                "qshipped" => $id['qshipped'],
                "qborder" => $id['qborder'],
                "itemNumber" => $id['itemNumber'],
                "Descripcion" => $id['Descripcion'],
                "listPrice" => $id['listPrice'],
                "netPrice" => $id['netPrice'],
                "invoice" => $id['invoice']
            ];
        }

        return [
            'id' => $invoice->id,
            'fecha' => $invoice->fecha,
            'cliente' => $invoice->cliente,
            'clienteNombre' => $invoice->clientes->nombre,
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
            'IdActiveCampaign' => $invoice->IdActiveCampaign,
            'detalle' => $detalle
        ];

    }
}
