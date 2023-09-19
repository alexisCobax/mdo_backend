<?php

namespace App\Http\Controllers;

use App\Services\PagoWebService;
use Illuminate\Http\Request;

class PagoWebController extends Controller
{
    private $service;

    public function __construct(PagoWebService $PagoWebService)
    {
        $this->service = $PagoWebService;
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\PagoWebService $service
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->service->create($request);
    }
}
