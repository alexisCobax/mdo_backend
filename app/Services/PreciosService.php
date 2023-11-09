<?php

namespace App\Services;

use App\Models\Portada;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;

class PreciosService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Portada::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los Precios'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Portada::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $productos = Producto::query();

        if ($request->marca) {
            $productos->where('marca', $request->marca);
        }
        if ($request->tipo) {
            $productos->where('tipo', $request->tipo);
        }
        if ($request->categoria) {
            $productos->where('categoria', $request->categoria);
        }
        if ($request->color) {
            $productos->where('color', $request->color);
        }
        if ($request->grupo) {
            $productos->where('grupo', $request->grupo);
        }
        if ($request->destacado) {
            $productos->where('destacado', $request->destacado);
        }
        if ($request->stock_desde) {
            if ($request->stock_desde) {
                $productos->whereBetween('stock', [$request->stock_desde, $request->stock_hasta]);
            }
            return "Debe ingresar stock hasta";
        }
        if ($request->stock_hasta) {
            $productos->where('stock', $request->stock_hasta);
        }
        if ($request->precio) {
            $productos->where('precio', $request->precio);
        }
        if ($request->suspendido) {
            $productos->where('suspendido', $request->suspendido);
        }

            $productos->get()->each(function ($producto) use ($request){

                if($request->monto_fijo){
                    $producto->precioPromocional = $producto->precio - $request->monto_fijo;
                    $producto->save();
                }

                if($request->descuento){
                    $producto->precioPromocional = $producto->precio - $request->descuento;
                    $producto->save();
                }

                if($request->porcentaje){
                    $porcentaje = $producto->precio * ($request->porcentaje/100);
                    $precio = $producto->precio - $porcentaje;
                    $producto->precioPromocional = $precio;
                    $producto->save();
                }

                if($request->suspendido){
                    $producto->suspendido = $request->suspendido;
                    $producto->save();
                }
            
            });

        $resultado = $productos->get();

        if (!$resultado) {
            return response()->json(['error' => 'Failed to create Precios'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($resultado, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $portada = Portada::find($request->id);

        if (!$portada) {
            return response()->json(['error' => 'Precios not found'], Response::HTTP_NOT_FOUND);
        }

        $portada->update($request->all());
        $portada->refresh();

        return response()->json($portada, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $portada = Portada::find($request->id);

        if (!$portada) {
            return response()->json(['error' => 'Precios not found'], Response::HTTP_NOT_FOUND);
        }

        $portada->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
