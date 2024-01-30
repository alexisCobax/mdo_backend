<?php

namespace App\Http\Controllers;

use App\Services\ExcelToJsonService;
use Illuminate\Http\Request;

class ExcelController extends Controller
{
    private $service;

    public function __construct(ExcelToJsonService $ExcelToJsonService)
    {
        $this->service = $ExcelToJsonService;
    }

    public function procesarExcel(Request $request)
    {

        return $this->service->procesar($request);

    }

    public function GenerarProductosCsv(Request $request)
    {

        return $this->service->generarProductos($request);

    }

    public function prospectoExcel(Request $request)
    {
        return $this->service->prospecto($request);
    }

    public function clienteExcel(Request $request)
    { 
        return $this->service->cliente($request);
    }
}
