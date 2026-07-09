<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FastApiService;
use App\Models\UserNotification;

class ReviewReplyController extends Controller
{
    public function store(Request $request, $place, $review, FastApiService $api)
    {
        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $token = session('access_token');

        if (!$token || !is_string($token)) {
            return back()->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->post('/review-replies', [
                'review_id' => (int) $review,
                'body' => $data['body'],
            ], $token);

            if (!$response->successful()) {
                $json = $response->json();

                $message = is_array($json)
                    ? ($json['detail'] ?? $json['message'] ?? 'No se pudo publicar la respuesta.')
                    : 'No se pudo publicar la respuesta.';

                return back()->with('error', $message);
            }

            UserNotification::sendTo(
                user: auth()->id(),
                type: 'reply_created',
                title: 'Respuesta publicada',
                body: 'Tu respuesta se publicó correctamente.',
                url: route('places.show', $place),
                actor: auth()->user(),
                data: ['place_id' => (int) $place, 'review_id' => (int) $review]
            );

            return back()->with('success', 'Respuesta publicada.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo conectar con la API para publicar la respuesta.');
        }
    }

    public function destroy($place, $review, $reply, FastApiService $api)
    {
        $token = session('access_token');

        if (!$token || !is_string($token)) {
            return back()->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->delete("/review-replies/{$reply}", $token);

            if (!$response->successful()) {
                $json = $response->json();

                $message = is_array($json)
                    ? ($json['detail'] ?? $json['message'] ?? 'No se pudo eliminar la respuesta.')
                    : 'No se pudo eliminar la respuesta.';

                return back()->with('error', $message);
            }

            UserNotification::sendTo(
                user: auth()->id(),
                type: 'reply_deleted',
                title: 'Respuesta eliminada',
                body: 'Eliminaste una respuesta.',
                url: route('places.show', $place),
                actor: auth()->user(),
                data: ['place_id' => (int) $place, 'review_id' => (int) $review, 'reply_id' => (int) $reply]
            );

            return back()->with('success', 'Respuesta eliminada correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo conectar con la API para eliminar la respuesta.');
        }
    }
}
