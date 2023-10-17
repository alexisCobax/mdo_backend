<?php

namespace App\Http\Controllers;

use App\Services\DescuentosService;
use Illuminate\Http\Request;

class DescuentosController extends Controller
{
    private $service;

    public function __construct(DescuentosService $DescuentosService)
    {
        $this->service = $DescuentosService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\DescuentosService $service
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->service->findAll($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\DescuentosService $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->service->discount($request->cupon, $request->total, $request->descuento);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\DescuentosService $service
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        return $this->service->add($request);
    }
}
