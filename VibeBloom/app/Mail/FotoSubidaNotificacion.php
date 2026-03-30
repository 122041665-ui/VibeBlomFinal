<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FotoSubidaNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public $place;
    public $user;

    public function __construct($place, $user)
    {
        $this->place = $place;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Un usuario subió una nueva foto')
                    ->markdown('emails.foto-notificacion');
    }
}
