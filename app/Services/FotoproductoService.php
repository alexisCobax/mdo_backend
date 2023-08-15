<?php

namespace App\Services;

use App\Models\Fotoproducto;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class FotoproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Fotoproducto::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Fotoproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $fotoproducto = Fotoproducto::create($data);

        if (!$fotoproducto) {
            return response()->json(['error' => 'Failed to create Fotoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($fotoproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $fotoproducto = Fotoproducto::find($request->id);

        if (!$fotoproducto) {
            return response()->json(['error' => 'Fotoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $fotoproducto->update($request->all());
        $fotoproducto->refresh();

        return response()->json($fotoproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $fotoproducto = Fotoproducto::find($request->id);

        if (!$fotoproducto) {
            return response()->json(['error' => 'Fotoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $fotoproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
