<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DescuentosService;

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
}
