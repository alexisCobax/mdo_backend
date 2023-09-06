<?php

namespace App\Http\Controllers;

use App\Services\CotizacionesWebService;
use Illuminate\Http\Request;

class CotizacionesWebController extends Controller
{
    private $service;

    public function __construct(CotizacionesWebService $CotizacionesWebService)
    {
        $this->service = $CotizacionesWebService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\CotizacionesWebService $service
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
     * @param  use App\Services\CotizacionesWebService $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->service->findById($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CotizacionesWebService $service
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->service->create($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  use App\Services\CotizacionesWebService $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return $this->service->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CotizacionesWebService $service
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        return $this->service->delete($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Procesar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function procesar(Request $request)
    {
        return $this->service->procesar($request);
    }
}
