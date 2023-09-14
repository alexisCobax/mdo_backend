<?php

namespace App\Transformers\Invoices;

use App\Helpers\DateHelper;
use App\Models\Cliente;
use App\Models\Invoicedetalle;
use App\Models\Producto;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform($invoice, $request)
    {
        $invoiceDetalle = Invoicedetalle::where('invoice', $request->id)->get()->ToArray();

        // $cliente = Cliente::where('id',$invoice->cliente)->first();

        
        $detalle = [];
        // $datosEnvio = [];

        foreach ($invoiceDetalle as $id) {

            $producto = Producto::where('id',$id['itemNumber'])->first();
            $productoDescripcion = $producto->descripcion.' '.optional($producto->colores)->nombre.' '.$producto->tamano.' '.optional($producto->materiales)->nombre;die;
            echo "asd ".$productoDescripcion;die;
            $detalle[] = [
                'id' => $id['id'],
                'qordered' => $id['qordered'],
                'qshipped' => $id['qshipped'],
                'qborder' => $id['qborder'],
                'itemNumber' => $id['itemNumber'],
                'Descripcion' => $id['Descripcion'],
                'listPrice' => $id['listPrice'],
                'netPrice' => $id['netPrice'],
                'invoice' => $id['invoice'],
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
            // 'datosEnvio' => $datosEnvio
        ];

    }
}
