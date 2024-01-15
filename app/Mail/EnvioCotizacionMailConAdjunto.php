<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioCotizacionMailConAdjunto extends Mailable
{
    use Queueable, SerializesModels;

    public $cuerpo;
    public $rutaCotizacion;

    public function __construct($cuerpo, $rutaCotizacion)
    {
        $this->cuerpo = $cuerpo;
        $this->rutaCotizacion = $rutaCotizacion;
    }

    public function build()
    {
        return $this->subject('Cotizacion')
                    ->view('mdo.emailCotizacion')
                    ->attach($this->rutaCotizacion);
    }
}
