<?php

namespace App\Services;

use App\Models\Plataforma;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PlataformaService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Plataforma::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Plataforma::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $plataforma = Plataforma::create($data);

        if (!$plataforma) {
            return response()->json(['error' => 'Failed to create Plataforma'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($plataforma, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $plataforma = Plataforma::find($request->id);

        if (!$plataforma) {
            return response()->json(['error' => 'Plataforma not found'], Response::HTTP_NOT_FOUND);
        }

        $plataforma->update($request->all());
        $plataforma->refresh();

        return response()->json($plataforma, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $plataforma = Plataforma::find($request->id);

        if (!$plataforma) {
            return response()->json(['error' => 'Plataforma not found'], Response::HTTP_NOT_FOUND);
        }

        $plataforma->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
