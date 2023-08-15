<?php

namespace App\Services;

use App\Models\Zipcode;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class ZipcodeService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Zipcode::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Zipcode::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $zipcode = Zipcode::create($data);

        if (!$zipcode) {
            return response()->json(['error' => 'Failed to create Zipcode'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($zipcode, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $zipcode = Zipcode::find($request->id);

        if (!$zipcode) {
            return response()->json(['error' => 'Zipcode not found'], Response::HTTP_NOT_FOUND);
        }

        $zipcode->update($request->all());
        $zipcode->refresh();

        return response()->json($zipcode, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $zipcode = Zipcode::find($request->id);

        if (!$zipcode) {
            return response()->json(['error' => 'Zipcode not found'], Response::HTTP_NOT_FOUND);
        }

        $zipcode->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
