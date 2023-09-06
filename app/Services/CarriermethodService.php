<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Carriermethod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarriermethodService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Carriermethod::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Carriermethod::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $carriermethod = Carriermethod::create($data);

        if (!$carriermethod) {
            return response()->json(['error' => 'Failed to create Carriermethod'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($carriermethod, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $carriermethod = Carriermethod::find($request->id);

        if (!$carriermethod) {
            return response()->json(['error' => 'Carriermethod not found'], Response::HTTP_NOT_FOUND);
        }

        $carriermethod->update($request->all());
        $carriermethod->refresh();

        return response()->json($carriermethod, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $carriermethod = Carriermethod::find($request->id);

        if (!$carriermethod) {
            return response()->json(['error' => 'Carriermethod not found'], Response::HTTP_NOT_FOUND);
        }

        $carriermethod->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
