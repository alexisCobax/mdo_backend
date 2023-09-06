<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Tipobanner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TipobannerService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Tipobanner::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Tipobanner::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $tipobanner = Tipobanner::create($data);

        if (!$tipobanner) {
            return response()->json(['error' => 'Failed to create Tipobanner'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($tipobanner, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $tipobanner = Tipobanner::find($request->id);

        if (!$tipobanner) {
            return response()->json(['error' => 'Tipobanner not found'], Response::HTTP_NOT_FOUND);
        }

        $tipobanner->update($request->all());
        $tipobanner->refresh();

        return response()->json($tipobanner, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $tipobanner = Tipobanner::find($request->id);

        if (!$tipobanner) {
            return response()->json(['error' => 'Tipobanner not found'], Response::HTTP_NOT_FOUND);
        }

        $tipobanner->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
