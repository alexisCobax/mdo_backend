<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use App\Transformers\PedidoCotizacion\CreateTransformer;

class PedidoCotizacionService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {
        //--
    }

    public function create(Request $request)
    {
        echo 2;die;
        $pedido = Pedido::find($request->cotizacion)->first();

        // $cotizacionTransformer = new CreateTransformer;
        // $cotizacionData = $cotizacionTransformer->transform($cotizacion); 

        // $pedido = Pedido::create($cotizacionData);

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        //   return response()->json($cotizacion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        // return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
