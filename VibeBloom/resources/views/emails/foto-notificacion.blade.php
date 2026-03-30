@component('mail::message')
# Nueva foto subida por un usuario 📩

El usuario **{{ $user->name }}** subió una foto al lugar:

**{{ $place->name }} – {{ $place->city }}**

@component('mail::button', ['url' => route('places.show', $place->id)])
Ver Lugar
@endcomponent

@endcomponent
