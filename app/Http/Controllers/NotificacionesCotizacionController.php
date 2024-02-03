<?php

namespace App\Http\Controllers;

use App\Services\NotificacionesCotizacionService;
use Illuminate\Http\Request;

class NotificacionesCotizacionController extends Controller
{
    
    private $service;

    public function __construct(NotificacionesCotizacionService $NotificacionesCotizacionService)
    {
        $this->service = $NotificacionesCotizacionService;
    }
    /**
     * Notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cotizacion()
    {
        return $this->service->cotizacion();
    }
}
