<?php

namespace App\Services;

use App\Filters\Pedidos\PedidosFilters;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use App\Transformers\Pedidos\CreateTransformer;
use App\Transformers\Pedidos\FindByIdTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PedidoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PedidosFilters::getPaginatePedidos($request, Pedido::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        {
            try {
                $transformer = new FindByIdTransformer($request);
                $transformer = $transformer->transform();

                return response()->json(['data' => $transformer], Response::HTTP_OK);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Ocurrió un error al obtener el pedido', $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function create(Request $request)
    {
        $transformer = new CreateTransformer();
        $pedidoTransformer = $transformer->transform($request);

        $pedido = Pedido::create($pedidoTransformer);

        if ($request->detalle) {
            $detalle = collect($request->detalle)->map(function ($dt) use ($pedido) {
                return [
                    'pedido' => $pedido->id,
                    'producto' => $dt['producto'],
                    'precio' => $dt['precio'],
                    'cantidad' => $dt['cantidad'],
                    'costo' => '00.00',
                    'envio' => '00.00',
        /*** 
         * tax y taxEnvio dejar siempre en 0.00 para futuras actualizaciones
         ***/
                    'tax' => '00.00',
                    'taxEnvio' => '00.00',
                ];
            });

            Pedidodetalle::insert($detalle->toArray());
        }

        if ($request->detalleNN) {
            $detalleNN = collect($request->detalleNN)->map(function ($dtNN) use ($pedido) {
                return [
                    'descripcion' => $dtNN['descripcion'],
                    'precio' => $dtNN['precio'],
                    'pedido' => $pedido->id,
                    'cantidad' => $dtNN['cantidad'],
                ];
            });

            Pedidodetallenn::insert($detalleNN->toArray());
        }

        // $totalDetalle = Pedidodetalle::where('pedido', $pedido->id)->sum('precio');
        // $totalDetalleNN = Pedidodetallenn::where('pedido', $pedido->id)->sum('precio');
        // $total = $totalDetalle + $totalDetalleNN;

        // $upPedido = Pedido::find($pedido->id);
        // $upPedido->total = $total;
        // $upPedido->save();

        if (!$pedido) {
            return response()->json(['error' => 'Failed to create Pedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedido = Pedido::find($request->id);

        if (!$pedido) {
            return response()->json(['error' => 'Pedido not found'], Response::HTTP_NOT_FOUND);
        }

        $transformer = app(CreateTransformer::class);
        $pedidoTransformer = $transformer->transform($request);

        $pedido->update($pedidoTransformer);

        if ($request->detalle) {
            $detalle = collect($request->detalle)->map(function ($dt) use ($pedido) {
                return [
                    'pedido' => $pedido->id,
                    'producto' => $dt['producto'],
                    'precio' => $dt['precio'],
                    'cantidad' => $dt['cantidad'],
                    'costo' => $dt['costo'],
                    'envio' => $dt['envio'],
                    'tax' => $dt['tax'],
                    'taxEnvio' => $dt['taxEnvio'],
                ];
            });

            Pedidodetalle::where('pedido', $pedido->id)->delete();
            Pedidodetalle::insert($detalle->toArray());
        }

        if ($request->detalleNN) {
            $detalleNN = collect($request->detalleNN)->map(function ($dtNN) use ($pedido) {
                return [
                    'descripcion' => $dtNN['descripcion'],
                    'precio' => $dtNN['precio'],
                    'pedido' => $pedido->id,
                    'cantidad' => $dtNN['cantidad'],
                ];
            });

            Pedidodetallenn::where('pedido', $pedido->id)->delete();
            Pedidodetallenn::insert($detalleNN->toArray());
        }

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pedido = Pedido::find($request->id);

        if (!$pedido) {
            return response()->json(['error' => 'Pedido not found'], Response::HTTP_NOT_FOUND);
        }

        $pedido->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
