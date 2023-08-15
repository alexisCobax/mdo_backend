<?php

namespace App\Services;

use App\Models\Puesto;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PuestoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Puesto::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Puesto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $puesto = Puesto::create($data);

        if (!$puesto) {
            return response()->json(['error' => 'Failed to create Puesto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($puesto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $puesto = Puesto::find($request->id);

        if (!$puesto) {
            return response()->json(['error' => 'Puesto not found'], Response::HTTP_NOT_FOUND);
        }

        $puesto->update($request->all());
        $puesto->refresh();

        return response()->json($puesto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $puesto = Puesto::find($request->id);

        if (!$puesto) {
            return response()->json(['error' => 'Puesto not found'], Response::HTTP_NOT_FOUND);
        }

        $puesto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
