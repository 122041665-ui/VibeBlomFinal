<?php

namespace App\Http\Controllers;

use App\Services\FastApiService;
use Illuminate\Http\RedirectResponse;
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
            return back()->with('error', 'Tu sesión web está activa, pero no hay sesión activa en la API.');
        }

        try {
            $response = $api->post('/favorites/toggle', [
                'place_id' => $place,
            ], $token);

            if (!$response->successful()) {
                $message = $response->json('detail')
                    ?? $response->json('message')
                    ?? 'No se pudo actualizar favoritos.';

                return back()->with('error', $message);
            }

            $data = $response->json();

            return back()->with(
                'success',
                $data['message'] ?? 'Favorito actualizado correctamente.'
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo conectar con la API de favoritos.');
        }
    }

    public function mine(FastApiService $api): View
    {
        $token = $this->getApiToken();

        if (!$token) {
            return view('places.favorites', [
                'favorites' => collect(),
            ])->with('error', 'Tu sesión web está activa, pero no hay sesión activa en la API.');
        }

        try {
            $response = $api->get('/favorites', $token);

            if (!$response->successful()) {
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
            return view('places.favorites', [
                'favorites' => collect(),
            ])->with('error', 'No se pudo conectar con la API de favoritos.');
        }
    }
}