<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BienvenidaUsuario;

class EnviarCorreoBienvenida
{
    public function handle(Registered $event): void
    {
        try {
            Mail::to($event->user->email)
                ->send(new BienvenidaUsuario($event->user));
        } catch (\Throwable $exception) {
            // El correo es una acción secundaria: una indisponibilidad del
            // proveedor nunca debe convertir un registro exitoso en un 500.
            Log::warning('No se pudo enviar el correo de bienvenida', [
                'user_id' => $event->user->getKey(),
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
