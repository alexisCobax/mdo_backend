<?php

namespace App\Services;

use App\Models\Empresatransportadora;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class EmpresatransportadoraService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Empresatransportadora::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Empresatransportadora::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $empresatransportadora = Empresatransportadora::create($data);

        if (!$empresatransportadora) {
            return response()->json(['error' => 'Failed to create Empresatransportadora'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($empresatransportadora, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $empresatransportadora = Empresatransportadora::find($request->id);

        if (!$empresatransportadora) {
            return response()->json(['error' => 'Empresatransportadora not found'], Response::HTTP_NOT_FOUND);
        }

        $empresatransportadora->update($request->all());
        $empresatransportadora->refresh();

        return response()->json($empresatransportadora, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $empresatransportadora = Empresatransportadora::find($request->id);

        if (!$empresatransportadora) {
            return response()->json(['error' => 'Empresatransportadora not found'], Response::HTTP_NOT_FOUND);
        }

        $empresatransportadora->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
