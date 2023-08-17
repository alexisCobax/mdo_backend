<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Services\ProformaService;

class PdfController extends Controller
{

    private $service;

    public function __construct(ProformaService $ProformaService)
    {
        $this->service = $ProformaService;
    }

    public function proforma(Request $request)
    {
        return $this->service->findById($request);
    }

    public function factura()
    {

        // $data = ['nombre'=>'alexis'];
        // $pdf = Pdf::loadView('pdf.factura', $data);

        // $dom_pdf = $pdf->getDomPDF();

        // return $pdf->stream();

        //return $pdf->download('factura.pdf');
    }

    public function recibo()
    {


        // $data = ['nombre'=>'alexis'];
        // $pdf = Pdf::loadView('pdf.recibo', $data);

        // $dom_pdf = $pdf->getDomPDF();

        // return $pdf->stream();

        //return $pdf->download('factura.pdf');
    }
}
