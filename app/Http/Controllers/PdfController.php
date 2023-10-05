<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Recibo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoiceService;
use App\Services\ProformaService;

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

    public function recibo(Request $request)
    {

        $recibo = Recibo::where('pedido',$request->id)->first();

        $pdf = Pdf::loadView('pdf.recibo', ["recibo"=>$recibo]);

        //$dom_pdf = $pdf->getDomPDF();

        return $pdf->stream();

        //return $pdf->download('factura.pdf');
    }
}
