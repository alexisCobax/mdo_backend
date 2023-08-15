<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Filters\Invoices\InvoicesFilters;
use App\Helpers\CalcHelper;
use App\Models\Invoicedetalle;
use App\Models\Pedidodetalle;
use App\Transformers\Invoices\FindByIdTransformer;
use App\Transformers\Invoices\CreateDetalleTransformer;
use App\Transformers\Invoices\CreateTransformer;

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

        $invoiceTranformada = new FindByIdTransformer();
        $invoiceTranformada = $invoiceTranformada->transform($invoice);

        return response()->json(['data' => $invoiceTranformada], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $pedido = Pedido::find($request->pedido);

        $cantidad = Pedidodetalle::where('pedido', $request->pedido)->groupBy('pedido')
        ->selectRaw('pedido, SUM(cantidad) as suma_cantidad')
        ->get();

        $invoiceData = new CreateTransformer();
        $invoiceData = $invoiceData->transform($pedido, $cantidad, $request);
        $invoice = Invoice::create($invoiceData);

        $detalle = Pedidodetalle::where('pedido', $request->pedido)->get();

        $invoiceDetalle = new CreateDetalleTransformer();
        $invoiceTransformado = $invoiceDetalle->transform($detalle, $invoice->id);
        $invoiceDetalle = Invoicedetalle::insert($invoiceTransformado);

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
