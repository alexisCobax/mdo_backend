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
    public function topClientesList(Request $request)
    {
        return $this->service->topClientesList($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function topClientesReport(Request $request)
    {
        return $this->service->topClientesReport($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function topMarcasList(Request $request)
    {
        return $this->service->topMarcasList($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function topMarcasReport(Request $request)
    {
        return $this->service->topMarcasReport($request);
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
    public function recibosList(Request $request)
    {
        return $this->service->recibosList($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function recibosReport(Request $request)
    {
        return $this->service->recibosReport($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function productosList(Request $request)
    {
        return $this->service->productosList($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function productosReport(Request $request)
    {
        return $this->service->productosReport($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function invoicesList(Request $request)
    {
        return $this->service->invoicesList($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReportesService $service
     * @return \Illuminate\Http\Response
     */
    public function invoicesReport(Request $request)
    {
        return $this->service->invoicesReport($request);
    }
}
