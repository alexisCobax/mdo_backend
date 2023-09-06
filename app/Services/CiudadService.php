<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CiudadService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Ciudad::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Ciudad::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $ciudad = Ciudad::create($data);

        if (!$ciudad) {
            return response()->json(['error' => 'Failed to create Ciudad'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($ciudad, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $ciudad = Ciudad::find($request->id);

        if (!$ciudad) {
            return response()->json(['error' => 'Ciudad not found'], Response::HTTP_NOT_FOUND);
        }

        $ciudad->update($request->all());
        $ciudad->refresh();

        return response()->json($ciudad, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $ciudad = Ciudad::find($request->id);

        if (!$ciudad) {
            return response()->json(['error' => 'Ciudad not found'], Response::HTTP_NOT_FOUND);
        }

        $ciudad->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
