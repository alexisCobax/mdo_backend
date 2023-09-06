<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Tipoproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TipoproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Tipoproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Tipoproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $tipoproducto = Tipoproducto::create($data);

        if (!$tipoproducto) {
            return response()->json(['error' => 'Failed to create Tipoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($tipoproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $tipoproducto = Tipoproducto::find($request->id);

        if (!$tipoproducto) {
            return response()->json(['error' => 'Tipoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $tipoproducto->update($request->all());
        $tipoproducto->refresh();

        return response()->json($tipoproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $tipoproducto = Tipoproducto::find($request->id);

        if (!$tipoproducto) {
            return response()->json(['error' => 'Tipoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $tipoproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
