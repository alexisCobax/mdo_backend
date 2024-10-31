<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Transformers\Pdf\FindByIdTransformer;
use Illuminate\Http\Response;

class ProformaService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->first();

        $tranformer = new FindByIdTransformer();
        $proforma = $tranformer->transform($pedido);

        $pdf = Pdf::loadView('pdf.proforma', ['proforma'=>$proforma]);

        $pdf->getDomPDF();

        return $pdf->stream();
    }

    public function proformaParaEmail($pedidoId)
    {
        $pedido = Pedido::where('id', $pedidoId)->first();

        $tranformer = new FindByIdTransformer();
        $proforma = $tranformer->transform($pedido);

        $pdf = Pdf::loadView('pdf.proforma', ['proforma'=>$proforma]);

        $pdfContent = $pdf->output();

        // Guardar el PDF en el directorio storage/app/public/tmpPdf
        $pdfPath = 'public/tmpdf/' . 'proforma_' . $pedidoId . '.pdf';

        try {
            Storage::put($pdfPath, $pdfContent);

            return response()->json(['response' => 'Pdf Guardado!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request)
    {
        //--
    }

    public function delete(Request $request)
    {
        //--
    }
}
