<?php

namespace App\Transformers\CotizacionPedido;

class CreateCotizacionTransformer
{
    public static function transform($cotizacion)
    {

        $cliente = [
            'fecha'=>date('Y-m-d'),
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
            'nombreEnvio'=>optional($cotizacion->clientes)->nombre,
            'paisEnvio'=>optional($cotizacion->clientes)->pais,
            'ciudadEnvio'=>optional($cotizacion->clientes)->ciudad,
            'domicilioEnvio'=>optional($cotizacion->clientes)->direccionBill,
            'cpEnvio'=>optional($cotizacion->clientes)->codigoPostal,
            'descuentoNeto'=>$cotizacion->descuento,
            'idActiveCampaign'=>$cotizacion->idActiveCampaign,
            'transportadoraNombre'=>optional($cotizacion->clientes)->transportadora,
            'transportadoraTelefono'=>optional($cotizacion->clientes)->telefonoTransportadora,
        ];

        return $cliente;
    }
}
