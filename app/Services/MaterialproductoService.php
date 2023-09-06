<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Materialproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MaterialproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Materialproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Materialproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $materialproducto = Materialproducto::create($data);

        if (!$materialproducto) {
            return response()->json(['error' => 'Failed to create Materialproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($materialproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $materialproducto = Materialproducto::find($request->id);

        if (!$materialproducto) {
            return response()->json(['error' => 'Materialproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $materialproducto->update($request->all());
        $materialproducto->refresh();

        return response()->json($materialproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $materialproducto = Materialproducto::find($request->id);

        if (!$materialproducto) {
            return response()->json(['error' => 'Materialproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $materialproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
