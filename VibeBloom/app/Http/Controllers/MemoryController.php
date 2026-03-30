<?php

namespace App\Http\Controllers;

use App\Services\FastApiService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MemoryController extends Controller
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

    private function normalizeMemory(array $memory): object
    {
        $photos = collect($memory['photos'] ?? [])
            ->map(function ($photo) {
                return (object) [
                    'id'   => $photo['id'] ?? null,
                    'path' => $photo['path'] ?? null,
                    'url'  => $photo['url'] ?? ($photo['photo_url'] ?? null),
                ];
            })
            ->values();

        return (object) [
            'id'          => $memory['id'] ?? null,
            'user_id'     => $memory['user_id'] ?? null,
            'title'       => $memory['title'] ?? '',
            'description' => $memory['description'] ?? null,
            'memory_date' => $memory['memory_date'] ?? null,
            'location'    => $memory['location'] ?? null,
            'created_at'  => $memory['created_at'] ?? null,
            'updated_at'  => $memory['updated_at'] ?? null,
            'photos'      => $photos,
        ];
    }

    private function parseJson($response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    private function extractItems(array $json): array
    {
        if (isset($json['data']) && is_array($json['data']) && array_is_list($json['data'])) {
            return $json['data'];
        }

        if (isset($json['data']['items']) && is_array($json['data']['items'])) {
            return $json['data']['items'];
        }

        if (isset($json['items']) && is_array($json['items'])) {
            return $json['items'];
        }

        if (isset($json['results']) && is_array($json['results'])) {
            return $json['results'];
        }

        if (array_is_list($json)) {
            return $json;
        }

        return [];
    }

    private function buildPaginator(array $json, array $mappedItems, int $defaultPerPage = 9): LengthAwarePaginator
    {
        $currentPage = (int) (
            $json['current_page']
            ?? data_get($json, 'data.current_page')
            ?? request()->integer('page', 1)
        );

        $perPage = (int) (
            $json['per_page']
            ?? data_get($json, 'data.per_page')
            ?? $defaultPerPage
        );

        $total = (int) (
            $json['total']
            ?? data_get($json, 'data.total')
            ?? count($mappedItems)
        );

        return new LengthAwarePaginator(
            $mappedItems,
            $total,
            $perPage,
            max($currentPage, 1),
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function index(Request $request, FastApiService $api): View|RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->get('/memories', $token, [
                'page' => $request->integer('page', 1),
                'per_page' => 9,
            ]);

            if ($response->failed()) {
                Log::error('Memories index failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return back()->with('error', 'No se pudieron cargar los recuerdos.');
            }

            $json = $this->parseJson($response);
            $items = $this->extractItems($json);

            $mappedItems = collect($items)
                ->map(fn ($memory) => $this->normalizeMemory((array) $memory))
                ->values()
                ->all();

            $memories = $this->buildPaginator($json, $mappedItems, 9);

            return view('memories.index', compact('memories'));
        } catch (\Throwable $e) {
            Log::error('Memories index exception', [
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudieron cargar los recuerdos.');
        }
    }

    public function create(): View
    {
        return view('memories.create');
    }

    public function store(Request $request, FastApiService $api): RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'memory_date' => ['nullable', 'date'],
            'location'    => ['nullable', 'string', 'max:120'],
            'photos'      => ['nullable', 'array', 'max:3'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $payload = [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'memory_date' => $data['memory_date'] ?? null,
            'location'    => $data['location'] ?? null,
        ];

        try {
            $response = $request->hasFile('photos')
                ? $api->postMultipart('/memories', $payload, $request->file('photos', []), $token, 'photos')
                : $api->post('/memories', $payload, $token);

            if ($response->failed()) {
                Log::error('Memories store failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return back()->withInput()->with('error', 'No se pudo guardar el recuerdo.');
            }

            return redirect()
                ->route('memories.index')
                ->with('success', 'Recuerdo guardado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Memories store exception', [
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'No se pudo guardar el recuerdo.');
        }
    }

    public function edit(int $memory, FastApiService $api): View|RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->get("/memories/{$memory}", $token);

            if ($response->failed()) {
                Log::error('Memories edit failed', [
                    'memory_id' => $memory,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if (in_array($response->status(), [403, 404], true)) {
                    abort($response->status());
                }

                return redirect()->route('memories.index')->with('error', 'No se pudo cargar el recuerdo.');
            }

            $json = $this->parseJson($response);
            $memory = $this->normalizeMemory((array) ($json['data'] ?? $json));

            return view('memories.edit', compact('memory'));
        } catch (\Throwable $e) {
            Log::error('Memories edit exception', [
                'memory_id' => $memory,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('memories.index')->with('error', 'No se pudo cargar el recuerdo.');
        }
    }

    public function update(Request $request, int $memory, FastApiService $api): RedirectResponse
{
    $token = $this->getApiToken();

    if (!$token) {
        return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
    }

    $data = $request->validate([
        'title'       => ['required', 'string', 'max:120'],
        'description' => ['nullable', 'string', 'max:2000'],
        'memory_date' => ['nullable', 'date'],
        'location'    => ['nullable', 'string', 'max:120'],
        'photos'      => ['nullable', 'array', 'max:3'],
        'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
    ]);

    $payload = [
        'title'       => $data['title'],
        'description' => $data['description'] ?? null,
        'memory_date' => $data['memory_date'] ?? null,
        'location'    => $data['location'] ?? null,
    ];

    try {
        $response = $api->putMultipart(
            "/memories/{$memory}",
            $payload,
            $request->file('photos', []),
            $token,
            'photos'
        );

        if ($response->failed()) {
            Log::error('Memories update failed', [
                'memory_id' => $memory,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if (in_array($response->status(), [403, 404], true)) {
                abort($response->status());
            }

            return back()->withInput()->with('error', 'No se pudo actualizar el recuerdo.');
        }

        return redirect()
            ->route('memories.index')
            ->with('success', 'Recuerdo actualizado correctamente.');
    } catch (\Throwable $e) {
        Log::error('Memories update exception', [
            'memory_id' => $memory,
            'message' => $e->getMessage(),
        ]);

        return back()->withInput()->with('error', 'No se pudo actualizar el recuerdo.');
    }
}

    public function destroy(int $memory, FastApiService $api): RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->delete("/memories/{$memory}", $token);

            if ($response->failed()) {
                Log::error('Memories destroy failed', [
                    'memory_id' => $memory,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if (in_array($response->status(), [403, 404], true)) {
                    abort($response->status());
                }

                return redirect()->route('memories.index')->with('error', 'No se pudo eliminar el recuerdo.');
            }

            return redirect()
                ->route('memories.index')
                ->with('success', 'Recuerdo eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Memories destroy exception', [
                'memory_id' => $memory,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('memories.index')->with('error', 'No se pudo eliminar el recuerdo.');
        }
    }
}