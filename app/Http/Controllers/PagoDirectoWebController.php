<?php

namespace App\Http\Controllers;

use App\Services\PagoDirectoWebService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PagoDirectoWebController extends Controller
{
    private $service;

    public function __construct(PagoDirectoWebService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            return $this->service->create($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar el pago'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
