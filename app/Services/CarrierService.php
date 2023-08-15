<?php

namespace App\Services;

use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class CarrierService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Carrier::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Carrier::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $carrier = Carrier::create($data);

        if (!$carrier) {
            return response()->json(['error' => 'Failed to create Carrier'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($carrier, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $carrier = Carrier::find($request->id);

        if (!$carrier) {
            return response()->json(['error' => 'Carrier not found'], Response::HTTP_NOT_FOUND);
        }

        $carrier->update($request->all());
        $carrier->refresh();

        return response()->json($carrier, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $carrier = Carrier::find($request->id);

        if (!$carrier) {
            return response()->json(['error' => 'Carrier not found'], Response::HTTP_NOT_FOUND);
        }

        $carrier->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
