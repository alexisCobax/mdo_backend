<?php

namespace App\Transformers\Cotizacion;

use App\Helpers\DateHelper;
use App\Models\Cotizacion;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Cotizacion $cotizacion)
    {
        return [
            'id' => $cotizacion->id,
            'fecha' => DateHelper::ToDateCustom($cotizacion->fecha),
            'cliente' => $cotizacion->cliente,
            'clienteNombre' => optional($cotizacion->clientes)->nombre,
            'total' => number_format($cotizacion->total,2),
            'estado' => $cotizacion->estado,
            'IdActiveCampaign' => $cotizacion->IdActiveCampaign,
            'descuento' => $cotizacion->descuento,
            'subTotal' => number_format($cotizacion->subTotal,2),
        ];
    }
}
