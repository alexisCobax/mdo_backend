<?php

namespace App\Transformers\Pedidos;

use App\Helpers\DateHelper;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Pedido $pedido)
    {
        // Subtotal = suma de (cantidad × precio) de cada línea, sin descuentos ni envío
        $subtotalDetalle = (float) Pedidodetalle::where('pedido', $pedido->id)
            ->get()
            ->sum(fn ($d) => (float) $d->precio * (int) $d->cantidad);
        $subtotalNN = (float) Pedidodetallenn::where('pedido', $pedido->id)
            ->get()
            ->sum(fn ($d) => (float) $d->precio * (int) $d->cantidad);
        $subtotal = round($subtotalDetalle + $subtotalNN, 2);

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
            'subtotal' => number_format($subtotal, 2),
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
