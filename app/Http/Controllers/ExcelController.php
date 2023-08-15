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

    public function procesarExcel(Request $request){

        return $this->service->procesar($request);

    }

}
