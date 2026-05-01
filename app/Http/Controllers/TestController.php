<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\EnvioMailAgradecimientoCompra;

class TestController extends Controller
{
    private $service;

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\TestService $service
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //     $mail = new EnvioMailAgradecimientoCompra(
    //         $request->email
    //     );

    //     $resultado = $mail->enviar();

    //     return response()->json($resultado);
    // }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\TestService $service
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
     * @param  use App\Services\TestService $service
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
     * @param  use App\Services\TestService $service
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
     * @param  use App\Services\TestService $service
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
