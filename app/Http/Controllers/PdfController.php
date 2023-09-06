<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoiceService;
use App\Services\ProformaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    private $proforma;
    private $invoice;

    public function __construct(ProformaService $ProformaService, InvoiceService $InvoiceService)
    {
        $this->proforma = $ProformaService;
        $this->invoice = $InvoiceService;
    }

    public function proforma(Request $request)
    {
        return $this->proforma->findById($request);
    }

    public function invoice(Request $request)
    {
        return $this->invoice->findById($request);
    }

    public function recibo()
    {

        $data = ['nombre'=>'alexis'];
        $pdf = Pdf::loadView('pdf.recibo', $data);

        $dom_pdf = $pdf->getDomPDF();

        return $pdf->stream();

        return $pdf->download('factura.pdf');
    }
}
