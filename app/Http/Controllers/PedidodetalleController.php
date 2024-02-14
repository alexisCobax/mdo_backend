<?php

namespace App\Http\Controllers;

use App\Services\PedidodetalleService;
use Illuminate\Http\Request;

class PedidodetalleController extends Controller
{
    private $service;

    public function __construct(PedidodetalleService $PedidodetalleService)
    {
        $this->service = $PedidodetalleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\PedidodetalleService $service
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
     * @param  use App\Services\PedidodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->service->findById($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\PedidodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function showDetalle(Request $request)
    {
        return $this->service->findByPedidoId($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\PedidodetalleService $service
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
     * @param  use App\Services\PedidodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return $this->service->update($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  use App\Services\PedidodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function updateProducto(Request $request)
    {
        return $this->service->updateProducto($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\PedidodetalleService $service
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
}
