<?php

namespace App\Http\Controllers;

use App\Services\PagoManualWebService;
use Illuminate\Http\Request;

class PagoManualWebController extends Controller
{
    private $service;

    public function __construct(PagoManualWebService $PagoManualWebService)
    {
        $this->service = $PagoManualWebService;
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\PagoManualWebService $service
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->service->create($request);
    }
}
