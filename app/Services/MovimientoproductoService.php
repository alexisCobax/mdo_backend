<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Movimientoproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MovimientoproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Movimientoproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Movimientoproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $movimientoproducto = Movimientoproducto::create($data);

        if (!$movimientoproducto) {
            return response()->json(['error' => 'Failed to create Movimientoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($movimientoproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $movimientoproducto = Movimientoproducto::find($request->id);

        if (!$movimientoproducto) {
            return response()->json(['error' => 'Movimientoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $movimientoproducto->update($request->all());
        $movimientoproducto->refresh();

        return response()->json($movimientoproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $movimientoproducto = Movimientoproducto::find($request->id);

        if (!$movimientoproducto) {
            return response()->json(['error' => 'Movimientoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $movimientoproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
