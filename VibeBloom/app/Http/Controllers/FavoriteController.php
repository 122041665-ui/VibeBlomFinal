<?php

namespace App\Http\Controllers;

use App\Services\FastApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    private function getApiToken(): ?string
    {
        $token = session('access_token');

        if (is_string($token) && trim($token) !== '') {
            return $token;
        }

        $nestedToken = data_get(session('user'), 'access_token')
            ?? data_get(session('api'), 'access_token')
            ?? data_get(session('api_user'), 'access_token');

        if (is_string($nestedToken) && trim($nestedToken) !== '') {
            return $nestedToken;
        }

        return null;
    }

    public function toggle(int $place, FastApiService $api): RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'Inicia sesión nuevamente para guardar lugares en favoritos.');
        }

        try {
            $response = $api->post('/favorites/toggle', [
                'place_id' => $place,
            ], $token);

            if (!$response->successful()) {
                if ($response->status() === 401) {
                    session()->forget(['access_token', 'api', 'api_user']);

                    return redirect()->route('login')
                        ->with('error', 'Tu sesión venció. Inicia sesión nuevamente para continuar.');
                }

                $message = $response->json('detail')
                    ?? $response->json('message')
                    ?? 'No pudimos actualizar tus favoritos. Inténtalo nuevamente.';

                return back()->with('error', $message);
            }

            $data = $response->json();
            $message = $data['message'] ?? (($data['is_favorite'] ?? false)
                ? 'Lugar guardado en tus favoritos.'
                : 'Lugar eliminado de tus favoritos.');

            return back()->with(
                'success',
                $message
            );
        } catch (\Throwable $e) {
            Log::error('No se pudo actualizar un favorito.', [
                'user_id' => auth()->id(),
                'place_id' => $place,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'No pudimos conectar con el servicio de favoritos. Inténtalo nuevamente.');
        }
    }

    public function mine(FastApiService $api): View
    {
        $token = $this->getApiToken();

        if (!$token) {
            return view('places.favorites', [
                'favorites' => collect(),
            ])->with('error', 'Inicia sesión nuevamente para ver tus favoritos.');
        }

        try {
            $response = $api->get('/favorites', $token);

            if (!$response->successful()) {
                if ($response->status() === 401) {
                    session()->forget(['access_token', 'api', 'api_user']);
                }

                $message = $response->json('detail')
                    ?? $response->json('message')
                    ?? 'No se pudieron cargar los favoritos.';

                return view('places.favorites', [
                    'favorites' => collect(),
                ])->with('error', $message);
            }

            $favorites = collect($response->json());

            return view('places.favorites', compact('favorites'));
        } catch (\Throwable $e) {
            Log::error('No se pudieron cargar los favoritos.', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return view('places.favorites', [
                'favorites' => collect(),
            ])->with('error', 'No se pudo conectar con la API de favoritos.');
        }
    }
}
