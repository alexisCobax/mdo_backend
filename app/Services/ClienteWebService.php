<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Cliente\CreateTransformer;
use App\Transformers\Cliente\CreateWebTransformer;
use App\Transformers\Cliente\UpdateTransformer;

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

        $data = Cliente::where('usuario', $user->id)->first();

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
            'nombre' => $request->usuario,
            'clave' => $request->clave,
            'permisos' => 1,
            'suspendido' => 0,
        ];

        $usuario = Usuario::create($usuario);

        if (!$usuario) {
            return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $transformer = new CreateWebTransformer();
        $cliente = $transformer->transform($request, $usuario->id);

        $cliente = Cliente::create($cliente);

        if (!$cliente) {
            return response()->json(['error' => 'Failed to create Cliente'], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                'clave' => $request->clave,
                'permisos' => 1,
                'suspendido' => 0,
            ];

            $usuario->update($dataUsuario);
        }

        // $transformer = new UpdateTransformer();
        // $dataCliente = $transformer->transform($request, $user->id);

        $dataCliente = [];

            // Lista de campos que se pueden actualizar
            $camposActualizables = [
                'nombre', 'Notas', 'WhatsApp', 'checkboxNotificarUsuario',
                'ciudad', 'clave', 'codigoPostal', 'contacto', 'contactoApellido',
                'cpShape', 'direccion', 'direccionBill', 'direccionShape', 'email',
                'estadoCliente', 'montoMaximoDePago', 'observaciones', 'pais',
                'paisShape', 'prospecto', 'puestoContacto', 'telefono',
                'telefonoTransportadora', 'tipoDeEnvio', 'transportadora',
                'usuario', 'vendedor', 'web', 'notification'
            ];

            // Recorre los campos y agrega solo los que están presentes en la solicitud
            foreach ($camposActualizables as $campo) {
                if ($request->has($campo)) {
                    $dataCliente[$campo] = $request->input($campo);
                }
            }

            // Realiza el update solo con los campos presentes en $dataCliente
            $cliente->update($dataCliente);

        // $cliente->update($dataCliente);

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
