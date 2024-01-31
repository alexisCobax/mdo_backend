<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Invoice;
use App\Models\Producto;
use App\Models\Cotizacion;
use App\Helpers\CalcHelper;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizaciondetalle;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Mail\EnvioCotizacionMailConAdjunto;
use App\Filters\Cotizaciones\CotizacionesFilters;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use App\Transformers\Cotizacion\FindByIdTransformer;
use Illuminate\Support\Facades\DB;

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

        if (!$cotizacion) {
            return response()->json(['error' => 'Failed to create Cotizacion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $totalCotizacion = 0;

        foreach ($request->productos as $p) {

            $producto = Producto::find($p['idProducto']);
            $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);
            $total = $precio * $p['cantidad'];
            $cotizacionDetalle = new Cotizaciondetalle();
            $cotizacionDetalle->cotizacion = $cotizacion->id;
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
        $this->generarCotizacionMailPdf($cotizacion->id);

        $cliente = Cliente::where('id', $request->cliente)->first();

        /** Envio por email PDF**/
        $cuerpo = '';
        $emailMdo = env('MAIL_COTIZACION_MDO');
        if ($cliente->email) {

            $destinatarios = [
                $emailMdo,
                $cliente->email
            ];
        } else {
            $destinatarios = [
                $emailMdo
            ];
        }

        $rutaArchivoZip = storage_path('app/public/tmpdf/' . 'cotizacion_' . $cotizacion->id . '.pdf');

        Mail::to($destinatarios)->send(new EnvioCotizacionMailConAdjunto($cuerpo, $rutaArchivoZip));

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
        $cotizacion = Cotizacion::where('id', $request->id)->first();

        $detalles = Cotizaciondetalle::where('cotizacion', $request->id)->get()->unique('id')->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'cotizacion' => $detalle->cotizacion,
                'productoId' => optional($detalle->productos)->id,
                'productoNombre' => optional($detalle->productos)->nombre,
                'productoCodigo' => optional($detalle->productos)->codigo,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'subtotal' => number_format($detalle->precio * $detalle->cantidad, 2)
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
            "total" => number_format($detalles->sum('subtotal'), 2)
        ];

        $cotizacion['detalles'] = $detalles->all();

        $pdf = Pdf::loadView('pdf.cotizacion', ['cotizacion' => $cotizacion]);

        $pdf->getDomPDF();

        return $pdf->stream();
    }

    public function generarCotizacionMailPdf($idCotizacion)
    {
        $cotizacion = Cotizacion::where('id', $idCotizacion)->first();

        $detalles = Cotizaciondetalle::where('cotizacion', $idCotizacion)->get()->unique('id')->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'cotizacion' => $detalle->cotizacion,
                'productoId' => optional($detalle->productos)->id,
                'productoNombre' => optional($detalle->productos)->nombre,
                'productoCodigo' => optional($detalle->productos)->codigo,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'subtotal' => number_format($detalle->precio * $detalle->cantidad, 2)
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
            "total" => number_format($detalles->sum('subtotal'), 2)
        ];

        $cotizacion['detalles'] = $detalles->all();

        $pdf = Pdf::loadView('pdf.cotizacion', ['cotizacion' => $cotizacion]);
        $pdfContent = $pdf->output();

        // Guardar el PDF en el directorio storage/app/public/tmpPdf
        $pdfPath = 'public/tmpdf/' . 'cotizacion_' . $idCotizacion . '.pdf';

        try {
            Storage::put($pdfPath, $pdfContent);
            return response()->json(['response' => 'Pdf Guardado!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function excel(Request $request)
    {

        $SQL = "SELECT 
        cotizaciondetalle.cantidad,
        producto.codigo,
        concat(producto.descripcion,' ',
        ' ',producto.color,
        ' ',producto.tamano,
        ' ',producto.material) AS descripcion,
        cotizaciondetalle.precio
        FROM 
        tienda.cotizaciondetalle 
        INNER JOIN 
        producto 
        ON 
        cotizaciondetalle.producto=producto.id
        INNER JOIN
        marcaproducto
        ON
        producto.marca=marcaproducto.id
        WHERE 
        cotizaciondetalle.cotizacion = ?";


$response = DB::select($SQL, [$request->id]);

        $CotizacionDetalle = json_decode(json_encode($response), true);

        $rutaArchivoExistente = storage_path('app/public/excel/demo2.xlsx');

        $reader = new Reader();
        $spreadsheet = $reader->load($rutaArchivoExistente);
        

        $sheet = $spreadsheet->getActiveSheet();

        //$sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
        $sheet->getStyle('A1:' . 'CZ' . 1000)
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FFFFFFFF');


        // $sheet->setCellValue('B13', 'Nuevo Valorssss');

            // Texto con saltos de línea
            $textoConSaltos = " MDO INC\n 2618 NW 112th AVENUE.\n MIAMI, FL 33172\n Phone: 305 513 9177 / 305 424 8199\n TAX ID # 46-0725157";

            // Establecer el texto en la celda C2 con saltos de línea
            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $richText->createText($textoConSaltos);

        $sheet->mergeCells('C2:J7'); // Fusionar celdas de C2 a J7
        $sheet->setCellValue('C2', $richText); // Establecer valor en la celda fusionada
    

        $i = 0;
        foreach ($CotizacionDetalle as $fila => $datos) {
            $i = 0;
            foreach ($datos as $valor) {
                $i++;
                $sheet->setCellValueByColumnAndRow($i + 2, $fila + 70, $valor);
            }
        }

        // $ultimaFila = count($CotizacionDetalle) + 28;
        // $sheet->setCellValue('A' . ($ultimaFila + 1), 'termine!');

        // $sheet->getColumnDimension('B')->setWidth(40);

        $rangoCeldas = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());

        $rangoCeldas->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $writer = new Xlsx($spreadsheet);
        $rutaArchivoModificado = storage_path('app/public/excel/archivo_modificado.xlsx');
        $writer->save($rutaArchivoModificado);

        return response()->download($rutaArchivoModificado, 'archivo_modificado.xlsx')->deleteFileAfterSend(true);
    }
}
