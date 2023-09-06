<?php

namespace App\Transformers\Invoices;

use League\Fractal\TransformerAbstract;

class CreateDetalleTransformer extends TransformerAbstract
{
    private $id = 0;

    public function transform($detalle, $id)
    {
        $this->id = $id;

        $response = $detalle->map(function ($detalle) {

            $descripcion = optional($detalle->productos)->nombre . ' | ' . optional($detalle->productos->marcas)->nombre . ' | ' . optional($detalle->productos->colores)->nombre;

            return [
                'qordered' => $detalle->cantidad,
                'qshipped' => $detalle->cantidad,
                'qborder' => $detalle->cantidad,
                'itemNumber' => $detalle->pedido,
                'Descripcion' => $descripcion,
                'listPrice' => $detalle->precio,
                'netPrice' => $detalle->precio,
                'invoice' => $this->id,
            ];
        })->toArray();

        return $response;
    }
}
