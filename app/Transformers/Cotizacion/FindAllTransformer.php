<?php

namespace App\Transformers\Cotizacion;

use App\Models\Cotizacion;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Cotizacion $cotizacion)
    {
        return [
            'id' => $cotizacion->id,
            'fecha' => $cotizacion->fecha,
            'cliente' => $cotizacion->cliente,
            'clienteNombre' => optional($cotizacion->clientes)->nombre,
            'total' => $cotizacion->total,
            'estado' => $cotizacion->estado,
            'IdActiveCampaign' => $cotizacion->IdActiveCampaign,
            'descuento' => $cotizacion->descuento,
            'subTotal' => $cotizacion->subTotal,
        ];
    }
}
