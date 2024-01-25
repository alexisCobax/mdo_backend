<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioMailComunicado extends Mailable
{
    use Queueable, SerializesModels;

    public $cuerpo;
    public $subject;
    public $informacion;

    public function __construct($cuerpo,$subject,$informacion)
    {
        $this->cuerpo = $cuerpo;
        $this->subject = $subject;
        $this->informacion = $informacion;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view($this->cuerpo)
                    ->with(['informacion' => $this->informacion]);
    }
}
