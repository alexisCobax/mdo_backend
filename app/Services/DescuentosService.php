<?php

namespace App\Services;

use App\Helpers\CalcHelper;
use App\Models\Marcaproducto;
use App\Models\Producto;
use App\Models\Promocioncomprandoxgratisz;
use Illuminate\Http\Request;

class DescuentosService
{
    public function findAll(Request $request)
    {
        $json = $request->json()->all();

        if (!isset($json['detalle']) || !is_array($json['detalle'])) {
            return response()->json(['error' => 'JSON malformado'], 400);
        }

        $productosAgrupados = collect($json['detalle'])->groupBy(function ($detalleProducto) {
            return Producto::findOrFail($detalleProducto['producto'])->marca;
        })->map(function ($detalleProductos, $idMarca) {
            $cantidad = $detalleProductos->sum('cantidad');
            $precioMenor = $this->calcularPrecioMenor($detalleProductos);
            $cantidadBonificada = $this->calcularCantidadBonificada($idMarca, $cantidad);
            $marca = Marcaproducto::find($idMarca);

            return [
                'id' => $idMarca,
                'nombre' => $marca->nombre,
                'cantidad' => $cantidadBonificada,
                'precio' => $precioMenor * $cantidadBonificada,
            ];
        })->values();

        return response()->json($productosAgrupados);
    }

    private function calcularPrecioMenor($detalleProductos)
    {
        $precioMenor = PHP_FLOAT_MAX;

        foreach ($detalleProductos as $detalleProducto) {
            $producto = Producto::findOrFail($detalleProducto['producto']);
            $precio = $producto->precio;

            $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);

            if ($precio < $precioMenor) {
                $precioMenor = $precio;
            }
        }

        return $precioMenor;
    }

    private function calcularCantidadBonificada($idMarca, $cantidad)
    {
        $promocion = Promocioncomprandoxgratisz::where('idMarca', $idMarca)->where('activa', 1)->first();
        if (!$promocion) {
            return 0;
        }
        $cantidadBonificada = $promocion->CantidadBonificada;

        $cantidadTotalBonificada = floor($cantidad / $promocion->Cantidad) * $cantidadBonificada;

        return $cantidadTotalBonificada;
    }
}
