<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Categoriaproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriaproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Categoriaproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Categoriaproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $categoriaproducto = Categoriaproducto::create($data);

        if (!$categoriaproducto) {
            return response()->json(['error' => 'Failed to create Categoriaproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($categoriaproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $categoriaproducto = Categoriaproducto::find($request->id);

        if (!$categoriaproducto) {
            return response()->json(['error' => 'Categoriaproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $categoriaproducto->update($request->all());
        $categoriaproducto->refresh();

        return response()->json($categoriaproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $categoriaproducto = Categoriaproducto::find($request->id);

        if (!$categoriaproducto) {
            return response()->json(['error' => 'Categoriaproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $categoriaproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
