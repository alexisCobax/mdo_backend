<?php

namespace App\Services;

use App\Filters\Pedidos\PedidosFilters;
use App\Filters\Pedidos\PedidosProductosFilters;
use App\Models\Pedido;
use App\Models\Pedidodescuentospromocion;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use App\Transformers\Pedidos\CreateTransformer;
use App\Transformers\Pedidos\FindByIdTransformer;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PedidoService
{
    public function findAll(Request $request)
    {
        try {
            if ($request->codigoProducto) {
                $data = PedidosProductosFilters::getPaginatePedidos($request, Pedido::class);
            } else {
                $data = PedidosFilters::getPaginatePedidos($request, Pedido::class);
            }

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        try {
            $transformer = new FindByIdTransformer($request);
            $transformer = $transformer->transform();

            return response()->json(['data' => $transformer], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener el pedido', $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {

        $transformer = new CreateTransformer();
        $pedidoTransformer = $transformer->transform($request);

        $pedido = Pedido::create($pedidoTransformer);

        if ($request->detalle) {
            $detalle = collect($request->detalle)->map(function ($dt) use ($pedido) {
                return [
                    'pedido' => $pedido->id,
                    'producto' => $dt['producto'],
                    'precio' => $dt['precio'],
                    'cantidad' => $dt['cantidad'],
                    'costo' => '00.00',
                    'envio' => '00.00',
                    /***
                     * tax y taxEnvio dejar siempre en 0.00 para futuras actualizaciones
                     ***/
                    'tax' => '00.00',
                    'taxEnvio' => '00.00',
                ];
            });
            try {
                Pedidodetalle::insert($detalle->toArray());
            } catch (Error $e) {
                return response()->json($e->getMessage());
            }
        }

        if ($request->detalleNN) {
            $detalleNN = collect($request->detalleNN)->map(function ($dtNN) use ($pedido) {
                return [
                    'descripcion' => $dtNN['descripcion'],
                    'precio' => $dtNN['precio'],
                    'pedido' => $pedido->id,
                    'cantidad' => $dtNN['cantidad'],
                ];
            });
            try {
                Pedidodetallenn::insert($detalleNN->toArray());
            } catch (Error $e) {
                return response()->json($e->getMessage());
            }
        }

        if ($request->descuentosPromocion) {

            $descuentosPromo = collect($request->descuentosPromocion)->map(function ($descPro) use ($pedido) {
                return [
                    'idPedido' => $pedido->id,
                    'idPromocion' => $descPro['idPromocion'],
                    'descripcion' => $descPro['descripcion'],
                    'montoDescuento' => $descPro['montoDescuento'],
                    'idTipoPromocion' => $descPro['idTipoPromocion'],
                ];
            });

            try {
                Pedidodescuentospromocion::insert($descuentosPromo->toArray());
            } catch (Error $e) {
                return response()->json($e->getMessage());
            }
        }

        // $totalDetalle = Pedidodetalle::where('pedido', $pedido->id)->sum('precio');
        // $totalDetalleNN = Pedidodetallenn::where('pedido', $pedido->id)->sum('precio');
        // $total = $totalDetalle + $totalDetalleNN;

        // $upPedido = Pedido::find($pedido->id);
        // $upPedido->total = $total;
        // $upPedido->save();

        if (!$pedido) {
            return response()->json(['error' => 'Failed to create Pedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function createNuevo(Request $request)
    {

        $pedidoTransformer = [
            'fecha' => NOW(),
            'estado' => 1
        ];

        $pedido = Pedido::create($pedidoTransformer);

        return response()->json(['id' => $pedido->id], Response::HTTP_OK);

        if (!$pedido) {
            return response()->json(['error' => 'Failed to create Pedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedido = Pedido::find($request->id);

        if (!$pedido) {
            return response()->json(['error' => 'Pedido not found'], Response::HTTP_NOT_FOUND);
        }

        // $transformer = new CreateTransformer;
        // $pedidoTransformer = $transformer->transform($request);

        $pedido->cliente = $request->cliente;
        $pedido->origen = $request->origen;
        $pedido->vendedor = $request->vendedor;
        $pedido->etapa = $request->etapa;
        $pedido->observaciones = $request->observaciones;
        $pedido->descuentoNeto = $request->descuentoNeto;
        $pedido->descuentoPorcentual = $request->descuentoPorPorcentaje;
        $pedido->descuentoPromociones = $request->descuentoPorPromociones;
        $pedido->totalEnvio = $request->totalEnvio;
        $pedido->total = $request->total;
        $pedido->transportadoraNombre = $request->transportadoraNombre;
        $pedido->transportadoraTelefono = $request->transportadoraTelefono;
        $pedido->codigoSeguimiento = $request->codigoSeguimiento;
        $pedido->idTransportadora = $request->idTransportadora;
        $pedido->tipoDeEnvio = $request->tipoDeEnvio;
        $pedido->nombreEnvio = $request->envioNombre;
        $pedido->paisEnvio = $request->envioPais;
        $pedido->regionEnvio = $request->envioRegion;
        $pedido->ciudadEnvio = $request->envioCiudad;
        $pedido->domicilioEnvio = $request->envioDomicilio;
        $pedido->cpEnvio = $request->envioCp;
        //$pedido->fecha = NOW();
        //$pedido->estado = 1;
        $pedido->formaDePago = 1;

        $pedido->save();

        // if ($request->detalle) {
        //     $detalle = collect($request->detalle)->map(function ($dt) use ($pedido) {
        //         return [
        //             'pedido' => $pedido->id,
        //             'producto' => $dt['producto'],
        //             'precio' => $dt['precio'],
        //             'cantidad' => $dt['cantidad'],
        //             'costo' => '0',
        //             'envio' => '0',
        //             'tax' => '0',
        //             'taxEnvio' => '0',
        //         ];
        //     });

        //     Pedidodetalle::where('pedido', $pedido->id)->delete();
        //     Pedidodetalle::insert($detalle->toArray());
        // }



            $detalleNN = collect($request->detalleNN)->map(function ($dtNN) use ($pedido) {
                return [
                    'descripcion' => $dtNN['descripcion'],
                    'precio' => $dtNN['precio'],
                    'pedido' => $pedido->id,
                    'cantidad' => $dtNN['cantidad'],
                ];
            });

            Pedidodetallenn::where('pedido', $pedido->id)->delete();

        if ($request->detalleNN) {
            Pedidodetallenn::insert($detalleNN->toArray());
        }

        if ($request->descuentosPorPromocion) {

            $descuentosPromo = collect($request->descuentosPorPromocion)->map(function ($descPro) use ($pedido) {
                return [
                    'idPedido' => $pedido->id,
                    'idPromocion' => $descPro['idPromocion'],
                    'descripcion' => $descPro['descripcion'],
                    'montoDescuento' => $descPro['montoDescuento'],
                    'idTipoPromocion' => $descPro['idTipoPromocion'],
                ];
            });

            try {
                Pedidodescuentospromocion::insert($descuentosPromo->toArray());
            } catch (Error $e) {
                return response()->json($e->getMessage());
            }
        }

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {

        $SQL = '
        UPDATE producto
        LEFT JOIN pedidodetalle ON pedidodetalle.producto = producto.id
        SET producto.stock = producto.stock + pedidodetalle.cantidad
        WHERE pedidodetalle.pedido = ?';

        try {
            DB::statement($SQL, [$request->id]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        try {
            Pedidodetalle::where('pedido', $request->id)->delete();
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        try {
            PedidoDetallenn::where('pedido', $request->id)->delete();
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        $pedido = Pedido::find($request->id);
        $pedido->estado = 4;

        if (!$pedido) {
            return response()->json(['error' => 'Pedido not found'], Response::HTTP_NOT_FOUND);
        }

        $pedido->save();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
