<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PayeezyService;

class PayeezyController extends Controller
{

    private $service;

    public function __construct(PayeezyService $PayeezyService)
    {
        $this->service = $PayeezyService;
    }

    public function processPayeezyPayment(Request $request)
    {
        try {
            return $this->service->creditCard($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error genera el pago'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
