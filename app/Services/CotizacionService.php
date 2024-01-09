<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Producto;
use App\Models\Cotizacion;
use App\Helpers\CalcHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizaciondetalle;
use App\Filters\Cotizaciones\CotizacionesFilters;
use App\Helpers\DateHelper;
use App\Transformers\Cotizacion\FindByIdTransformer;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnvioCotizacionMail;

class CotizacionService
{
    public function findAll(Request $request)
    {
        try {
            $data = CotizacionesFilters::getPaginateCotizaciones($request, Cotizacion::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener las cotizaciones', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        try {
            $data = collect([Cotizacion::find($request->id)]);

            if ($data[0]) {
                $transformer = new FindByIdTransformer();

                $cotizacionTransformada = $data->map(function ($cotizacion) use ($transformer) {
                    return $transformer->transform($cotizacion);
                });

                $response = [
                    'status' => Response::HTTP_OK,
                    'message' => $cotizacionTransformada,
                ];

                return response()->json(['data' => $response], Response::HTTP_OK);
            } else {
                return response()->json(['data' => null], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener la cotización'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {
        $cotizacion = new Cotizacion();

        $cotizacion->fecha = date('Y-m-d');
        $cotizacion->cliente = $request->cliente;
        $cotizacion->estado = 1;
        $cotizacion->save();
        $cotizacionId = $cotizacion->id;

        if (!$cotizacion) {
            return response()->json(['error' => 'Failed to create Cotizacion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $totalCotizacion = 0;

        foreach ($request->productos as $p) {

            $producto = Producto::find($p['idProducto']);
            $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);
            $total = $precio * $p['cantidad'];
            $cotizacionDetalle = new Cotizaciondetalle();
            $cotizacionDetalle->cotizacion = $cotizacionId;
            $cotizacionDetalle->producto = $p['idProducto'];
            $cotizacionDetalle->precio = $precio;
            $cotizacionDetalle->cantidad = $p['cantidad'];
            $cotizacionDetalle->save();

            $totalCotizacion += $total;
        }

        $cotizacion = Cotizacion::find($cotizacion->id);
        $cotizacion->total = $totalCotizacion;
        $cotizacion->save();

        /** genero invoice PDF **/

        /** Envio por email PDF**/

//         $cuerpo = ''; 
// $emailMdo = env('MAIL_COTIZACION_MDO');
// $destinatarios = [
//     $emailMdo,
//     'mgarralda@cobax.com.ar'
// ];

// $rutaArchivoZip = asset('storage/pdfCotizaciones/cotizacion2522.pdf');

// Mail::to($destinatarios)->send(new EnvioCotizacionMail($cuerpo, $rutaArchivoZip));

        return response()->json($cotizacion, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $cotizacion = Cotizacion::find($request->id);

        if (!$cotizacion) {
            return response()->json(['error' => 'Cotizacion not found'], Response::HTTP_NOT_FOUND);
        }

        $cotizacion->cliente = $request->cliente;

        $cotizacion->save();

        $total = 0;
        $existingProductIds = [];

        foreach ($request->productos as $p) {
            $producto = Producto::find($p['idProducto']);
            $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);
            $total += $precio * $p['cantidad'];

            $cotizacionDetalle = Cotizaciondetalle::where('cotizacion', $request->id)
                ->where('producto', $p['idProducto'])
                ->first();

            if (!$cotizacionDetalle) {
                $cotizacionDetalle = new Cotizaciondetalle();
                $cotizacionDetalle->cotizacion = $request->id;
                $cotizacionDetalle->producto = $p['idProducto'];
            }

            $cotizacionDetalle->precio = $precio;
            $cotizacionDetalle->cantidad = $p['cantidad'];
            $cotizacionDetalle->save();

            $existingProductIds[] = $p['idProducto'];
        }

        Cotizaciondetalle::where('cotizacion', $request->id)
            ->whereNotIn('producto', $existingProductIds)
            ->delete();

        $cotizacion->total = $total;
        $cotizacion->save();

        return response()->json($cotizacion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cotizacion = Cotizacion::find($request->id);

        if (!$cotizacion) {
            return response()->json(['error' => 'Cotizacion not found'], Response::HTTP_NOT_FOUND);
        }

        $cotizacion->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function findByIdPdf(Request $request)
    {
        $cotizacion = Cotizacion::where('id',$request->id)->first();

        $detalles = Cotizaciondetalle::where('cotizacion', $request->id)->get()->unique('id')->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'cotizacion' => $detalle->cotizacion,
                'productoId' => optional($detalle->productos)->id,
                'productoNombre' => optional($detalle->productos)->nombre,
                'productoCodigo' => optional($detalle->productos)->codigo,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'subtotal' => number_format($detalle->precio*$detalle->cantidad,2)
            ];
        })->values(); 

        $cotizacion = [
            "cotizacion" => $cotizacion->id,
            "fecha" => DateHelper::ToDateCustom($cotizacion->fecha),
            "cliente" => $cotizacion->cliente,
            "nombreCliente" => optional($cotizacion->clientes)->nombre,
            "idCliente" => optional($cotizacion->clientes)->id,
            "telefonoCliente" => optional($cotizacion->clientes)->telefono,
            "direccionCliente" => optional($cotizacion->clientes)->direccion,
            "emailCliente" => optional($cotizacion->clientes)->email,
            "subTotal" => $cotizacion->subTotal,
            "total" => $cotizacion->total,
            "descuento" => $cotizacion->descuento,
            "cantidad" => $detalles->sum('cantidad'),
            "total" => number_format($detalles->sum('subtotal'),2)
        ];

        $cotizacion['detalles'] = $detalles->all();

        $pdf = Pdf::loadView('pdf.cotizacion', ['cotizacion' => $cotizacion]);

        $pdf->getDomPDF();

        return $pdf->stream();
    }
}
