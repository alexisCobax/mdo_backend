<?php

namespace App\Transformers\Prospecto;

use App\Models\Cliente;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{

    public function transform(Cliente $cliente)
    {
        $clientes = [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'direccion' => $cliente->direccion,
            'codigoPostal' => $cliente->codigoPostal,
            'telefono' => $cliente->telefono,
            'email' => $cliente->email,
            'fax' => $cliente->fax,
            'contacto' => $cliente->contacto,
            'puestoContacto' => $cliente->puestoContacto,
            'transportadora' => $cliente->transportadora,
            'telefonoTransportadora' => $cliente->telefonoTransportadora,
            'observaciones' => $cliente->observaciones,
            'usuario' => $cliente->usuario,
            'suspendido' => $cliente->suspendido,
            'web' => $cliente->web,
            'direccionShape' => $cliente->direccionShape,
            'direccionBill' => $cliente->direccionBill,
            'vendedor' => $cliente->vendedor,
            'ciudad' => $cliente->ciudad,
            'pais' => $cliente->pais,
            'usuarioVIP' => $cliente->usuarioVIP,
            'claveVIP' => $cliente->claveVIP,
            'VIP' => $cliente->VIP,
            'ctacte' => $cliente->ctacte,
            'cpShape' => $cliente->cpShape,
            'paisShape' => $cliente->paisShape,
            'primeraCompra' => $cliente->primeraCompra,
            'cantidadDeCompras' => $cliente->cantidadDeCompras,
            'idAgile' => $cliente->idAgile,
            'montoMaximoDePago' => $cliente->montoMaximoDePago,
            'WhatsApp' => $cliente->WhatsApp,
            'Notas' => $cliente->Notas,
            'tipoDeEnvio' => $cliente->tipoDeEnvio,
            'nombreEnvio' => $cliente->nombreEnvio,
            'regionEnvio' => $cliente->regionEnvio,
            'ciudadEnvio' => $cliente->ciudadEnvio,
            'fechaAlta' => $cliente->fechaAlta,
            'ipAlta' => $cliente->ipAlta,
            'ultimoLogin' => $cliente->ultimoLogin,
            'ipUltimoLogin' => $cliente->ipUltimoLogin,
            'prospecto' => $cliente->prospecto,
            'contactoApellido' => $cliente->contactoApellido,
            'IdActiveCampaign' => $cliente->IdActiveCampaign,
            'IdActiveCampaignContact' => $cliente->IdActiveCampaignContact,
            'notification' => $cliente->notification
        ];

        return $clientes;
    }
}
