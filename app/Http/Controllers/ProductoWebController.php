<?php

namespace App\Http\Controllers;

use App\Services\ProductoWebService;
use Illuminate\Http\Request;

class ProductoWebController extends Controller
{
    private $service;

    public function __construct(ProductoWebService $ProductoWebService)
    {
        $this->service = $ProductoWebService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ProductoWebService $service
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->service->findAll($request);
    }

    /**
     * Display the specified stock resource.
     *
     * @param  use App\Services\ProductoWebService $service
     * @return \Illuminate\Http\Response
     */
    public function stock(Request $request)
    {
        return $this->service->stock($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\ProductoWebService $service
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
     * @param  use App\Services\ProductoWebService $service
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
     * @param  use App\Services\ProductoWebService $service
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
     * @param  use App\Services\ProductoWebService $service
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function related(Request $request)
    {
        return $this->service->related($request);
    }
}
