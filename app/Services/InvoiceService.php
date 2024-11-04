<?php

namespace App\Services;

use Error;
use App\Models\Pais;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Pedidodetalle;
use Illuminate\Http\Response;
use App\Mail\EnvioMailInvoice;
use App\Models\Invoicedetalle;
use App\Models\Pedidodetallenn;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Filters\Invoices\InvoicesFilters;
use App\Transformers\Invoices\CreateTransformer;
use App\Transformers\Invoices\FindByIdTransformer;
use App\Transformers\Invoices\InvoiceEmailTransformer;
use App\Transformers\Invoices\CreateDetalleTransformer;

class InvoiceService
{
    public function findAll(Request $request)
    {
        try {
            $data = InvoicesFilters::getPaginateInvoices($request, Invoice::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los invoices'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {

        $invoice = Invoice::find($request->id);

        $tranformer = new FindByIdTransformer();
        $invoiceTransformer = $tranformer->transform($invoice, $request);

        if (!$invoice) {
            return response()->json(['error' => 'Failed to create Invoice'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($invoiceTransformer, Response::HTTP_OK);
    }

    public function findByIdPdf(Request $request)
    {
        try {
            $invoice = Invoice::find($request->id);

            $tranformer = new FindByIdTransformer();
            $invoiceTransformer = $tranformer->transform($invoice, $request);

            $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoiceTransformer]);

            $pdf->getDomPDF();

            return $pdf->stream();
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$invoice) {
            return response()->json(['error' => 'Failed to create Invoice'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($invoiceTransformer, Response::HTTP_OK);
    }

    public function invoiceParaEmail($invoiceId)
    {

            $invoice = Invoice::find($invoiceId);

            $tranformer = new InvoiceEmailTransformer();
            $invoiceTransformer = $tranformer->transform($invoice);

            $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoiceTransformer]);

            $pdfContent = $pdf->output();

            $pdfPath = 'public/tmpdf/'.'cotizacion_'.$invoiceId.'.pdf';

            try {
                Storage::put($pdfPath, $pdfContent);

                return response()->json(['response' => 'Pdf Guardado!'], Response::HTTP_OK);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
            }

        return response()->json($invoiceTransformer, Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        try {

            $cantidad = 0;

            //Busco el pedido
            $pedido = Pedido::where('id', $request->pedido)->first();

            $SQL = "
                SELECT SUM(total) as total
                FROM (
                    SELECT IFNULL(SUM(precio * cantidad), 0) AS total
                    FROM pedidodetalle
                    WHERE pedido = ?
                    UNION ALL
                    SELECT IFNULL(SUM(precio * cantidad), 0) AS total
                    FROM pedidodetallenn
                    WHERE pedido = ?
                ) AS combined_totals
            ";

            $result = DB::select($SQL, [$pedido->id, $pedido->id]);

            $subTotal = $result[0]->total;

            //Saco la cantidad detalle
            $cantidad = Pedidodetalle::where('pedido', $request->pedido)
                ->sum('cantidad');

            //Saco la cantidad detalleNN
            $cantidad += Pedidodetallenn::where('pedido', $request->pedido)
                ->sum('cantidad');

            //Busco el cliente
            $cliente = Cliente::where('id', $pedido->cliente)->first();

            //Calculo
            //$subTotal = $pedido->total - $pedido->envio + $pedido->DescuentoNeto + $pedido->DescuentoPromociones;
            $vendedorNombre = $pedido->vendedor ? optional($pedido->vendedores)->nombre : '';

            $pais = Pais::where('id', $pedido->paisEnvio)->first();
            $NombrePais = '';
            if(isset($pais->nombre)){$NombrePais = $pais->nombre;}
            //Genero el invoice
            $invoiceId = DB::table('invoice')->insertGetId([
                'fecha' => date('Y-m-d H:i:s'),
                'cliente' => $pedido->cliente,
                'total' => $pedido->total,
                'formaDePago' => $pedido->formaDePago,
                'estado' => 1,
                'observaciones' => $pedido->observaciones,
                'anulada' => 0,
                'billTo' => $cliente->direccionBill,
                'shipTo' => $pedido->nombreEnvio . "\n" . $pedido->domicilioEnvio . "\n" . $pedido->ciudadEnvio . "\n" . $pedido->regionEnvio . "\n" . $NombrePais . "\n" . 'ZIP: ' . $pedido->cpEnvio, // Envío | Cliente
                'shipVia' => '',
                'FOB' => '',
                'Terms' => '',
                'fechaOrden' => $pedido->fecha,
                'salesPerson' => $vendedorNombre,
                'orden' => $pedido->id,
                'peso' => 0,
                'cantidad' => $cantidad,
                'DescuentoNeto' => $pedido->DescuentoNeto,
                'DescuentoPorcentual' => $pedido->DescuentoPorcentual,
                'UPS' => '',
                'TotalEnvio' => $pedido->TotalEnvio,
                'codigoUPS' => $request->codigoUPS,
                'subTotal' => $subTotal,
                'DescuentoPorPromociones' => $pedido->DescuentoPromociones,
                'IdActiveCampaign' => 0,
            ]);

            //Busco los detalles
            $SQL = "INSERT INTO `invoicedetalle`
                (`qordered`,
                `qshipped`,
                `qborder`,
                `itemNumber`,
                `Descripcion`,
                `listPrice`,
                `netPrice`,
                `invoice`)
                SELECT cantidad AS qordered,
                    cantidad AS qshipped,
                    cantidad AS qborder,
                    producto.codigo AS itemNumber,
                    producto.nombre AS descripcion,
                    pedidodetalle.precio AS listPrice,
                    pedidodetalle.precio AS netPrice,
                    {$invoiceId} AS invoice
                FROM pedidodetalle
                    LEFT JOIN producto ON producto.id=pedidodetalle.producto
                    LEFT JOIN marcaproducto ON producto.marca=marcaproducto.id
                WHERE pedido=?
                UNION
                SELECT cantidad AS qordered,
                    cantidad AS qshipped,
                    cantidad AS qborder,
                    'NN' AS itemNumber,
                    descripcion,
                    precio AS listPrice,
                    precio AS netPrice,
                    {$invoiceId} AS invoice
                FROM pedidodetallenn
                WHERE pedido=?;";

            DB::select($SQL, [$pedido->id, $pedido->id]);

            $pedido->estado = 2; //pagado
            $pedido->invoice = $invoiceId;
            $pedido->save();



            //Busco el  invoice
            $invoice = Invoice::find($invoiceId);

            return response()->json($invoice, Response::HTTP_OK);
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createToEmail($invoiceId)
    {
        try {

            // $cantidad = 0;

            // //Busco el pedido
            // $pedido = Pedido::where('id', $pedidoId)->first();

            // $SQL = "
            //     SELECT SUM(total) as total
            //     FROM (
            //         SELECT IFNULL(SUM(precio * cantidad), 0) AS total
            //         FROM pedidodetalle
            //         WHERE pedido = ?
            //         UNION ALL
            //         SELECT IFNULL(SUM(precio * cantidad), 0) AS total
            //         FROM pedidodetallenn
            //         WHERE pedido = ?
            //     ) AS combined_totals
            // ";

            // $result = DB::select($SQL, [$pedido->id, $pedido->id]);

            // $subTotal = $result[0]->total;

            // //Saco la cantidad detalle
            // $cantidad = Pedidodetalle::where('pedido', $pedidoId)
            //     ->sum('cantidad');

            // //Saco la cantidad detalleNN
            // $cantidad += Pedidodetallenn::where('pedido', $pedidoId)
            //     ->sum('cantidad');

            // //Busco el cliente
            // $cliente = Cliente::where('id', $pedido->cliente)->first();

            // //Calculo
            // //$subTotal = $pedido->total - $pedido->envio + $pedido->DescuentoNeto + $pedido->DescuentoPromociones;
            // $vendedorNombre = $pedido->vendedor ? optional($pedido->vendedores)->nombre : '';

            // $pais = Pais::where('id', $pedido->paisEnvio)->first();
            // $NombrePais = '';
            // if(isset($pais->nombre)){$NombrePais = $pais->nombre;}
            // //Genero el invoice
            // $invoiceId = DB::table('invoice')->insertGetId([
            //     'fecha' => date('Y-m-d H:i:s'),
            //     'cliente' => $pedido->cliente,
            //     'total' => $pedido->total,
            //     'formaDePago' => $pedido->formaDePago,
            //     'estado' => 1,
            //     'observaciones' => $pedido->observaciones,
            //     'anulada' => 0,
            //     'billTo' => $cliente->direccionBill,
            //     'shipTo' => $pedido->nombreEnvio . "\n" . $pedido->domicilioEnvio . "\n" . $pedido->ciudadEnvio . "\n" . $pedido->regionEnvio . "\n" . $NombrePais . "\n" . 'ZIP: ' . $pedido->cpEnvio, // Envío | Cliente
            //     'shipVia' => '',
            //     'FOB' => '',
            //     'Terms' => '',
            //     'fechaOrden' => $pedido->fecha,
            //     'salesPerson' => $vendedorNombre,
            //     'orden' => $pedido->id,
            //     'peso' => 0,
            //     'cantidad' => $cantidad,
            //     'DescuentoNeto' => $pedido->DescuentoNeto,
            //     'DescuentoPorcentual' => $pedido->DescuentoPorcentual,
            //     'UPS' => '',
            //     'TotalEnvio' => $pedido->TotalEnvio,
            //     'codigoUPS' => 1234,
            //     'subTotal' => $subTotal,
            //     'DescuentoPorPromociones' => $pedido->DescuentoPromociones,
            //     'IdActiveCampaign' => 0,
            // ]);

            // //Busco los detalles
            // $SQL = "INSERT INTO `invoicedetalle`
            //     (`qordered`,
            //     `qshipped`,
            //     `qborder`,
            //     `itemNumber`,
            //     `Descripcion`,
            //     `listPrice`,
            //     `netPrice`,
            //     `invoice`)
            //     SELECT cantidad AS qordered,
            //         cantidad AS qshipped,
            //         cantidad AS qborder,
            //         producto.codigo AS itemNumber,
            //         producto.nombre AS descripcion,
            //         pedidodetalle.precio AS listPrice,
            //         pedidodetalle.precio AS netPrice,
            //         {$invoiceId} AS invoice
            //     FROM pedidodetalle
            //         LEFT JOIN producto ON producto.id=pedidodetalle.producto
            //         LEFT JOIN marcaproducto ON producto.marca=marcaproducto.id
            //     WHERE pedido=?
            //     UNION
            //     SELECT cantidad AS qordered,
            //         cantidad AS qshipped,
            //         cantidad AS qborder,
            //         'NN' AS itemNumber,
            //         descripcion,
            //         precio AS listPrice,
            //         precio AS netPrice,
            //         {$invoiceId} AS invoice
            //     FROM pedidodetallenn
            //     WHERE pedido=?;";

            // DB::select($SQL, [$pedido->id, $pedido->id]);

            // $pedido->estado = 2; //pagado
            // $pedido->invoice = $invoiceId;
            // $pedido->save();



            //Busco el  invoice
            $invoice = Invoice::find($invoiceId);

            return response()->json($invoice, Response::HTTP_OK);
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function regenerate(Request $request)
    {
        // Actualizo datos de pedido
        try {

            $actualizarPedido = new PedidoService;
            $actualizarPedido->update($request);
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        // Borro invoiceDetalle
        $pedido = Pedido::where('id', $request->id)->first();

        try {
            $invoice = Invoice::findOrFail($pedido->invoice);
            $fillableAttributes = $invoice->getFillable();

            foreach ($fillableAttributes as $attribute) {
                if ($attribute !== 'id' & $attribute !== 'fecha') { // Limpio todos los campos del invoice para poder generarlos de nuevo
                    $invoice->{$attribute} = null;
                }
            }

            $invoice->save();

            Invoicedetalle::where('invoice', $pedido->invoice)->delete();
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        // Crear invoice regenerar

        $pedidoDetalleCantidad = 0;
        $pedidoDetalleNnCantidad = 0;

        $pedidoDetalleCantidad = Pedidodetalle::where('pedido', $request->id)->groupBy('pedido')
            ->selectRaw('pedido, SUM(cantidad) as suma_cantidad')
            ->first();



        if ($pedidoDetalleCantidad == '') {
            $cantidad = 0;
        } else {
            $cantidad = $pedidoDetalleCantidad->suma_cantidad;
        }

        $invoiceData = new CreateTransformer();
        $invoiceData = $invoiceData->transform($pedido, $cantidad, $request);

        try {
            Invoice::where('id', $pedido->invoice)->update($invoiceData);
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $query = "INSERT INTO invoicedetalle (id, qordered, qshipped, qborder, itemNumber, descripcion, listPrice, netPrice, invoice)
        SELECT
            NULL as id,
            pedidodetalle.cantidad as qordered,
            pedidodetalle.cantidad as qshipped,
            pedidodetalle.cantidad as qborder,
            codigo as itemNumber,
            producto.nombre as descripcion,
            pedidodetalle.precio as listPrice,
            pedidodetalle.precio as netPrice,
            {$invoice->id} as invoice
        FROM pedidodetalle
        LEFT JOIN producto ON producto.id = pedidodetalle.producto
        WHERE pedidodetalle.pedido = {$request->id}
        UNION
        SELECT
            NULL as id,
            sum(pedidodetallenn.cantidad) as qordered,
            sum(pedidodetallenn.cantidad) as qshipped,
            sum(pedidodetallenn.cantidad) as qborder,
            'NN' as itemNumber,
            pedidodetallenn.descripcion as descripcion,
            pedidodetallenn.precio as listPrice,
            pedidodetallenn.precio as netPrice,
            {$invoice->id} as invoice
        FROM pedidodetallenn
        WHERE pedidodetallenn.pedido = {$request->id}
        GROUP BY pedidodetallenn.descripcion,pedidodetallenn.precio
    ";

        try {
            DB::insert($query);
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$invoice) {
            return response()->json(['error' => 'Failed to create Invoice'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($invoice, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        $invoice = Invoice::find($request->id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $invoice->update($request->all());
        $invoice->refresh();

        return response()->json($invoice, Response::HTTP_OK);
    }

    public function updateSend(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $invoice->update($request->all());
        $invoice->refresh();

        /** ENVIO DE EMAIL **/

        $invoiceService = new InvoiceService;
        $invoiceService->invoiceParaEmail($request->id);

        $totalArticulos = Invoicedetalle::where('invoice', $request->id)->sum('qordered');
        $datosParaEmail = [
            "totalArticulos"=>$totalArticulos,
            "subtotal"=>$request->subTotal,
            "total"=>$request->total,
            "codigoSeguimiento"=>$request->codigoSeguimiento,
            "fechaPedido"=>$request->fechaOrden,
            "direccionEnvio"=>$request->shipTo,
            "metodoPago"=>"Tarjeta de Crédito",
            "invoiceId"=>$request->id
        ];

        Log::info($datosParaEmail);

        /** Envio por email PDF**/
        $cuerpo = '';
        $emailMdo = env('MAIL_COTIZACION_MDO');

        if (isset($cliente->email)) {

            $destinatarios = [
                $emailMdo,
                $cliente->email,
                'doralice@mayoristasdeopticas.com'
            ];
        } else {
            $destinatarios = [
                $emailMdo,
                'doralice@mayoristasdeopticas.com'
            ];
        }

        $rutaArchivoZip = storage_path('app/public/tmpdf/cotizacion_'.$request->id.'.pdf');
        Mail::to($destinatarios)->send(new EnvioMailInvoice($cuerpo, $rutaArchivoZip, $datosParaEmail));


        return response()->json($invoice, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $invoice = Invoice::find($request->id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], Response::HTTP_NOT_FOUND);
        }

        $invoice->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
