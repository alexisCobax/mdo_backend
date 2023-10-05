<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Invoice;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use App\Models\Pedidodetalle;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Models\Invoicedetalle;
use App\Models\Cotizaciondetalle;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Invoices\CreateTransformer;
use App\Transformers\Invoices\FindByIdTransformer;
use App\Transformers\Invoices\CreateDetalleTransformer;

class CotizacionesWebService
{
    public function findAll(Request $request)
    {

        $user = Auth::user();

        $cliente = Cliente::where('usuario', $user->id)->first();

        try {
            $page = $request->input('pagina', env('PAGE'));
            $perPage = $request->input('cantidad', env('PER_PAGE'));

            $data = Cotizacion::where('cliente', $cliente->id)->where('estado',0)->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

            $response = [
                'status' => Response::HTTP_OK,
                'total' => $data->total(),
                'cantidad_por_pagina' => $data->perPage(),
                'pagina' => $data->currentPage(),
                'cantidad_total' => $data->total(),
                'results' => $data->items(),
            ];

            return response()->json(['data' => $response], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $invoice = Invoice::find($request->id);

        $invoiceTranformada = new FindByIdTransformer();
        $invoiceTranformada = $invoiceTranformada->transform($invoice);

        return response()->json(['data' => $invoiceTranformada], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $pedido = Pedido::find($request->pedido);

        $cantidad = Pedidodetalle::where('pedido', $request->pedido)->groupBy('pedido')
            ->selectRaw('pedido, SUM(cantidad) as suma_cantidad')
            ->get();

        $invoiceData = new CreateTransformer();
        $invoiceData = $invoiceData->transform($pedido, $cantidad, $request);
        $invoice = Invoice::create($invoiceData);

        $detalle = Pedidodetalle::where('pedido', $request->pedido)->get();

        $invoiceDetalle = new CreateDetalleTransformer();
        $invoiceTransformado = $invoiceDetalle->transform($detalle, $invoice->id);
        $invoiceDetalle = Invoicedetalle::insert($invoiceTransformado);

        if (!$invoice) {
            return response()->json(['error' => 'Failed to create Invoice'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($invoice, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $invoice->update($request->all());
        $invoice->refresh();

        return response()->json($invoice, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $invoice->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function procesar(Request $request)
    {

        $cotizacion = Cotizacion::where('id',$request->cotizacion)->first();

        $carritoExistente = CarritoHelper::getCarrito();

        $carrito = Carrito::where('id', $carritoExistente['id'])->first();
        $carrito->fecha = NOW();
        $carrito->cliente = $cotizacion->cliente;
        $carrito->estado = 0;
        $carrito->vendedor = 1;
        $carrito->save();

        if (Carritodetalle::where('carrito', '=', $carritoExistente['id'])->exists()) {

            Carritodetalle::where('carrito', '=', $carritoExistente['id'])->delete();
        }

        $cotizacionDetalle = Cotizaciondetalle::where('cotizacion', $request->cotizacion)->get();

        foreach ($cotizacionDetalle as $cd) {

            $carritoDetalle = new Carritodetalle;
            $carritoDetalle->carrito = $carritoExistente['id'];
            $carritoDetalle->producto = $cd['producto'];
            $carritoDetalle->precio = $cd['precio'];
            $carritoDetalle->cantidad = $cd['cantidad'];
            $carritoDetalle->save();
        }

        if (!$carrito) {
            return response()->json(['error' => 'Carrito not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            if ($cotizacion) {
                $cotizacion->estado = 1;
                $cotizacion->save();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return response()->json(['data' => $carrito], Response::HTTP_OK);
    }
}
