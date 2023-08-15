<?php

namespace App\Services;

use App\Models\Pedidocupon;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PedidocuponService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pedidocupon::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Pedidocupon::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $pedidocupon = Pedidocupon::create($data);

        if (!$pedidocupon) {
            return response()->json(['error' => 'Failed to create Pedidocupon'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidocupon, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedidocupon = Pedidocupon::find($request->id);

        if (!$pedidocupon) {
            return response()->json(['error' => 'Pedidocupon not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidocupon->update($request->all());
        $pedidocupon->refresh();

        return response()->json($pedidocupon, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pedidocupon = Pedidocupon::find($request->id);

        if (!$pedidocupon) {
            return response()->json(['error' => 'Pedidocupon not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidocupon->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
