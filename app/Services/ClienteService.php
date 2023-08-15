<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use App\Models\Usuario;
use Illuminate\Http\Response;
use App\Transformers\Cliente\CreateTransformer;

class ClienteService
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

    public function findById(Request $request)
    {
        $data = Cliente::find($request->id);

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
            "nombre" => $request->usuario,
            "clave" => $request->clave,
            "permisos" => 1,
            "suspendido" => 0
        ];

        $usuario = Usuario::create($usuario);

        if (!$usuario) {
            return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $transformer = new CreateTransformer();
        $cliente = $transformer->transform($request, 1);

        $cliente = Cliente::create($cliente);

        if (!$cliente) {
            return response()->json(['error' => 'Failed to create Cliente'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cliente, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $cliente = Cliente::findOrFail($request->id);
        $usuario = Usuario::findOrFail($cliente->usuario);

        $dataUsuario = [
            "id" => $usuario->id,
            "nombre" => $request->usuario,
            "clave" => $request->clave,
            "permisos" => 1,
            "suspendido" => 0
        ];

        $usuario->update($dataUsuario);

        $transformer = new CreateTransformer();
        $dataCliente = $transformer->transform($request, $cliente->usuario);

        $cliente->update($dataCliente);
        $usuario = Usuario::find($usuario->id);

        $response = [
            $cliente,
            "usuario" => [
                "nombre" => $usuario->nombre,
                "permisos" => $usuario->permisos,
                "suspendido" => $usuario->suspendido
            ]
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
