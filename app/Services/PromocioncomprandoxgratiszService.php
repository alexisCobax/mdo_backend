<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Promocioncomprandoxgratisz;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PromocioncomprandoxgratiszService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Promocioncomprandoxgratisz::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Promocioncomprandoxgratisz::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $promocioncomprandoxgratisz = Promocioncomprandoxgratisz::create($data);

        if (!$promocioncomprandoxgratisz) {
            return response()->json(['error' => 'Failed to create Promocioncomprandoxgratisz'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($promocioncomprandoxgratisz, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $promocioncomprandoxgratisz = Promocioncomprandoxgratisz::find($request->id);

        if (!$promocioncomprandoxgratisz) {
            return response()->json(['error' => 'Promocioncomprandoxgratisz not found'], Response::HTTP_NOT_FOUND);
        }

        $promocioncomprandoxgratisz->update($request->all());
        $promocioncomprandoxgratisz->refresh();

        return response()->json($promocioncomprandoxgratisz, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $promocioncomprandoxgratisz = Promocioncomprandoxgratisz::find($request->id);

        if (!$promocioncomprandoxgratisz) {
            return response()->json(['error' => 'Promocioncomprandoxgratisz not found'], Response::HTTP_NOT_FOUND);
        }

        $promocioncomprandoxgratisz->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
