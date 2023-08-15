<?php

namespace App\Services;

use App\Models\Categoriafalabella;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use App\Transformers\CategoriaFalabella\FindAllTransformer;
use Illuminate\Http\Response;

class CategoriafalabellaService
{
    public function findAll()
    {
        $transformer = new FindAllTransformer();

        try {
            return response()->json(['data' => $transformer->transform()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Categoriafalabella::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $categoriafalabella = Categoriafalabella::create($data);

        if (!$categoriafalabella) {
            return response()->json(['error' => 'Failed to create Categoriafalabella'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($categoriafalabella, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $categoriafalabella = Categoriafalabella::find($request->id);

        if (!$categoriafalabella) {
            return response()->json(['error' => 'Categoriafalabella not found'], Response::HTTP_NOT_FOUND);
        }

        $categoriafalabella->update($request->all());
        $categoriafalabella->refresh();

        return response()->json($categoriafalabella, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $categoriafalabella = Categoriafalabella::find($request->id);

        if (!$categoriafalabella) {
            return response()->json(['error' => 'Categoriafalabella not found'], Response::HTTP_NOT_FOUND);
        }

        $categoriafalabella->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
