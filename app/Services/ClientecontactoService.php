<?php

namespace App\Services;

use App\Models\Clientecontacto;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class ClientecontactoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Clientecontacto::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Clientecontacto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $clientecontacto = Clientecontacto::create($data);

        if (!$clientecontacto) {
            return response()->json(['error' => 'Failed to create Clientecontacto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($clientecontacto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $clientecontacto = Clientecontacto::find($request->id);

        if (!$clientecontacto) {
            return response()->json(['error' => 'Clientecontacto not found'], Response::HTTP_NOT_FOUND);
        }

        $clientecontacto->update($request->all());
        $clientecontacto->refresh();

        return response()->json($clientecontacto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $clientecontacto = Clientecontacto::find($request->id);

        if (!$clientecontacto) {
            return response()->json(['error' => 'Clientecontacto not found'], Response::HTTP_NOT_FOUND);
        }

        $clientecontacto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
