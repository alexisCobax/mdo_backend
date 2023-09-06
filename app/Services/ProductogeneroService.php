<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Productogenero;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductogeneroService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Productogenero::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Productogenero::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $productogenero = Productogenero::create($data);

        if (!$productogenero) {
            return response()->json(['error' => 'Failed to create Productogenero'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($productogenero, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $productogenero = Productogenero::find($request->id);

        if (!$productogenero) {
            return response()->json(['error' => 'Productogenero not found'], Response::HTTP_NOT_FOUND);
        }

        $productogenero->update($request->all());
        $productogenero->refresh();

        return response()->json($productogenero, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $productogenero = Productogenero::find($request->id);

        if (!$productogenero) {
            return response()->json(['error' => 'Productogenero not found'], Response::HTTP_NOT_FOUND);
        }

        $productogenero->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
