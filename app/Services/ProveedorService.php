<?php

namespace App\Services;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class ProveedorService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Proveedor::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Proveedor::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $proveedor = Proveedor::create($data);

        if (!$proveedor) {
            return response()->json(['error' => 'Failed to create Proveedor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($proveedor, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $proveedor = Proveedor::find($request->id);

        if (!$proveedor) {
            return response()->json(['error' => 'Proveedor not found'], Response::HTTP_NOT_FOUND);
        }

        $proveedor->update($request->all());
        $proveedor->refresh();

        return response()->json($proveedor, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $proveedor = Proveedor::find($request->id);

        if (!$proveedor) {
            return response()->json(['error' => 'Proveedor not found'], Response::HTTP_NOT_FOUND);
        }

        $proveedor->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
