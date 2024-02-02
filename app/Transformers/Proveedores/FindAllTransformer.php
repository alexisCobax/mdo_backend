<?php

namespace App\Transformers\Proveedores;

use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($producto)
    {   
        return [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'direccion' => $producto->direccion,
            'ciudad' => $producto->ciudad,
            'codigoPostal' => $producto->codigoPostal,
            'telefono' => $producto->telefono,
            'movil' => $producto->movil,
            'email' => $producto->email,
            'fax' => $producto->fax,
            'contacto' => $producto->contacto,
            'transportadora' => $producto->transportadora,
            'telefonoTransportadora' => $producto->telefonoTransportadora,
            'observaciones' => $producto->observaciones,
            'formaDePago' => $producto->formaDePago,
            'suspendido' => $producto->suspendido,
        ];
    }
}
