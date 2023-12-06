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
            $precio = $precioMenor * $cantidadBonificada['cantidadBonificada'];
            return [
                'id' => 0,
                'idPedido' => 0,
                'idPromocion' => $cantidadBonificada['idPromocion'],
                'descripcion' => $cantidadBonificada['nombrePromocion'],
                'montoDescuento' => $precio,
                'idTipoPromocion' => 1
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

        return [
            'idPromocion' => $promocion->id,
            'nombrePromocion' => $promocion->nombre,
            'cantidadBonificada' => $cantidadTotalBonificada
        ];

        //return $cantidadTotalBonificada;
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

            return response()->json(['mensaje' => 'El cupón no existe', ' status' => 404], Response::HTTP_NOT_FOUND);
        }

        try {

            $carrito = Carrito::where('id', $carrito['id'])->first();
            if ($carrito->cupon) {
                return response()->json(['cupon' => $cupon->id, 'status' => 404], Response::HTTP_OK);
            } else {
                $carrito->cupon = $cupon->id;
                $carrito->save();
            }
            return response()->json(['cupon' => $cupon->id, 'status' => 200], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los cupones'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {

        $modificacion = $request->input('modificacion');

        switch ($modificacion) {
            case 'monto_fijo':
                $datos = $request->only(['marca', 'tipo', 'categoria', 'color', 'grupo', 'destacado', 'estuche', 'stock_desde', 'stock_hasta', 'precio_desde', 'precio_hasta', 'suspendido']);
                break;

            case 'aumento_fijo':
                $datos = $request->only(['aumento_fijo', 'estuche', 'suspendido']);
                break;

            case 'aumento_porcentual':
                $datos = $request->only(['aumento_porcentual', 'estuche', 'suspendido']);
                break;

            case 'costo':
                $datos = $request->only(['costo', 'estuche', 'suspendido']);
                break;

            case 'estuche':
                $datos = $request->only(['estuche', 'suspendido']);
                break;

            case 'suspendido':
                $datos = $request->only(['suspendido']);
                break;

            default:
                return response()->json(['mensaje' => 'Tipo de modificación no válido'], 400);
        }

        //$producto->update($datos);

        return response()->json(['data' => $datos, 'mensaje' => 'Producto actualizado con éxito']);
    }
}
