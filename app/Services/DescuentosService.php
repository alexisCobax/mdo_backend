<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Producto;
use App\Helpers\CalcHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Cupondescuento;
use App\Helpers\CalcCuponHelper;
use Illuminate\Support\Facades\DB;
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
                'idTipoPromocion' => 1,
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
            'cantidadBonificada' => $cantidadTotalBonificada,
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

        try {
            $strCondicion = "";
            $strCambios = "";

            $query = "UPDATE producto SET " . $strCambios . " WHERE " . substr($strCondicion, 4);

            $bindings = [];

            if ($request->marcaCheckbox != 0) {
                $strCondicion .= " AND marca = ?";
                $bindings[] = $request->marca;
            }
            if ($request->tipoCheckbox != 0) {
                $strCondicion .= " AND tipo = ?";
                $bindings[] = $request->tipo;
            }
            if ($request->categoriaCheckbox != 0) {
                $strCondicion .= " AND categoria = ?";
                $bindings[] = $request->categoria;
            }
            if ($request->colorCheckbox != 0) {
                $strCondicion .= " AND color = ?";
                $bindings[] = $request->color;
            }
            if ($request->grupoCheckbox != 0) {
                $strCondicion .= " AND grupo = ?";
                $bindings[] = $request->grupo;
            }
            if ($request->destacadoCheckbox) {
                $strCondicion .= " AND destacado = ?";
                $bindings[] = $request->destacado;
            }
            if ($request->estucheCheckbox) {
                $strCondicion .= " AND estuche = ?";
                $bindings[] = $request->estuche;
            }
            if ($request->stockCheckbox) {
                $strCondicion .= " AND stock BETWEEN ? AND ?";
                $bindings[] = $request->stockDesde;
                $bindings[] = $request->stockHasta;
            }
            if ($request->precioCheckbox) {
                $strCondicion .= " AND precio BETWEEN ? AND ?";
                $bindings[] = $request->precioDesde;
                $bindings[] = $request->precioHasta;
            }

            if ($request->suspendidoCheckbox) {
                $strCondicion .= " AND suspendido = ?";
                $bindings[] = $request->suspendido;
            }

            switch ($request->modificacion) {
                case 'montoFijo':
                    //
                    break;
                case 'descuentoAumentoFijo':
                    $strCambios = " precio = (precio + ?)";
                    $bindings[] = $request->montoFijo;

                    break;
                case 'descuentoAumentoPorcentual';
                    $strCambios = " precio = (precio + (? * precio / 100))";
                    $bindings[] = $request->descuentoAumentoPorcentual;
                    break;
                case 'costo':
                    $strCambios = " costo = ?";
                    $bindings[] = $request->costo;
                    break;
                case 'estuche':
                    $strCambios = " estuche = ?";
                    $bindings[] = $request->estucheModificacion;
                    break;
                case 'suspendidoModificacion':
                    $strCambios = " suspendido = ?";
                    $bindings[] = $request->optionsSuspendidoModificado;
                    break;
                case 'stockModificacion':
                    //
                    break;
            }

            $result = DB::update($query, $bindings);

            return response()->json(['data' => $result], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return true;
    }
}
