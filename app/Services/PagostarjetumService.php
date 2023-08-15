<?php

namespace App\Services;

use App\Models\Pagostarjetum;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PagostarjetumService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pagostarjetum::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Pagostarjetum::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $pagostarjetum = Pagostarjetum::create($data);

        if (!$pagostarjetum) {
            return response()->json(['error' => 'Failed to create Pagostarjetum'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pagostarjetum, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pagostarjetum = Pagostarjetum::find($request->id);

        if (!$pagostarjetum) {
            return response()->json(['error' => 'Pagostarjetum not found'], Response::HTTP_NOT_FOUND);
        }

        $pagostarjetum->update($request->all());
        $pagostarjetum->refresh();

        return response()->json($pagostarjetum, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pagostarjetum = Pagostarjetum::find($request->id);

        if (!$pagostarjetum) {
            return response()->json(['error' => 'Pagostarjetum not found'], Response::HTTP_NOT_FOUND);
        }

        $pagostarjetum->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
