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

    public function __construct($cuerpo,$subject,$nombre)
    {
        $this->cuerpo = $cuerpo;
        $this->subject = $subject;
        $this->nombre = $nombre;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view($this->cuerpo)
                    ->with(['nombre' => $this->nombre]);
    }
}
