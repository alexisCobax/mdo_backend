<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Mail\EnvioCotizacionMailSinAdjunto;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Transformers\Cliente\CreateWebTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClienteWebService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Cliente::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los clientes'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByToken(Request $request)
    {
        $user = Auth::user();

        $data = Cliente::with('usuarios')->where('usuario', $user->id)->first();

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $existeEmail = Cliente::where('email', $request->email)->count();
        if ($existeEmail != 0) {
            return response()->json(['error' => 'Email existente', 'status' => 203], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        $existeUsuario = Usuario::where('nombre', $request->usuario)->count();
        if ($existeUsuario != 0) {
            return response()->json(['error' => 'Usuario existente', 'status' => 203], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        $usuario = [
            'nombre' => $request->email,
            'clave' => $request->clave ? Hash::make($request->clave) : str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
            'permisos' => 2,
            'suspendido' => 0,
        ];

        $usuario = Usuario::create($usuario);

        if (!$usuario) {
            return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $payload = [
            'contact' => [
                'email' => $request->email,
                'firstName' => $request->nombre,
                'lastName' => $request->nombre,
                'phone' => $request->telefono,
                'fieldValues' => [
                    [
                        'field' => '17',
                        'value' => '9',
                    ],
                ],
            ],
        ];

        $postData = json_encode($payload);

        // $activeCampaign = new ActiveCampaignService;

        // $response =  $activeCampaign->post('https://cobax1694091376.api-us1.com/api/3/contacts', $postData);
        // $response = json_decode($response, true);

        // $transformer = new CreateWebTransformer();
        // $cliente = $transformer->transform($request, $usuario->id, $response['contact']['id']);

        $transformer = new CreateWebTransformer();
        $cliente = $transformer->transform($request, $usuario->id);
        $nombre = (!empty($request->nombre)) ? $request->nombre : '';

        $cliente = Cliente::create($cliente);

        if (!$cliente) {
            return response()->json(['error' => 'Failed to create Cliente'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /* Envio email a cliente **/

        try {
            $cuerpo = 'pdf.alta_cliente_prospecto';
            $subject = 'Cotizacion';
            $destinatarios = [
                $request->email,
            ];

            Mail::to($destinatarios)->send(new EnvioCotizacionMailSinAdjunto($cuerpo, $subject, $nombre));

            return response()->json(['Response' => 'Enviado Correctamente'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cliente, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        $user = Auth::user();

        $usuario = Usuario::findOrFail($user->id);
        $cliente = Cliente::where('usuario', $user->id);

        if ($request->usuario) {
            $dataUsuario = [
                'id' => $usuario->id,
                'nombre' => $request->usuario,
                'permisos' => 1,
                'suspendido' => 0,
            ];

            $usuario->update($dataUsuario);
        }

        if ($request->clave) {
            $dataUsuario = [
                'id' => $usuario->id,
                'clave' => $request->clave,
                'permisos' => 1,
                'suspendido' => 0,
            ];

            $usuario->update($dataUsuario);
        }

        if ($cliente) {
            $camposActualizables = [
                'nombre', 'direccion', 'codigoPostal', 'telefono', 'email',
                'fax', 'contacto', 'puestoContacto', 'transportadora', 'telefonoTransportadora',
                'observaciones', 'usuario', 'suspendido', 'web', 'direccionShape',
                'direccionBill', 'vendedor', 'ciudad', 'pais', 'usuarioVIP',
                'claveVIP', 'VIP', 'ctacte', 'cpShape', 'paisShape',
                'primeraCompra', 'cantidadDeCompras', 'idAgile', 'montoMaximoDePago', 'WhatsApp',
                'Notas', 'tipoDeEnvio', 'nombreEnvio', 'regionEnvio', 'ciudadEnvio',
                'fechaAlta', 'ipAlta', 'ultimoLogin', 'ipUltimoLogin', 'prospecto',
                'contactoApellido', 'IdActiveCampaign', 'IdActiveCampaignContact', 'notification',
            ];
            $dataCliente = $request->only($camposActualizables);

            $cliente->update($dataCliente);
        }

        $response = [
            $cliente->get(),
            'usuario' => [
                'nombre' => $usuario->nombre,
                'permisos' => $usuario->permisos,
                'suspendido' => $usuario->suspendido,
            ],
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cliente = Cliente::find($request->id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], Response::HTTP_NOT_FOUND);
        }

        $cliente->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
