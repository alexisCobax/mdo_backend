<?php

namespace App\Http\Controllers;

use App\Models\Tipobanner;
use Illuminate\Http\Request;
use App\Services\BannerService;

class BannerController extends Controller
{
    private $service;

    public function __construct(BannerService $BannerService)
    {
        $this->service = $BannerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\BannerService $service
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
     * @param  use App\Services\BannerService $service
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
     * @param  use App\Services\BannerService $service
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Tipobanner $tipobanner)
    {
        return $this->service->create($request, $tipobanner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  use App\Services\BannerService $service
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
     * @param  use App\Services\BannerService $service
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
