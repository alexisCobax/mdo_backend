<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private $service;

    public function __construct(InvoiceService $InvoiceService)
    {
        $this->service = $InvoiceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\InvoiceService $service
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
     * @param  use App\Services\InvoiceService $service
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
     * @param  use App\Services\InvoiceService $service
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->service->create($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\InvoiceService $service
     * @return \Illuminate\Http\Response
     */
    public function regenerate(Request $request)
    {
        return $this->service->regenerate($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  use App\Services\InvoiceService $service
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
     * @param  use App\Services\InvoiceService $service
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
