<?php

namespace App\Services;

use App\Models\Marcafalabella;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class MarcafalabellaService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Marcafalabella::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Marcafalabella::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $marcafalabella = Marcafalabella::create($data);

        if (!$marcafalabella) {
            return response()->json(['error' => 'Failed to create Marcafalabella'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($marcafalabella, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $marcafalabella = Marcafalabella::find($request->id);

        if (!$marcafalabella) {
            return response()->json(['error' => 'Marcafalabella not found'], Response::HTTP_NOT_FOUND);
        }

        $marcafalabella->update($request->all());
        $marcafalabella->refresh();

        return response()->json($marcafalabella, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $marcafalabella = Marcafalabella::find($request->id);

        if (!$marcafalabella) {
            return response()->json(['error' => 'Marcafalabella not found'], Response::HTTP_NOT_FOUND);
        }

        $marcafalabella->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
