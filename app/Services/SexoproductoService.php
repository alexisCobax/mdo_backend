<?php

namespace App\Services;

use App\Models\Sexoproducto;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class SexoproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Sexoproducto::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Sexoproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $sexoproducto = Sexoproducto::create($data);

        if (!$sexoproducto) {
            return response()->json(['error' => 'Failed to create Sexoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($sexoproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $sexoproducto = Sexoproducto::find($request->id);

        if (!$sexoproducto) {
            return response()->json(['error' => 'Sexoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $sexoproducto->update($request->all());
        $sexoproducto->refresh();

        return response()->json($sexoproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $sexoproducto = Sexoproducto::find($request->id);

        if (!$sexoproducto) {
            return response()->json(['error' => 'Sexoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $sexoproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
