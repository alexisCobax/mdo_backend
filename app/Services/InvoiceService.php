<?php

namespace App\Services;

use App\Filters\Invoices\InvoicesFilters;
use App\Models\Invoice;
use App\Models\Invoicedetalle;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use App\Transformers\Invoices\CreateDetalleTransformer;
use App\Transformers\Invoices\CreateTransformer;
use App\Transformers\Invoices\FindByIdTransformer;
use Barryvdh\DomPDF\Facade\Pdf;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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

    public function create(Request $request)
    {
        
        $pedido = Pedido::find($request->pedido);

        $sqlCantidad = Pedidodetalle::where('pedido', $request->pedido)->groupBy('pedido')
            ->selectRaw('pedido, SUM(cantidad) as suma_cantidad')
            ->first();

        if($sqlCantidad==''){
            $cantidad = 0;
        }else{
            $cantidad = $sqlCantidad->suma_cantidad;
        }

        $invoiceData = new CreateTransformer();
        $invoiceData = $invoiceData->transform($pedido, $cantidad, $request);

        $invoice = Invoice::create($invoiceData);

        $pedidosDetalle = Pedidodetalle::where('pedido', $request->pedido)
            ->select('cantidad', 'precio', 'producto')
            ->get();

        $pedidosDetalleNn = PedidodetalleNn::where('pedido', $request->pedido)
            ->select('id', 'cantidad', 'precio', 'descripcion')
            ->get();

        $resultadoFinal = $pedidosDetalle->merge($pedidosDetalleNn);

        $invoiceDetalle = new CreateDetalleTransformer();
        $invoiceTransformado = $invoiceDetalle->transform($resultadoFinal, $invoice->id);
        $invoiceDetalle = Invoicedetalle::insert($invoiceTransformado);

        if (!$invoice) {
            return response()->json(['error' => 'Failed to create Invoice'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $pedido->invoice = $invoice->id;
        $pedido->save();

        return response()->json($invoice, Response::HTTP_OK);
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

        // Borro invoice e invoiceDetalle
        $pedido = Pedido::where('id', $request->id)->first();

        try {
            $invoice = Invoice::findOrFail($pedido->invoice);
            $fillableAttributes = $invoice->getFillable();

            foreach ($fillableAttributes as $attribute) {
                if ($attribute !== 'id') {
                    $invoice->{$attribute} = null;
                }
            }

            $invoice->save();

            Invoicedetalle::where('invoice', $pedido->invoice)->delete();
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        // Crear invoice

        $sqlCantidad = Pedidodetalle::where('pedido', $request->id)->groupBy('pedido')
            ->selectRaw('pedido, SUM(cantidad) as suma_cantidad') 
            ->first();

            if($sqlCantidad==''){
                $cantidad = 0;
            }else{
                $cantidad = $sqlCantidad->suma_cantidad;
            }

        $invoiceData = new CreateTransformer();
        $invoiceData = $invoiceData->transform($pedido, $cantidad, $request);

        try {
            Invoice::where('id', $pedido->invoice)->update($invoiceData);
        } catch (Error $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $query = "
        INSERT INTO invoicedetalle (id, qordered, qshipped, qborder, itemNumber, descripcion, listPrice, netPrice, invoice)
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
            pedidodetallenn.cantidad as qordered,
            pedidodetallenn.cantidad as qshipped,
            pedidodetallenn.cantidad as qborder,
            'NN' as itemNumber,
            pedidodetallenn.descripcion as descripcion,
            pedidodetallenn.precio as listPrice,
            pedidodetallenn.precio as netPrice,
            {$invoice->id} as invoice
        FROM pedidodetallenn
        WHERE pedidodetallenn.pedido = {$request->id}
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
