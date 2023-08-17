<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Transformers\Pdf\FindByIdTransformer;

class ProformaService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {

        $pedido = Pedido::find($request->id)->first();

        $tranformer = new FindByIdTransformer();
        $proforma = $tranformer->transform($pedido, $request);
        $pdf = Pdf::loadView('pdf.proforma', ["proforma"=>$proforma]);

        //$dom_pdf = $pdf->getDomPDF();

        return $pdf->stream();

        //return $pdf->download('proforma.pdf');

        //return $tranformer->transform($pedido, $request);
    }

    public function create(Request $request)
    {
        //--
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
