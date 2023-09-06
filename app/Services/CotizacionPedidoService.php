<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\Cotizaciondetalle;
use App\Models\Pedido;
use App\Transformers\CotizacionPedido\CreateCotizacionTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotizacionPedidoService
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
        $cotizacion = Cotizacion::find($request->cotizacion)->first();

        $cotizacionTransformer = new CreateCotizacionTransformer();
        $cotizacionData = $cotizacionTransformer->transform($cotizacion);

        // $pedido = Pedido::create($cotizacionData);

        $cotizacionDetalle = Cotizaciondetalle::where('cotizacion', $request->cotizacion)->first();

        $foo = [
            'cotizacion'=>$cotizacionDetalle->cotizacion,
            'producto'=>$cotizacionDetalle->producto,
            'precio'=>$cotizacionDetalle->precio,
            'cantidad'=>$cotizacionDetalle->cantidad,
        ];

        dd($foo);

        //return response()->json($pedido, Response::HTTP_OK);
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
