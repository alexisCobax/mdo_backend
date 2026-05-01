<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioCotizacionMailSinAdjunto extends Mailable
{
    use Queueable, SerializesModels;

    public $cuerpo;
    public $subject;
    public $nombre;
    public $usuario;
    public $clave;

    public function __construct($cuerpo, $subject, $nombre, $usuario, $clave)
    {
        $this->cuerpo = $cuerpo;
        $this->subject = $subject;
        $this->nombre = $nombre;
        $this->usuario = $usuario;
        $this->clave = $clave;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view($this->cuerpo)
                    ->with([
                        'nombre' => $this->nombre,
                        'usuario' => $this->usuario,
                        'clave' => $this->clave
                    ]);
    }
}
