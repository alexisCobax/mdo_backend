<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function proforma()
    {


        $data = ['nombre'=>'alexis'];
        $pdf = Pdf::loadView('pdf.proforma', $data);

        $dom_pdf = $pdf->getDomPDF();

        return $pdf->stream();

        //return $pdf->download('proforma.pdf');
    }

    public function factura()
    {


        $data = ['nombre'=>'alexis'];
        $pdf = Pdf::loadView('pdf.factura', $data);

        $dom_pdf = $pdf->getDomPDF();

        return $pdf->stream();

        //return $pdf->download('factura.pdf');
    }

    public function recibo()
    {


        $data = ['nombre'=>'alexis'];
        $pdf = Pdf::loadView('pdf.recibo', $data);

        $dom_pdf = $pdf->getDomPDF();

        return $pdf->stream();

        //return $pdf->download('factura.pdf');
    }
}
