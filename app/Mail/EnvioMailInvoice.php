<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioMailInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $cuerpo;
    public $rutaCotizacion;
    public $rutaArchivoFijo;
    public $datosParaEmail;

    public function __construct($cuerpo, $rutaCotizacion, $datosParaEmail)
    {
        $this->cuerpo = $cuerpo;
        $this->rutaCotizacion = $rutaCotizacion;
        $this->datosParaEmail = $datosParaEmail;
        //$this->rutaArchivoFijo = $rutaArchivoFijo;
    }

    public function build()
    {
        return $this->subject('Mayorista de ópticas, confirmación de envío #'.$this->datosParaEmail['invoiceId'])
                    ->view('mdo.emailInvoice')
                    ->with(['datos' => $this->datosParaEmail])
                    ->attach($this->rutaCotizacion);
                    //->attach($this->rutaArchivoFijo);
    }
}

