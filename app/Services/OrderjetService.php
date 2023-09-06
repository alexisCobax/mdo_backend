<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Orderjet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderjetService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Orderjet::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Orderjet::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $orderjet = Orderjet::create($data);

        if (!$orderjet) {
            return response()->json(['error' => 'Failed to create Orderjet'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($orderjet, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $orderjet = Orderjet::find($request->id);

        if (!$orderjet) {
            return response()->json(['error' => 'Orderjet not found'], Response::HTTP_NOT_FOUND);
        }

        $orderjet->update($request->all());
        $orderjet->refresh();

        return response()->json($orderjet, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $orderjet = Orderjet::find($request->id);

        if (!$orderjet) {
            return response()->json(['error' => 'Orderjet not found'], Response::HTTP_NOT_FOUND);
        }

        $orderjet->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
