<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\BienvenidaUsuario;

class EnviarCorreoBienvenida
{
    public function handle(Registered $event)
    {
        Mail::to($event->user->email)
            ->send(new BienvenidaUsuario($event->user));
    }
}
