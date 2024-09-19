<?php

namespace App\Transformers\Invoices;

use App\Helpers\DateHelper;
use App\Models\Cliente;
use App\Models\Invoicedetalle;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform($invoice, $request)
    {
        $invoiceDetalle = Invoicedetalle::where('invoice', $request->id)->orderBy('Descripcion', 'asc')->get()->ToArray();

        // $cliente = Cliente::where('id',$invoice->cliente)->first();

        $detalle = [];
        // $datosEnvio = [];
        $cantidad = 0;
        foreach ($invoiceDetalle as $id) {
            $cantidad += $id['qordered'];
                $total = $id['listPrice'] * $id['qordered'];
            $detalle[] = [
                'id' => $id['id'],
                'qordered' => $id['qordered'],
                'qshipped' => $id['qshipped'],
                'qborder' => $id['qborder'],
                'itemNumber' => $id['itemNumber'],
                'Descripcion' => $id['Descripcion'],
                'listPrice' => number_format($id['listPrice'], 2, '.', ''),
                'netPrice' => $id['netPrice'],
                'invoice' => $id['invoice'],
                'total' => number_format($total, 2, '.', ''),
            ];
        }

        // $datosEnvio = [
        //     'nombre' => $cliente->nombre,
        //     'direccion' => $cliente->direccion,
        //     'ciudad' => $cliente->ciudad,
        //     'pais' => $cliente->pais,
        //     'telefono' => $cliente->telefono
        // ];

        return [
            'id' => $invoice->id,
            'fecha' => DateHelper::ToDateCustom($invoice->fecha),
            'cliente' => $invoice->cliente,
            'clienteId' => $invoice->clientes->id,
            'clienteNombre' => $invoice->clientes->nombre,
            'clienteCiudad' => $invoice->clientes->ciudad,
            'clientePais' => $invoice->clientes->pais,
            'clienteCodigoPostal' => $invoice->clientes->codigoPostal,
            'clienteTelefono' => $invoice->clientes->telefono,
            'clienteDireccionShape' => $invoice->clientes->direccionShape,
            'total' => $invoice->total - $invoice->TotalEnvio,
            'formaDePago' => $invoice->formaDePago,
            'estado' => $invoice->estado,
            'observaciones' => $invoice->observaciones,
            'anulada' => $invoice->anulada,
            'billTo' => $invoice->billTo,
            'shipTo' => $invoice->shipTo,
            'shipVia' => $invoice->shipVia,
            'FOB' => $invoice->FOB,
            'Terms' => $invoice->Terms,
            'fechaOrden' => date('Y-m-d', strtotime($invoice->fechaOrden)),
            'salesPerson' => $invoice->salesPerson,
            'orden' => $invoice->orden,
            'peso' => $invoice->peso,
            'cantidad' => $cantidad,
            'DescuentoNeto' => $invoice->DescuentoNeto,
            'DescuentoPorcentual' => $invoice->DescuentoPorcentual,
            'UPS' => $invoice->UPS,
            'TotalEnvio' => $invoice->TotalEnvio,
            'codigoUPS' => $invoice->codigoUPS,
            'subTotal' => round($invoice->subTotal, 2),
            'DescuentoPorPromociones' => $invoice->DescuentoPorPromociones,
            'IdActiveCampaign' => $invoice->IdActiveCampaign,
            'detalle' => $detalle,
            'preciosTotales' => [
                'descuentoNeto' => $invoice->DescuentoNeto,
                'totalDescuentoPorcentual' => $invoice->subTotal * $invoice->DescuentoPorcentual / 100,
                'descuentoPorcentual' => $invoice->DescuentoPorcentual,
                'subtotalConDescuento' => $invoice->subTotal - $invoice->DescuentoNeto - ($invoice->subTotal * $invoice->DescuentoPorcentual / 100) - $invoice->DescuentoPorPromociones,
                'subtotal' => $invoice->subTotal,
                'totalEnvio' => $invoice->TotalEnvio,
                'descuentoPorPromociones' => $invoice->DescuentoPorPromociones,
                'total'  => round($invoice->subTotal - $invoice->DescuentoNeto - ($invoice->subTotal * $invoice->DescuentoPorcentual / 100) - $invoice->DescuentoPorPromociones + $invoice->TotalEnvio, 2)
                //'total' => $invoice->subTotal - $invoice->DescuentoNeto - ($invoice->subTotal * $invoice->DescuentoPorcentual / 100) - $invoice->DescuentoPorPromociones + $invoice->TotalEnvio,
            ],

            // 'datosEnvio' => $datosEnvio
        ];
    }
}
