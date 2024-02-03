<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioCotizacionMailConAdjuntoNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public $cuerpo;
    public $rutaCotizacion;
    public $rutaArchivoFijo;
    public $view;
    public $subject;

    public function __construct($cuerpo, $rutaCotizacion, $rutaArchivoFijo, $view, $subject)
    {
        $this->cuerpo = $cuerpo;
        $this->rutaCotizacion = $rutaCotizacion;
        $this->rutaArchivoFijo = $rutaArchivoFijo;
        $this->view = $view;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view($this->view)
                    ->attach($this->rutaCotizacion)
                    ->attach($this->rutaArchivoFijo);
    }
}
