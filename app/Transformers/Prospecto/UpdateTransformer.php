<?php

namespace App\Transformers\Cliente;

class UpdateTransformer
{
    public static function transform($request, $usuarioId)
    {
        $cliente = [
            'nombre' => $request->nombre,
            'Notas' => $request->Notas,
            'WhatsApp' => $request->WhatsApp,
            'checkboxNotificarUsuario' => $request->checkboxNotificarUsuario,
            'ciudad' => $request->ciudad,
            'clave' => $request->clave,
            'codigoPostal' => $request->codigoPostal,
            'contacto' => $request->contacto,
            'contactoApellido' => $request->contactoApellido,
            'cpShape' => $request->cpShape,
            'direccion' => $request->direccion,
            'direccionBill' => $request->direccionBill,
            'direccionShape' => $request->direccionShape,
            'email' => $request->email,
            'estadoCliente' => $request->estadoCliente,
            'montoMaximoDePago' => $request->montoMaximoDePago,
            'observaciones' => $request->observaciones,
            'pais' => $request->pais,
            'paisShape' => $request->paisShape,
            'prospecto' => $request->prospecto,
            'puestoContacto' => $request->puestoContacto,
            'suspendido' => 0,
            'telefono' => $request->telefono,
            'telefonoTransportadora' => $request->telefonoTransportadora,
            'tipoDeEnvio' => $request->tipoDeEnvio,
            'transportadora' => $request->transportadora,
            'usuario' => $usuarioId,
            'vendedor' => $request->vendedor,
            'web' => $request->web,
            'notification' => $request->notification,
        ];

        return $cliente;
    }
}