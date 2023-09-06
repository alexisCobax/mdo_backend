<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Deposito;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepositoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Deposito::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Deposito::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $deposito = Deposito::create($data);

        if (!$deposito) {
            return response()->json(['error' => 'Failed to create Deposito'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($deposito, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $deposito = Deposito::find($request->id);

        if (!$deposito) {
            return response()->json(['error' => 'Deposito not found'], Response::HTTP_NOT_FOUND);
        }

        $deposito->update($request->all());
        $deposito->refresh();

        return response()->json($deposito, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $deposito = Deposito::find($request->id);

        if (!$deposito) {
            return response()->json(['error' => 'Deposito not found'], Response::HTTP_NOT_FOUND);
        }

        $deposito->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
