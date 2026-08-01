<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FastApiService;
use App\Models\UserNotification;
use App\Services\AI\ContentModerator;

class ReviewController extends Controller
{
    public function store(Request $request, $place, FastApiService $api, ContentModerator $moderator)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'body.required' => 'Escribe tu reseña antes de publicarla.',
            'body.min' => 'La reseña debe tener al menos 5 caracteres.',
            'body.max' => 'La reseña no puede superar 1000 caracteres.',
        ]);

        $moderation = $moderator->review($data['body'], 'reseña');
        if (!$moderation['allowed']) {
            return back()->withInput()->withErrors([
                'body' => 'La reseña debe cambiarse porque infringe las normas: '.($moderation['reason'] ?? 'contenido no permitido.'),
            ]);
        }

        $token = session('access_token');

        if (!$token || !is_string($token)) {
            return back()->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->post('/reviews', [
                'place_id' => (int) $place,
                'body' => $data['body'],
            ], $token);

            if (!$response->successful()) {
                $json = $response->json();

                $message = is_array($json)
                    ? ($json['detail'] ?? $json['message'] ?? 'No se pudo publicar la reseña.')
                    : 'No se pudo publicar la reseña.';

                return back()->with('error', $message);
            }

            UserNotification::sendTo(
                user: auth()->id(),
                type: 'review_created',
                title: 'Reseña publicada',
                body: 'Tu reseña se publicó correctamente.',
                url: route('places.show', $place),
                actor: auth()->user(),
                data: ['place_id' => (int) $place]
            );

            return back()->with('success', 'Reseña publicada.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo conectar con la API para publicar la reseña.');
        }
    }

    public function destroy($place, $review, FastApiService $api)
    {
        $token = session('access_token');

        if (!$token || !is_string($token)) {
            return back()->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->delete("/reviews/{$review}", $token);

            if (!$response->successful()) {
                $json = $response->json();

                $message = is_array($json)
                    ? ($json['detail'] ?? $json['message'] ?? 'No se pudo eliminar la reseña.')
                    : 'No se pudo eliminar la reseña.';

                return back()->with('error', $message);
            }

            UserNotification::sendTo(
                user: auth()->id(),
                type: 'review_deleted',
                title: 'Reseña eliminada',
                body: 'Eliminaste una reseña.',
                url: route('places.show', $place),
                actor: auth()->user(),
                data: ['place_id' => (int) $place, 'review_id' => (int) $review]
            );

            return back()->with('success', 'Reseña eliminada correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo conectar con la API para eliminar la reseña.');
        }
    }
}
