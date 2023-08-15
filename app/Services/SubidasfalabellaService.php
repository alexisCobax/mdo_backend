<?php

namespace App\Services;

use App\Models\Subidasfalabella;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class SubidasfalabellaService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Subidasfalabella::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Subidasfalabella::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $subidasfalabella = Subidasfalabella::create($data);

        if (!$subidasfalabella) {
            return response()->json(['error' => 'Failed to create Subidasfalabella'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($subidasfalabella, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $subidasfalabella = Subidasfalabella::find($request->id);

        if (!$subidasfalabella) {
            return response()->json(['error' => 'Subidasfalabella not found'], Response::HTTP_NOT_FOUND);
        }

        $subidasfalabella->update($request->all());
        $subidasfalabella->refresh();

        return response()->json($subidasfalabella, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $subidasfalabella = Subidasfalabella::find($request->id);

        if (!$subidasfalabella) {
            return response()->json(['error' => 'Subidasfalabella not found'], Response::HTTP_NOT_FOUND);
        }

        $subidasfalabella->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
