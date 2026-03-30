<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FotoSubidaConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public $place;

    public function __construct($place)
    {
        $this->place = $place;
    }

    public function build()
    {
        return $this->subject('Tu foto se ha subido correctamente')
                    ->markdown('emails.foto-confirmacion');
    }
}
