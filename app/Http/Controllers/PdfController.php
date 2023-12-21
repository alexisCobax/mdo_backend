<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Recibo;
use App\Services\InvoiceService;
use App\Services\ProformaService;
use App\Services\ReciboService;
use App\Services\CotizacionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    private $proforma;
    private $invoice;
    private $recibo;
    private $cotizacion;

    public function __construct(
        ProformaService $ProformaService, 
        InvoiceService $InvoiceService, 
        ReciboService $ReciboService,
        CotizacionService $cotizacionService)
    {
        $this->proforma = $ProformaService;
        $this->invoice = $InvoiceService;
        $this->recibo = $ReciboService;
        $this->cotizacion = $cotizacionService;
    }

    public function proforma(Request $request)
    {
        return $this->proforma->findById($request);
    }

    public function invoice(Request $request)
    {
        return $this->invoice->findByIdPdf($request);
    }

    public function recibo(Request $request)
    {

        return $this->recibo->findById($request);
    }

    public function cotizacion(Request $request)
    {
        return $this->cotizacion->findByIdPdf($request);
    }
}
