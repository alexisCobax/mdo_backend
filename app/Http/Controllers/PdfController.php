<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Recibo;
use App\Services\InvoiceService;
use App\Services\ProformaService;
use App\Services\ReciboService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    private $proforma;
    private $invoice;
    private $recibo;

    public function __construct(ProformaService $ProformaService, InvoiceService $InvoiceService, ReciboService $ReciboService)
    {
        $this->proforma = $ProformaService;
        $this->invoice = $InvoiceService;
        $this->recibo = $ReciboService;
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

        return $this->recibo->findById($request);
    }
}
