<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ArrayToXlsxHelper;
use Illuminate\Support\Facades\DB;

class ReportesService
{
    public function stock(Request $request)
    {
        try {
            $sql = "SELECT
        id AS idProducto,
        codigo,
        nombre AS nombreProducto,
        color,
        stock,
        costo,
        precio,
        stock * costo AS CostoStock,
        stock * precio AS PrecioStock
    FROM
        producto
    WHERE
        stock > 0";

            $stock = DB::select($sql);

            // Convertir los objetos stdClass en arrays asociativos
            $stock = array_map(function ($item) {
                return (array) $item;
            }, $stock);

            $cabeceras = ['idProducto', 'codigo', 'nombreProducto', 'color', 'stock', 'costo', 'precio', 'CostoStock', 'PrecioStock'];

            $response = ArrayToXlsxHelper::getXlsx($stock, $cabeceras);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function productos(Request $request)
    {

        try {

            $fecha_inicio = '2024-01-01';
            $fecha_fin = '2024-02-01';

            $productos = DB::select("
            SELECT SUM(pedidodetalle.cantidad) AS cantidad,
            producto.color,
            producto.nombre,
            producto.stock,
            producto.id AS idProducto,
            producto.precio,
            producto.costo,
            ((producto.precio - producto.costo) * SUM(pedidodetalle.cantidad)) as ganancia,
            (producto.stock * producto.costo) as CostoStock,
            SUM(pedidodetalle.cantidad) * producto.precio as total,
            ( producto.costo * SUM(pedidodetalle.cantidad)) as costoVenta
   FROM
    pedidodetalle
       LEFT JOIN producto  on pedidodetalle.producto = producto.id
       LEFT JOIN color ON producto.color = color.id
       LEFT JOIN pedido ON pedidodetalle.pedido = pedido.id
   WHERE pedido.fecha BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}' AND pedido.estado <> 4
       GROUP BY color.nombre, producto.nombre, producto.stock, pedidodetalle.precio, producto.id, producto.precio, producto.costo
UNION
   SELECT pedidodetallenn.cantidad AS cantidad,
           '' AS nombreColor,
           pedidodetallenn.descripcion, '' as stock,
           '' AS idProducto,
           pedidodetallenn.precio,
           0 as costo, 0 as ganancia,
           0 as CostoStock,
           pedidodetallenn.cantidad * pedidodetallenn.precio as total,
           0 as costoVenta
    FROM
       pedidodetallenn
       LEFT JOIN pedido ON pedidodetallenn.pedido = pedido.id
    WHERE pedido.fecha BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}' AND pedido.estado <> 4
    order by 3
", [$fecha_inicio, $fecha_fin]);



            // Convertir los objetos stdClass en arrays asociativos
            $productos = array_map(function ($item) {
                return (array) $item;
            }, $productos);

            $cabeceras = ['id', 'cantidad', 'color', 'nombre', 'stock', 'id producto', 'precio', 'costo', 'ganancia', 'costo stock', 'total', 'costo venta'];

            $response = ArrayToXlsxHelper::getXlsx($productos, $cabeceras);

            return $response;


            //     return response()->json(['data' => $productos], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function invoices(Request $request)
    {
        try {
            $sql = "SELECT
            invoice.id AS id,
            invoice.fecha,
            invoice.cliente,
            invoice.total,
            invoice.formaDePago,
            invoice.estado,
            invoice.observaciones,
            invoice.anulada,
            invoice.billTo,
            invoice.shipTo,
            invoice.shipVia,
            invoice.FOB,
            invoice.Terms,
            invoice.fechaOrden,
            invoice.salesPerson,
            invoice.orden,
            invoice.peso,
            invoice.cantidad,
            cliente.nombre AS nombreCliente,
            invoice.subTotal,
            invoice.TotalEnvio
        FROM
            invoice
        LEFT JOIN
            cliente ON invoice.cliente = cliente.id";

            $invoices = DB::select($sql);

            // Convertir los objetos stdClass en arrays asociativos
            $invoices = array_map(function ($item) {
                return (array) $item;
            }, $invoices);

            $cabeceras = [
                'id', 'fecha', 'cliente', 'total', 'forma de pago', 'estado', 'observaciones', 'anulada', 'billto', 'shipto', 'shipvia', 'fob', 'terms', 'fecha orden', 'sales person', 'orden',
                'peso', 'cantidad', 'nombre cliente', 'subtotal', ' total envio'
            ];

            $response = ArrayToXlsxHelper::getXlsx($invoices, $cabeceras);

            return $response;

            //     return response()->json(['data' => $invoices], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los invoices'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
