<?php

namespace App\Services;

use App\Filters\Cotizaciones\CotizacionesFilters;
use App\Helpers\CalcHelper;
use App\Helpers\DateHelper;
use App\Mail\EnvioCotizacionMailConAdjunto;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Cotizaciondetalle;
use App\Models\Invoice;
use App\Models\Invoicedetalle;
use App\Models\Pedido;
use App\Models\Producto;
use App\Transformers\Cotizacion\FindByIdTransformer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        /* genero invoice PDF **/
        $this->generarCotizacionMailPdf($cotizacion->id);

        $cliente = Cliente::where('id', $request->cliente)->first();

        /** Envio por email PDF**/
        $cuerpo = '';
        $emailMdo = env('MAIL_COTIZACION_MDO');
        if ($cliente->email) {

            $destinatarios = [
                $emailMdo,
                $cliente->email,
            ];
        } else {
            $destinatarios = [
                $emailMdo,
            ];
        }

        $rutaArchivoZip = storage_path('app/public/tmpdf/' . 'cotizacion_' . $cotizacion->id . '.pdf');

        $rutaArchivoFijo = storage_path('app/public/fijos/Inf.TRANSFERENCIA_BANCARIA.pdf');

        Mail::to($destinatarios)->send(new EnvioCotizacionMailConAdjunto($cuerpo, $rutaArchivoZip, $rutaArchivoFijo));

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
                'subtotal' => number_format($detalle->precio * $detalle->cantidad, 2),
            ];
        })->values();

        $cotizacion = [
            'cotizacion' => $cotizacion->id,
            'fecha' => DateHelper::ToDateCustom($cotizacion->fecha),
            'cliente' => $cotizacion->cliente,
            'nombreCliente' => optional($cotizacion->clientes)->nombre,
            'idCliente' => optional($cotizacion->clientes)->id,
            'telefonoCliente' => optional($cotizacion->clientes)->telefono,
            'direccionCliente' => optional($cotizacion->clientes)->direccion,
            'emailCliente' => optional($cotizacion->clientes)->email,
            'subTotal' => $cotizacion->subTotal,
            'total' => $cotizacion->total,
            'descuento' => $cotizacion->descuento,
            'cantidad' => $detalles->sum('cantidad'),
            'total' => number_format($detalles->sum('subtotal'), 2),
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
                'subtotal' => number_format($detalle->precio * $detalle->cantidad, 2),
            ];
        })->values();

        $cotizacion = [
            'cotizacion' => $cotizacion->id,
            'fecha' => DateHelper::ToDateCustom($cotizacion->fecha),
            'cliente' => $cotizacion->cliente,
            'nombreCliente' => optional($cotizacion->clientes)->nombre,
            'idCliente' => optional($cotizacion->clientes)->id,
            'telefonoCliente' => optional($cotizacion->clientes)->telefono,
            'direccionCliente' => optional($cotizacion->clientes)->direccion,
            'emailCliente' => optional($cotizacion->clientes)->email,
            'subTotal' => $cotizacion->subTotal,
            'total' => $cotizacion->total,
            'descuento' => $cotizacion->descuento,
            'cantidad' => $detalles->sum('cantidad'),
            'total' => number_format($detalles->sum('subtotal'), 2),
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

        // $invoice = Invoice::where('id',$request->id)->first();
        // $invoice = Invoice::join('pedido', 'invoice.orden', '=', 'pedido.id')
        // ->select('invoice.id', 'fechaOrden', 'billTo', 'shipTo', 'fechaOrden', 'orden', 'pedido.fecha as pedidoFecha')
        // ->where('invoice.id', $request->id)
        // ->first();

        $invoice = Invoice::join('cliente', 'invoice.cliente', '=', 'cliente.id')
        ->select('invoice.*', 'cliente.*', 'cliente.nombre as clienteNombre')
        ->where('invoice.id', $request->id)
        ->first();

        $pedido = Pedido::where('pedido.id', $invoice->orden)->first();

        //echo $invoice->shipTo;die;
        $invoiceDetalle = Invoicedetalle::where('invoice', $request->id)->get()->sortBy('Descripcion')->values()->toArray();
        $rutaArchivoExistente = storage_path('app/public/excel/demo2.xlsx');

        $reader = new Reader();
        $spreadsheet = $reader->load($rutaArchivoExistente);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:' . 'CZ' . 1000)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFFFFFF');

        /* Numero factura **/
        $sheet->setCellValue('AH4', $request->id);

        /* Fecha orden**/
        $sheet->setCellValue('C18', 'Fecha orden: ' . DateHelper::ToDateCustom($pedido->fecha));

        /* Fecha**/
        $sheet->setCellValue('AD6', DateHelper::ToDateCustom($invoice->fecha));

        /* Direccion de cobro BillTo **/
        $sheet->setCellValue('C10', $invoice->billTo);

        /* Direccion de envio ship to  */
        $sheet->setCellValue('N10', $invoice->shipTo);

        /* Numero de orden  */
        $sheet->setCellValue('C16', 'Orden #' . $invoice->orden);

        /* Envio **/
        $sheet->setCellValue('I16', 'Envio Via: ' . $invoice->shipVia);

        /* Termino **/
        $sheet->setCellValue('Q18', 'Terminos: ' . $invoice->Terms);

        /* Vendedor **/
        $sheet->setCellValue('I18', 'Vendedor : Vendedor: MDO S.');

        /* Transporte **/
        $sheet->setCellValue('J20', $invoice->UPS);

        /* Codigo seguimiento **/
        $sheet->setCellValue('s20', $invoice->codigoUPS);

        /* Cliente **/
        $sheet->setCellValue('C14', 'Cliente: ' . $invoice->cliente . '-' . $invoice->clienteNombre);

        $i = 24;
        $total = 0;
        $cantidad = 0;
        foreach ($invoiceDetalle as $fila => $datos) {

            $total += $datos['qordered'] * $datos['listPrice'];
            $cantidad += $datos['qordered'];

            $sheet->mergeCells('B' . $i . ':C' . $i . '');
            $sheet->setCellValue('B' . $i, $datos['qordered']);

            $sheet->mergeCells('D' . $i . ':F' . $i . '');
            $sheet->setCellValue('D' . $i, $datos['itemNumber']);

            $sheet->mergeCells('G' . $i . ':Z' . $i . '');
            $sheet->setCellValue('G' . $i, $datos['Descripcion']);

            $sheet->mergeCells('AB' . $i . ':AH' . $i . '');
            $sheet->setCellValue('AB' . $i, $datos['listPrice']);

            $sheet->mergeCells('AI' . $i . ':AM' . $i . '');
            $sheet->setCellValue('AI' . $i, $datos['qordered'] * $datos['listPrice']);

            $i++;
        }
        //dd($invoice);
        $sheet->mergeCells('AH' . $i . ':AN' . $i . '');
        $sheet->setCellValue('AH' . $i, '$' . $total);

        $sheet->mergeCells('B' . $i . ':F' . $i . '');
        $sheet->getStyle('B' . $i)->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('B' . $i, 'Total de artículos: ' . $cantidad);
        $sheet->getStyle('B' . $i . ':AI' . $i)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B' . $i . ':AI' . $i)->getBorders()->getTop()->getColor()->setARGB('000000');

        /* Descuento Neto **/
        $sheet->mergeCells('R' . ($i + 3) . ':AG' . ($i + 3) . '');
        $sheet->setCellValue('R' . ($i + 3), 'Descuento por promociones');

        $sheet->mergeCells('AH' . ($i + 3) . ':AL' . ($i + 3) . '');
        $sheet->getStyle('AH' . ($i + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('AH' . ($i + 3), 'U$S ' . $invoice->DescuentoPorPromociones);

        /* Descuento Porcentual **/
        $sheet->mergeCells('U' . ($i + 4) . ':X' . ($i + 4) . '');
        $sheet->setCellValue('U' . ($i + 4), 'Desc.');

        $sheet->mergeCells('Z' . ($i + 4) . ':AB' . ($i + 4) . '');
        $sheet->setCellValue('Z' . ($i + 4), $invoice->DescuentoPorcentual);

        $sheet->mergeCells('AE' . ($i + 4) . ':AG' . ($i + 4) . '');
        $sheet->setCellValue('AE' . ($i + 4), '%');

        $sheet->MergeCells('AH' . ($i + 4) . ':AL' . ($i + 4) . '');
        $sheet->getStyle('AH' . ($i + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('AH' . ($i + 4), 'U$S ' . ($invoice->subTotal * $invoice->DescuentoPorcentual / 100));

        /* Descuento Neto **/
        $sheet->mergeCells('T' . ($i + 5) . ':AG' . ($i + 5) . '');
        $sheet->setCellValue('T' . ($i + 5), 'Descuento neto:');

        $sheet->mergeCells('AH' . ($i + 5) . ':AL' . ($i + 5) . '');
        $sheet->getStyle('AH' . ($i + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('AH' . ($i + 5), 'U$S ' . $invoice->DescuentoNeto);

        /* Subtotal **/
        $sheet->mergeCells('X' . ($i + 6) . ':AG' . ($i + 6) . '');
        $sheet->getStyle('X' . ($i + 6))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('X' . ($i + 6), 'SubTotal:');

        $sheet->mergeCells('AH' . ($i + 6) . ':AL' . ($i + 6) . '');
        $sheet->getStyle('AH' . ($i + 6))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('AH' . ($i + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('AH' . ($i + 6), 'U$S ' . ($invoice->subTotal - $invoice->DescuentoNeto - ($invoice->subTotal * $invoice->DescuentoPorcentual / 100) - $invoice->DescuentoPorPromociones));

        /* Envio y manejo **/
        $sheet->mergeCells('T' . ($i + 7) . ':AG' . ($i + 7) . '');
        $sheet->setCellValue('T' . ($i + 7), 'Envio y Manejo:');

        $sheet->mergeCells('AH' . ($i + 7) . ':AL' . ($i + 7) . '');
        $sheet->getStyle('AH' . ($i + 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('AH' . ($i + 7), 'U$S ' . $invoice->TotalEnvio);

        /* Total **/
        $sheet->mergeCells('AB' . ($i + 8) . ':AG' . ($i + 8) . '');
        $sheet->getStyle('AB' . ($i + 8))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->setCellValue('AB' . ($i + 8), 'Total:');

        $sheet->mergeCells('AH' . ($i + 8) . ':AL' . ($i + 8) . '');
        $sheet->getStyle('AH' . ($i + 8))->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('AH' . ($i + 8))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('AH' . ($i + 8), 'U$S ' . ($invoice->subTotal - $invoice->DescuentoNeto - ($invoice->subTotal * $invoice->DescuentoPorcentual / 100) - $invoice->DescuentoPorPromociones + $invoice->TotalEnvio));

        $rangoCeldas = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());

        $rangoCeldas->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $writer = new Xlsx($spreadsheet);
        $rutaArchivoModificado = storage_path('app/public/excel/archivo_modificado.xlsx');
        $writer->save($rutaArchivoModificado);

        return response()->download($rutaArchivoModificado, 'archivo_modificado.xlsx')->deleteFileAfterSend(true);
    }

    public function notificacion()
    {

        echo 1;
        die;
    }
}
