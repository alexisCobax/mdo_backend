<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Plataformaproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlataformaproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Plataformaproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Plataformaproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $plataformaproducto = Plataformaproducto::create($data);

        if (!$plataformaproducto) {
            return response()->json(['error' => 'Failed to create Plataformaproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($plataformaproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $plataformaproducto = Plataformaproducto::find($request->id);

        if (!$plataformaproducto) {
            return response()->json(['error' => 'Plataformaproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $plataformaproducto->update($request->all());
        $plataformaproducto->refresh();

        return response()->json($plataformaproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $plataformaproducto = Plataformaproducto::find($request->id);

        if (!$plataformaproducto) {
            return response()->json(['error' => 'Plataformaproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $plataformaproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
