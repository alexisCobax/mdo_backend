<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Deposito;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Compradetalle;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;

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
        
        $cantidadProductosNoDeposito = 0;
        
        foreach ($productos as $productoData) {
            $producto = Producto::find($productoData['productoId']);
        
            if ($producto) {
                $nuevaCantidad = $producto->stock;
        
                if ($productoData['isChecked']) {
                    $nuevaCantidad += $productoData['cantidad'];
                } else {
                    $nuevaCantidad -= $productoData['cantidad'];
                    $cantidadProductosNoDeposito++;
                }
        
                $producto->update([
                    'stock' => $nuevaCantidad,
                    'costo' => $productoData['precioUnitario'],
                    'ultimoIngresoDeMercaderia' => now()->toDateString(),
                ]);
        
                Compradetalle::where('id', $productoData['producto'])->update([
                    'enDeposito' => $productoData['isChecked']
                ]);
            }
        }
        
        $compra = Compra::find($datosCompra['compra']);
        $compra->enDeposito = ($cantidadProductosNoDeposito === 0) ? 1 : 0;
        $compra->save();
        
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
