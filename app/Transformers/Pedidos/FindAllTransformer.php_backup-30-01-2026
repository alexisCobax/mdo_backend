<?php

namespace App\Transformers\Pedidos;

use App\Helpers\DateHelper;
use App\Models\Pedido;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Pedido $pedido)
    {
        return [
            'id' => $pedido->id,
            'fecha' => DateHelper::ToDateCustom($pedido->fecha),
            'cliente' => $pedido->cliente,
            'nombreCliente' => optional($pedido->clientes)->nombre,
            'estado' => $pedido->estado,
            'nombreEstado' => optional($pedido->estadoPedido)->nombre,
            'vendedor' => $pedido->vendedor,
            'nombreEmpleado' => optional($pedido->vendedores)->nombre,
            'formaDePago' => $pedido->formaDePago,
            'nombreFormaDePago' => optional($pedido->formaDePagos)->nombre,
            'observaciones' => $pedido->observaciones,
            'invoice' => $pedido->invoice,
            'total' => number_format($pedido->total, 2),
            'descuentoPorcentual' => $pedido->descuentoPorcentual,
            'descuentoNeto' => $pedido->descuentoNeto,
            'totalEnvio' => $pedido->totalEnvio,
            'recibo' => $pedido->recibo,
            'origen' => $pedido->origen,
            'nombreOrigen' => optional($pedido->origenes)->nombre,
            'etapa' => $pedido->etapa,
            'nombreEtapa' => optional($pedido->etapas)->nombre,
            'tipoDeEnvio' => $pedido->tipoDeEnvio,
            'nombreTipoDeEnvio' =>  optional($pedido->tipoDeEnvios)->nombre,
            'envioNombre' => $pedido->envioNombre,
            'envioPais' => $pedido->envioPais,
            'envioRegion' => $pedido->envioRegion,
            'envioCiudad' => $pedido->envioCiudad,
            'envioDomicilio' => $pedido->envioDomicilio,
            'envioCp' => $pedido->envioCp,
            'idAgile' => $pedido->idAgile,
            'IdActiveCampaign' => $pedido->IdActiveCampaign,
            'transportadoraNombre' => $pedido->transportadoraNombre,
            'transportadoraTelefono' => $pedido->transportadoraTelefono,
            'codigoSeguimiento' => $pedido->codigoSeguimiento,
            'MailSeguimientoEnviado' => $pedido->MailSeguimientoEnviado,
        ];
    }
}
