<?php

namespace App\Services;

use App\Models\Invoicedetalle;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class InvoicedetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Invoicedetalle::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Invoicedetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $invoicedetalle = Invoicedetalle::create($data);

        if (!$invoicedetalle) {
            return response()->json(['error' => 'Failed to create Invoicedetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($invoicedetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $invoicedetalle = Invoicedetalle::find($request->id);

        if (!$invoicedetalle) {
            return response()->json(['error' => 'Invoicedetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $invoicedetalle->update($request->all());
        $invoicedetalle->refresh();

        return response()->json($invoicedetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $invoicedetalle = Invoicedetalle::find($request->id);

        if (!$invoicedetalle) {
            return response()->json(['error' => 'Invoicedetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $invoicedetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
