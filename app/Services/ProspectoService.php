<?php

namespace App\Services;

use App\Models\Prospecto;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class ProspectoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Prospecto::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Prospecto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $prospecto = Prospecto::create($data);

        if (!$prospecto) {
            return response()->json(['error' => 'Failed to create Prospecto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($prospecto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $prospecto = Prospecto::find($request->id);

        if (!$prospecto) {
            return response()->json(['error' => 'Prospecto not found'], Response::HTTP_NOT_FOUND);
        }

        $prospecto->update($request->all());
        $prospecto->refresh();

        return response()->json($prospecto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $prospecto = Prospecto::find($request->id);

        if (!$prospecto) {
            return response()->json(['error' => 'Prospecto not found'], Response::HTTP_NOT_FOUND);
        }

        $prospecto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
