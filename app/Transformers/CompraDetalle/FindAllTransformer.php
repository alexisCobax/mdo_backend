<?php

namespace App\Transformers\CompraDetalle;

use App\Models\CompraDetalle;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(CompraDetalle $compraDetalle)
    {
        $compraDetalle = [
            'id' => $compraDetalle->id,
            'compra' => $compraDetalle->compra,
            'producto' => $compraDetalle->producto,
            'productoNombre' => optional($compraDetalle->productos)->nombre,
            'cantidad' => $compraDetalle->cantidad,
            'precioUnitario' => $compraDetalle->precioUnitario,
            'enDeposito' => $compraDetalle->enDeposito,
            'productoCodigo' => optional($compraDetalle->productos)->codigo
        ];

        return $compraDetalle;
    }
}
