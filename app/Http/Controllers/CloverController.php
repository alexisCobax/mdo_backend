<?php

namespace App\Http\Controllers;

use App\Services\CloverService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CloverController extends Controller
{
    private $service;

    public function __construct(CloverService $CloverService)
    {
        $this->service = $CloverService;
    }

    public function processCloverPayment(Request $request)
    {
        try {
            return $this->service->creditCard($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error genera el pago'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
