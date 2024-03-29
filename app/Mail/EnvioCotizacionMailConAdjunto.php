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
    public $rutaArchivoFijo;

    public function __construct($cuerpo, $rutaCotizacion, $rutaArchivoFijo)
    {
        $this->cuerpo = $cuerpo;
        $this->rutaCotizacion = $rutaCotizacion;
        $this->rutaArchivoFijo = $rutaArchivoFijo;
    }

    public function build()
    {
        return $this->subject('Cotizacion')
                    ->view('mdo.emailCotizacion')
                    ->attach($this->rutaCotizacion)
                    ->attach($this->rutaArchivoFijo);
    }
}
