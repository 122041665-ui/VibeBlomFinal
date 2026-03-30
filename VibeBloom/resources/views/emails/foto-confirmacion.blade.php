@component('mail::message')
# ¡Tu foto se ha subido correctamente! 📸

El lugar **{{ $place->name }}** ya tiene tu foto registrada.

Gracias por aportar a la comunidad de **VibeBloom** 💙

@component('mail::button', ['url' => route('places.show', $place->id)])
Ver Lugar
@endcomponent

@endcomponent
