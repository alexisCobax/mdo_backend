<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Formadepago;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormadepagoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Formadepago::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Formadepago::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $formadepago = Formadepago::create($data);

        if (!$formadepago) {
            return response()->json(['error' => 'Failed to create Formadepago'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($formadepago, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $formadepago = Formadepago::find($request->id);

        if (!$formadepago) {
            return response()->json(['error' => 'Formadepago not found'], Response::HTTP_NOT_FOUND);
        }

        $formadepago->update($request->all());
        $formadepago->refresh();

        return response()->json($formadepago, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $formadepago = Formadepago::find($request->id);

        if (!$formadepago) {
            return response()->json(['error' => 'Formadepago not found'], Response::HTTP_NOT_FOUND);
        }

        $formadepago->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
