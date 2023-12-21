<?php

namespace App\Transformers\Compra;

use App\Helpers\DateHelper;
use App\Models\Compra;
use App\Models\Compradetalle;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Compra $compra)
    {

        $compra = [
            'id' => $compra->id,
            'proveedor' => $compra->proveedor,
            'nombreProveedor' => optional($compra->proveedores)->nombre,
            'fechaDeIngreso' => DateHelper::ToDateCustom($compra->fechaDeIngreso),
            'fechaDePago' => DateHelper::ToDateCustom($compra->fechaDePago),
            'precio' => $compra->precio,
            'numeroLote' => $compra->numeroLote,
            'observaciones' => $compra->observaciones,
            'pagado' => $compra->pagado,
            'enDeposito' => $compra->enDeposito,
        ];

        return $compra;
    }
}
