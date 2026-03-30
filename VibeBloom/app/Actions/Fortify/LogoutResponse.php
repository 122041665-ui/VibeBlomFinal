<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * A dónde redirigir después de cerrar sesión.
     */
    public function toResponse($request)
    {
        return redirect('/places');
    }
}
