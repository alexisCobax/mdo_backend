<?php

namespace App\Transformers\PedidoCotizacion;

class CreateTransformer
{
    public static function transform($cotizacion)
    {

        $cliente = [
            'fecha'=>$cotizacion->fecha,
            'cliente'=>$cotizacion->cliente,
            'estado'=>$cotizacion->estado,
            'vendedor'=>1,
            'formaDePago'=>1,
            'observaciones'=>'',
            'invoice'=>0,
            'total'=>$cotizacion->total,
            'descuentoPorcentual'=>'0.00',
            'totalEnvio'=>'0.00',
            'origen'=>1,
            'envioNombre'=>optional($cotizacion->clientes)->nombre,
            'envioPais'=>optional($cotizacion->clientes)->pais,
            'envioCiudad'=>optional($cotizacion->clientes)->ciudad,
            'envioDomicilio'=>optional($cotizacion->clientes)->direccionBill,
            'envioCp'=>optional($cotizacion->clientes)->codigoPostal,
            'descuentoNeto'=>$cotizacion->descuento,
            'idActiveCampaign'=>$cotizacion->idActiveCampaign,
            'transportadoraNombre'=>optional($cotizacion->clientes)->transportadora,
            'transportadoraTelefono'=>optional($cotizacion->clientes)->telefonoTransportadora,
        ];

        return $cliente;
    }
}
