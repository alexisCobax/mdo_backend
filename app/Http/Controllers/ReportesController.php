<?php

namespace App\Http\Controllers;

use App\Services\ReportesService;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    private $service;

    public function __construct(ReportesService $ReportesService)
    {
        $this->service = $ReportesService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function stockList(Request $request)
    {
        return $this->service->stockList($request);
    }

        /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function stockReport(Request $request)
    {
        return $this->service->stockReport($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function productos(Request $request)
    {
        return $this->service->productos($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function invoices(Request $request)
    {
        return $this->service->invoices($request);
    }
}
