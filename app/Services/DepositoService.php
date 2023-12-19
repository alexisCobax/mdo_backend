<?php

namespace App\Services;

use App\Models\Deposito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;
use App\Models\Compradetalle;

class DepositoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Deposito::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Deposito::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $deposito = Deposito::create($data);

        if (!$deposito) {
            return response()->json(['error' => 'Failed to create Deposito'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($deposito, Response::HTTP_OK);
    }

    public function ingreso(Request $request)
    {
        $datosCompra = $request->all();
        $productos = $datosCompra['productos'];
        $productoIds = array_column($productos, 'producto');

        $productosModel = Producto::whereIn('id', $productoIds)->get();
        //dd($productos);
        foreach ($productos as $productoData) {
            // $producto = $productosModel->where('id', $productoData['producto'])->first();

            // if ($producto) {
            //     $nuevaCantidad = $producto->stock + $productoData['cantidad'];
            //     $producto->update([
            //         'stock' => $nuevaCantidad,
            //         'costo' => $productoData['precioUnitario'],
            //         'ultimoIngresoDeMercaderia' => now()->toDateString(),
            //     ]);
            // }
            echo $productoData['producto']."<br/>";

            // Compradetalle::where('producto', $productoData['producto'])
            //     ->update(['enDeposito' => '1']);
        }

        return response()->json(['mensaje' => 'Ingreso procesado con éxito']);
    }


    public function update(Request $request)
    {
        $deposito = Deposito::find($request->id);

        if (!$deposito) {
            return response()->json(['error' => 'Deposito not found'], Response::HTTP_NOT_FOUND);
        }

        $deposito->update($request->all());
        $deposito->refresh();

        return response()->json($deposito, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $deposito = Deposito::find($request->id);

        if (!$deposito) {
            return response()->json(['error' => 'Deposito not found'], Response::HTTP_NOT_FOUND);
        }

        $deposito->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
