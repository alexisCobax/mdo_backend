<?php

namespace App\Services;

use App\Models\Jet;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class JetService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Jet::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Jet::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $jet = Jet::create($data);

        if (!$jet) {
            return response()->json(['error' => 'Failed to create Jet'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($jet, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $jet = Jet::find($request->id);

        if (!$jet) {
            return response()->json(['error' => 'Jet not found'], Response::HTTP_NOT_FOUND);
        }

        $jet->update($request->all());
        $jet->refresh();

        return response()->json($jet, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $jet = Jet::find($request->id);

        if (!$jet) {
            return response()->json(['error' => 'Jet not found'], Response::HTTP_NOT_FOUND);
        }

        $jet->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
