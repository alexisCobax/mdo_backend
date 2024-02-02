<?php

namespace App\Transformers\Proveedores;

use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($proveedor)
    {   
        return [
            'id' => $proveedor->id,
            'nombre' => $proveedor->nombre,
            'direccion' => $proveedor->direccion,
            'ciudad' => $proveedor->ciudad,
            'codigoPostal' => $proveedor->codigoPostal,
            'telefono' => $proveedor->telefono,
            'movil' => $proveedor->movil,
            'email' => $proveedor->email,
            'fax' => $proveedor->fax,
            'contacto' => $proveedor->contacto,
            'transportadora' => optional($proveedor->transportadoras)->nombre,
            'telefonoTransportadora' => $proveedor->telefonoTransportadora,
            'observaciones' => $proveedor->observaciones,
            'formaDePago' => $proveedor->formaDePago,
            'suspendido' => $proveedor->suspendido,
        ];
    }
}
