<?php

namespace App\Transformers\Pedidos;

use League\Fractal\TransformerAbstract;

class CreateTransformer extends TransformerAbstract
{
    public function transform($request)
    {

        return [
            'cliente' => $request->cliente,
            'origen' => $request->origen,
            'vendedor' => $request->vendedor,
            'etapa' => $request->etapa,
            'observaciones' => $request->observaciones,
            'descuentoNeto' => $request->descuentoNeto,
            'descuentoPorcentual' => $request->descuentoPorPorcentaje,
            'descuentoPromociones' => $request->descuentoPorPromocionesOff,
            'totalEnvio' => $request->totalEnvio,
            'total' => $request->total,
            'transportadoraNombre' => $request->transportadoraNombre,
            'transportadoraTelefono' => $request->transportadoraTelefono,
            'codigoSeguimiento' => $request->codigoSeguimiento,
            'idTransportadora' => $request->idTransportadora,
            'tipoDeEnvio' => $request->tipoDeEnvio,
            'nombreEnvio' => $request->envioNombre,
            'paisEnvio' => $request->envioPais,
            'regionEnvio' => $request->envioRegion,
            'ciudadEnvio' => $request->envioCiudad,
            'domicilioEnvio' => $request->envioDomicilio,
            'cpEnvio' => $request->envioCp,
            'fecha' => NOW(),
            'estado' => 1,
            'formaDePago' => 1,
        ];
    }
}
