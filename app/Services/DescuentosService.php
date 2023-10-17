<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Producto;
use App\Helpers\CalcHelper;
use Illuminate\Http\Request;
use App\Models\Marcaproducto;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Cupondescuento;
use App\Helpers\CalcCuponHelper;
use App\Models\Promocioncomprandoxgratisz;

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

    public function calcularPrecioMenor($detalleProductos)
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

    public function calcularCantidadBonificada($idMarca, $cantidad)
    {
        $promocion = Promocioncomprandoxgratisz::where('idMarca', $idMarca)->where('activa', 1)->first();
        if (!$promocion) {
            return 0;
        }
        $cantidadBonificada = $promocion->CantidadBonificada;

        $cantidadTotalBonificada = floor($cantidad / $promocion->Cantidad) * $cantidadBonificada;

        return $cantidadTotalBonificada;
    }

    public function discount($cupon, $total, $descuento)
    {
        return CalcCuponHelper::calcularDescuento($cupon, $total, $descuento);
    }

    public function add($request)
    {

        $carrito = CarritoHelper::getCarrito();

        $cupon = Cupondescuento::where('nombre', $request->cupon)->first();

        if (!$cupon) {

            return response()->json(['mensaje' => 'El cupón no existe'], Response::HTTP_NOT_FOUND);
        }

        try {

            $carrito = Carrito::where('id', $carrito['id'])->first();
            $carrito->cupon = $cupon->id;
            $carrito->save();

            return response()->json(['cupon' => $cupon->id], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los cupones'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
