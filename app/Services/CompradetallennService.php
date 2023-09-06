<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Compradetallenn;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompradetallennService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Compradetallenn::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Compradetallenn::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $compradetallenn = Compradetallenn::create($data);

        if (!$compradetallenn) {
            return response()->json(['error' => 'Failed to create Compradetallenn'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($compradetallenn, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $compradetallenn = Compradetallenn::find($request->id);

        if (!$compradetallenn) {
            return response()->json(['error' => 'Compradetallenn not found'], Response::HTTP_NOT_FOUND);
        }

        $compradetallenn->update($request->all());
        $compradetallenn->refresh();

        return response()->json($compradetallenn, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $compradetallenn = Compradetallenn::find($request->id);

        if (!$compradetallenn) {
            return response()->json(['error' => 'Compradetallenn not found'], Response::HTTP_NOT_FOUND);
        }

        $compradetallenn->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
