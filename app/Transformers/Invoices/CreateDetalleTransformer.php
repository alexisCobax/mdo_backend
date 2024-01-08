<?php

namespace App\Transformers\Invoices;

use League\Fractal\TransformerAbstract;

class CreateDetalleTransformer extends TransformerAbstract
{

    public function transform($detalle, $id)
    {

        $response = $detalle->map(function ($detalle) use ($id){

            if($detalle->producto){
                $descripcion = optional($detalle->productos)->nombre . ' | ' . optional($detalle->productos->marcas)->nombre . ' | ' . optional($detalle->productos->colores)->nombre;
            }else{
                $descripcion = $detalle->descripcion;
            }

            if($detalle->producto){
                $itemNumber = optional($detalle->productos)->codigo;
            }else{
                $itemNumber = 'NN';
            }

            $response[] = [
                'qordered' => $detalle->cantidad,
                'qshipped' => $detalle->cantidad,
                'qborder' => $detalle->cantidad,
                'itemNumber' => $itemNumber,
                'Descripcion' => $descripcion,
                'listPrice' => $detalle->precio,
                'netPrice' => $detalle->precio,
                'invoice' => $id,
            ];

        //     return [
        //         'qordered' => $detalle->cantidad,
        //         'qshipped' => $detalle->cantidad,
        //         'qborder' => $detalle->cantidad,
        //         'itemNumber' => $itemNumber,
        //         'Descripcion' => $descripcion,
        //         'listPrice' => $detalle->precio,
        //         'netPrice' => $detalle->precio,
        //         'invoice' => $id,
        //     ];
        // })->toArray();
    })->toArray();
        return $response;
    }
}
