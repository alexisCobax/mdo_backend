<?php

namespace App\Services;

use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class EmpleadoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Empleado::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Empleado::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $empleado = Empleado::create($data);

        if (!$empleado) {
            return response()->json(['error' => 'Failed to create Empleado'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($empleado, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $empleado = Empleado::find($request->id);

        if (!$empleado) {
            return response()->json(['error' => 'Empleado not found'], Response::HTTP_NOT_FOUND);
        }

        $empleado->update($request->all());
        $empleado->refresh();

        return response()->json($empleado, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $empleado = Empleado::find($request->id);

        if (!$empleado) {
            return response()->json(['error' => 'Empleado not found'], Response::HTTP_NOT_FOUND);
        }

        $empleado->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
