@component('mail::message')
# ¡Bienvenido a VibeBloom, {{ $user->name }}! 🎉

Gracias por unirte a nuestra comunidad.  
Ahora puedes explorar los mejores lugares de la ciudad y compartir tus propios descubrimientos.

@component('mail::button', ['url' => route('dashboard')])
Entrar al Dashboard
@endcomponent

@endcomponent
