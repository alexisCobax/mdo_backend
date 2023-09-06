<?php

namespace App\Http\Controllers;

use App\Services\CarritoWebService;
use Illuminate\Http\Request;

class CarritoWebController extends Controller
{
    private $service;

    public function __construct(CarritoWebService $CarritoWebService)
    {
        $this->service = $CarritoWebService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\CarritoWebService $service
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
     * @param  use App\Services\CarritoWebService $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->service->findByToken($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CarritoWebService $service
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        return $this->service->findStatus($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CarritoWebService $service
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
     * @param  use App\Services\CarritoWebService $service
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
     * @param  use App\Services\CarritoWebService $service
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

    public function procesar(Request $request)
    {
        return $this->service->procesar($request);
    }
}
