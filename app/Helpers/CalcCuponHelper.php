<?php

namespace App\Helpers;

use App\Models\Carritodetalle;
use App\Models\Cupondescuento;
use Illuminate\Support\Facades\DB;

class CalcCuponHelper
{
    public static function calcularDescuento($cuponId, $total, $totalDescuentoPromociones)
    {
        $cupon = Cupondescuento::find($cuponId);

        $carrito = CarritoHelper::getCarrito();

        $descuentoPorCupon = 0;

        // if ($cupon && $total >= $cupon->montoMinimo) {
        //     $descuentoPorCupon = $cupon->descuentoFijo + ($total * ($cupon->descuentoPorcentual / 100));
        // }

        if ($cupon && ($cupon->combinable || $totalDescuentoPromociones == 0)) {
            $decTotalPrecioEnPromocion = 0;
            $intCantidadEnPromocion = 0;

             //reviso si el cupon tiene una marca para realizar el descuento

                $SQL = "SELECT carritodetalle.*
                FROM carritodetalle LEFT JOIN producto ON producto.id = carritodetalle.producto
                LEFT JOIN marcaproducto ON marcaproducto.id = producto.marca
                WHERE carritodetalle.carrito = :carritoId";

                if ($cupon->marca !== 0 && $cupon->marca !== null) {
                    $marcaId = $cupon->marca;
                    $SQL .= " AND marcaproducto.id = ".$marcaId;
                }
                if ($cupon->grupo !== 0 && $cupon->grupo !== null) {
                    $grupo = $cupon->grupo;
                    $SQL .= " AND producto.grupo = ".$grupo;
                }

                LogHelper::get($SQL);
                $carritoDetalles = DB::select($SQL, [
                    'carritoId' => $carrito['id']
                ]);

                foreach ($carritoDetalles as $carritoDetalle) {
                    $decTotalPrecioEnPromocion += ($carritoDetalle->cantidad * $carritoDetalle->precio);
                    $intCantidadEnPromocion += $carritoDetalle->cantidad;
                }

                if ($decTotalPrecioEnPromocion >= $cupon->montoMinimo && $cupon->cantidadMinima <= $intCantidadEnPromocion) {
                    $descuentoPorCupon = $cupon->descuentoFijo + ($decTotalPrecioEnPromocion * ($cupon->descuentoPorcentual / 100));
                }

        }

        return $descuentoPorCupon;
    }
}
