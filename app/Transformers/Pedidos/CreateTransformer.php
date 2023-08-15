<?php

namespace App\Transformers\Pedidos;

use League\Fractal\TransformerAbstract;
use App\Helpers\DateHelper;

class CreateTransformer extends TransformerAbstract
{
    public function transform($request)
    {

        return [
            "cliente" => $request->cliente,
            "origen" => $request->origen,
            "vendedor" => $request->vendedor,
            "etapa" => $request->etapa,
            "observaciones" => $request->observaciones,
            "descuentoNeto" => $request->descuentoNeto,
            "total" => $request->total,
            "transportadoraNombre" => $request->transportadoraNombre,
            "transportadoraTelefono" => $request->transportadoraTelefono,
            "codigoSeguimiento" => $request->codigoSeguimiento,
            "idTransportadora" => $request->idTransportadora,
            "tipoDeEnvio" => $request->tipoDeEnvio,
            "envioNombre" => $request->envioNombre,
            "envioPais" => $request->envioPais,
            "envioRegion" => $request->envioRegion,
            "envioCiudad" => $request->envioCiudad,
            "envioDomicilio" => $request->envioDomicilio,
            "envioCp" => $request->envioCp,
            "fecha" => NOW(),
            "estado" => 1,
            "formaDePago" => 1,
        ];
    }
}
