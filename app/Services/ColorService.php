<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ColorService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Color::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Color::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $color = Color::create($data);

        if (!$color) {
            return response()->json(['error' => 'Failed to create Color'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($color, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $color = Color::find($request->id);

        if (!$color) {
            return response()->json(['error' => 'Color not found'], Response::HTTP_NOT_FOUND);
        }

        $color->update($request->all());
        $color->refresh();

        return response()->json($color, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $color = Color::find($request->id);

        if (!$color) {
            return response()->json(['error' => 'Color not found'], Response::HTTP_NOT_FOUND);
        }

        $color->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
