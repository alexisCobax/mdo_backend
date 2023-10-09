<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Helpers\PaginateHelper;
use App\Models\Recibo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $recibo = Recibo::where('pedido', $request->id)->first();

        $data = [
            'recibo' => [
                "numero" => $recibo->id,
                "cliente" => optional($recibo->clientes)->nombre,
                "fecha" => DateHelper::ToDateCustom($recibo->fecha),
                "total" => $recibo->total,
                "observaciones" => $recibo->observaciones,
                "formaPago" => optional($recibo->formasPago)->nombre
            ]
        ];

        $pdf = Pdf::loadView('pdf.recibo', $data);

        $pdf->getDomPDF();

        return $pdf->stream();

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

    public function createOne(Request $request)
    {

        $recibo = [
            "cliente" => $request->cliente,
            "formaDePago" => $request->formaDePago,
            "total" => $request->total,
            "observaciones" => $request->observaciones,
            "pedido" => 0,
            "garantia" => 0,
            "anulado" => 0,
            "fecha" => NOW()
        ];

        $recibo = Recibo::create($recibo);

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
