<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Recibo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReciboService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Recibo::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Recibo::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $recibo = Recibo::create($data);

        if (!$recibo) {
            return response()->json(['error' => 'Failed to create Recibo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($recibo, Response::HTTP_OK);
    }

    public function createOnly(Request $request)
    {

        $payload = [
            'cliente' => $request->cliente,
            'fecha' => NOW(),
            'formaDePago' => $request->formaDePago,
            'total' => $request->total,
            'anulado' => 0,
            'observaciones' => $request->observaciones,
            'pedido' => 0,
            'garantia' => 0
        ];

        $recibo = Recibo::create($payload);

        if (!$recibo) {
            return response()->json(['error' => 'Failed to create Recibo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($recibo, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $recibo = Recibo::find($request->id);

        if (!$recibo) {
            return response()->json(['error' => 'Recibo not found'], Response::HTTP_NOT_FOUND);
        }

        $recibo->update($request->all());
        $recibo->refresh();

        return response()->json($recibo, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $recibo = Recibo::find($request->id);

        if (!$recibo) {
            return response()->json(['error' => 'Recibo not found'], Response::HTTP_NOT_FOUND);
        }

        $recibo->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
