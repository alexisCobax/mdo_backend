<?php

namespace App\Services;

use App\Models\Comision;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class ComisionService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Comision::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Comision::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $comision = Comision::create($data);

        if (!$comision) {
            return response()->json(['error' => 'Failed to create Comision'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($comision, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $comision = Comision::find($request->id);

        if (!$comision) {
            return response()->json(['error' => 'Comision not found'], Response::HTTP_NOT_FOUND);
        }

        $comision->update($request->all());
        $comision->refresh();

        return response()->json($comision, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $comision = Comision::find($request->id);

        if (!$comision) {
            return response()->json(['error' => 'Comision not found'], Response::HTTP_NOT_FOUND);
        }

        $comision->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
