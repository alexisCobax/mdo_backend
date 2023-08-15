<?php

namespace App\Services;

use App\Models\Orderjetdevolucion;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class OrderjetdevolucionService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Orderjetdevolucion::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Orderjetdevolucion::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $orderjetdevolucion = Orderjetdevolucion::create($data);

        if (!$orderjetdevolucion) {
            return response()->json(['error' => 'Failed to create Orderjetdevolucion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($orderjetdevolucion, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $orderjetdevolucion = Orderjetdevolucion::find($request->id);

        if (!$orderjetdevolucion) {
            return response()->json(['error' => 'Orderjetdevolucion not found'], Response::HTTP_NOT_FOUND);
        }

        $orderjetdevolucion->update($request->all());
        $orderjetdevolucion->refresh();

        return response()->json($orderjetdevolucion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $orderjetdevolucion = Orderjetdevolucion::find($request->id);

        if (!$orderjetdevolucion) {
            return response()->json(['error' => 'Orderjetdevolucion not found'], Response::HTTP_NOT_FOUND);
        }

        $orderjetdevolucion->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
