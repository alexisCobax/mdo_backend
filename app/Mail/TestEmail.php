<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $productos;

        /**
     * Create a new message instance.
     *
     * @param \Illuminate\Support\Collection $productos
     */
    public function __construct($productos)
    {
        $this->productos = $productos;
    }

    public function build()
    {
        return $this->view('mdo.emailNuevosProductos')
                    ->subject('Email de Prueba');
    }
}
