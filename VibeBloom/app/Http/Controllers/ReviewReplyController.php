<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FastApiService;

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

            return back()->with('success', 'Respuesta eliminada correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo conectar con la API para eliminar la respuesta.');
        }
    }
}