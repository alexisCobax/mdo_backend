<?php

namespace App\Services;

use App\Models\Orderjetdevoluciondetalle;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class OrderjetdevoluciondetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Orderjetdevoluciondetalle::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Orderjetdevoluciondetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $orderjetdevoluciondetalle = Orderjetdevoluciondetalle::create($data);

        if (!$orderjetdevoluciondetalle) {
            return response()->json(['error' => 'Failed to create Orderjetdevoluciondetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($orderjetdevoluciondetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $orderjetdevoluciondetalle = Orderjetdevoluciondetalle::find($request->id);

        if (!$orderjetdevoluciondetalle) {
            return response()->json(['error' => 'Orderjetdevoluciondetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $orderjetdevoluciondetalle->update($request->all());
        $orderjetdevoluciondetalle->refresh();

        return response()->json($orderjetdevoluciondetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $orderjetdevoluciondetalle = Orderjetdevoluciondetalle::find($request->id);

        if (!$orderjetdevoluciondetalle) {
            return response()->json(['error' => 'Orderjetdevoluciondetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $orderjetdevoluciondetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
