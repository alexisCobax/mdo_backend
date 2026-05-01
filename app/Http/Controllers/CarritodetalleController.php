<?php

namespace App\Http\Controllers;

use App\Services\CarritodetalleService;
use Illuminate\Http\Request;

class CarritodetalleController extends Controller
{
    private $service;

    public function __construct(CarritodetalleService $CarritodetalleService)
    {
        $this->service = $CarritodetalleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\CarritodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->service->findAll($request);
    }

        
    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CarritodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function asignarVendedor(Request $request)
    {
        return $this->service->asignarVendedor($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CarritodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function LiberarVendedor(Request $request)
    {
        return $this->service->liberarVendedor($request);
    }

    /* Generate a pedido from carrito.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  use App\Services\CarritodetalleService $service
    * @return \Illuminate\Http\Response
    */
   public function generarPedido(Request $request)
   {
       return $this->service->generarPedido($request);
   }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CarritodetalleService $service
     * @return \Illuminate\Http\Response
     */
    public function showVendedor(Request $request)
    {
        return $this->service->findByIdVendedor($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\CarritodetalleService $service
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
     * @param  use App\Services\CarritodetalleService $service
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
     * @param  use App\Services\CarritodetalleService $service
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
     * @param  use App\Services\CarritodetalleService $service
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
