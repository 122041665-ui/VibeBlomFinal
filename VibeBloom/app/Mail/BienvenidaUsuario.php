<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BienvenidaUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $email;

    public function __construct($user)
    {
        $this->user = $user;
        $this->email = $user->email ?? '';
    }

    public function build()
    {
        $subject = '¡Bienvenido a VibeBloom! 🎉';

        if (!empty($this->email)) {
            $subject .= ' | ' . $this->email;
        }

        return $this->subject($subject)
                    ->markdown('emails.bienvenida', [
                        'user' => $this->user,
                        'email' => $this->email,
                    ]);
    }
}
