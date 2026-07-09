<?php

namespace App\Http\Controllers;

use App\Models\PlaceSubmission;
use App\Models\UserNotification;
use App\Services\FastApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PlaceController extends Controller
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

    private function extractFavoritePlaceIds($json): array
    {
        if (!is_array($json)) {
            return [];
        }

        return collect($json)
            ->map(function ($favorite) {
                if (is_array($favorite)) {
                    if (!empty($favorite['place_id'])) {
                        return (int) $favorite['place_id'];
                    }

                    if (!empty($favorite['place']) && is_array($favorite['place']) && !empty($favorite['place']['id'])) {
                        return (int) $favorite['place']['id'];
                    }
                }

                return null;
            })
            ->filter(fn ($id) => !is_null($id) && $id > 0)
            ->values()
            ->all();
    }

    private function fetchFavoritePlaceIds(FastApiService $api): array
    {
        $token = $this->getApiToken();

        if (!$token) {
            return [];
        }

        try {
            $response = $api->get('/favorites', $token);

            if (!$response->successful()) {
                return [];
            }

            return $this->extractFavoritePlaceIds($response->json());
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function fetchFavoritePlaces(FastApiService $api): Collection
    {
        $token = $this->getApiToken();

        if (!$token) {
            return collect();
        }

        try {
            $response = $api->get('/favorites', $token);

            if (!$response->successful()) {
                return collect();
            }

            return collect($response->json())
                ->map(function ($favorite) {
                    if (is_array($favorite) && !empty($favorite['place']) && is_array($favorite['place'])) {
                        return $favorite['place'];
                    }

                    if (is_array($favorite) && !empty($favorite['id']) && !empty($favorite['name'])) {
                        return $favorite;
                    }

                    return null;
                })
                ->filter()
                ->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function normalizePlacesResponse($json): Collection
    {
        if (is_array($json) && array_key_exists('data', $json) && is_array($json['data'])) {
            return collect($json['data']);
        }

        if (is_array($json)) {
            return collect($json);
        }

        return collect();
    }

    private function fetchPlaces(FastApiService $api): array
    {
        try {
            $response = $api->get('/places');

            if (!$response->successful()) {
                return [
                    'places' => collect(),
                    'favoritePlaceIds' => [],
                    'error' => 'No se pudieron cargar los lugares.',
                ];
            }

            $places = $this->normalizePlacesResponse($response->json());

            return [
                'places' => $places,
                'favoritePlaceIds' => $this->fetchFavoritePlaceIds($api),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'places' => collect(),
                'favoritePlaceIds' => [],
                'error' => 'No se pudo conectar con la API.',
            ];
        }
    }

    private function fetchMyPlaces(FastApiService $api): array
    {
        $token = $this->getApiToken();

        if (!$token) {
            return [
                'places' => collect(),
                'favoritePlaceIds' => [],
                'error' => 'No hay sesión activa en la API.',
            ];
        }

        try {
            $response = $api->get('/places/mine', $token);

            if (!$response->successful()) {
                return [
                    'places' => collect(),
                    'favoritePlaceIds' => [],
                    'error' => 'No se pudieron cargar tus lugares.',
                ];
            }

            $places = $this->normalizePlacesResponse($response->json());

            return [
                'places' => $places,
                'favoritePlaceIds' => $this->fetchFavoritePlaceIds($api),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'places' => collect(),
                'favoritePlaceIds' => [],
                'error' => 'No se pudo conectar con la API.',
            ];
        }
    }

    private function fetchProfileSubmissions(): Collection
    {
        if (!auth()->check()) {
            return collect();
        }

        return PlaceSubmission::with('photos')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'pendiente'])
            ->latest()
            ->take(12)
            ->get();
    }

    private function fetchProfileCreatedPlaces(): Collection
    {
        if (!auth()->check()) {
            return collect();
        }

        return PlaceSubmission::with('photos')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    private function normalizeMemory(array $memory): object
    {
        $photos = collect($memory['photos'] ?? [])
            ->map(function ($photo) {
                return (object) [
                    'id' => $photo['id'] ?? null,
                    'path' => $photo['path'] ?? null,
                    'url' => $photo['url'] ?? ($photo['photo_url'] ?? null),
                ];
            })
            ->values();

        return (object) [
            'id' => $memory['id'] ?? null,
            'user_id' => $memory['user_id'] ?? null,
            'title' => $memory['title'] ?? '',
            'description' => $memory['description'] ?? null,
            'memory_date' => $memory['memory_date'] ?? null,
            'location' => $memory['location'] ?? null,
            'photos' => $photos,
        ];
    }

    private function extractMemoryItems(array $json): array
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

    private function fetchProfileMemories(FastApiService $api): Collection
    {
        $token = $this->getApiToken();

        if (!$token) {
            return collect();
        }

        try {
            $response = $api->get('/memories', $token, [
                'page' => 1,
                'per_page' => 12,
            ]);

            if (!$response->successful()) {
                return collect();
            }

            $json = $response->json();
            $items = is_array($json) ? $this->extractMemoryItems($json) : [];

            return collect($items)
                ->map(fn ($memory) => $this->normalizeMemory((array) $memory))
                ->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function normalizeText(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $value = mb_strtoupper($value, 'UTF-8');

        return strtr($value, [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
            'Ñ' => 'N',
        ]);
    }

    private function normalizeType(?string $type): string
    {
        $type = $this->normalizeText($type);

        if ($type === '') {
            return 'OTRO';
        }

        $aliases = [
            'RESTAURANT'   => 'RESTAURANTE',
            'RESTAURANTES' => 'RESTAURANTE',
            'CAFE'         => 'CAFETERIA',
            'CAFÉ'         => 'CAFETERIA',
            'CAFETERIAS'   => 'CAFETERIA',
            'DISCOTECA'    => 'ANTRO',
            'CLUB'         => 'ANTRO',
            'CENTRO COMERCIAL' => 'PLAZA',
            'CENTROS COMERCIALES' => 'PLAZA',
            'MALL' => 'PLAZA',
            'MUSEOS'       => 'MUSEO',
            'MIRADORES'    => 'MIRADOR',
            'PLAZAS'       => 'PLAZA',
            'PARQUES'      => 'PARQUE',
        ];

        return $aliases[$type] ?? $type;
    }

    private function placeValue($place, string $key, $default = null)
    {
        if (is_array($place)) {
            return $place[$key] ?? $default;
        }

        if (is_object($place)) {
            return $place->{$key} ?? $default;
        }

        return $default;
    }

    private function applyFilters(Collection $places, Request $request): Collection
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $city = trim((string) $request->query('city', ''));
        $type = trim((string) $request->query('type', ''));
        $maxPriceRaw = trim((string) $request->query('max_price', ''));
        $maxPrice = $maxPriceRaw === '' ? null : str_replace([',', '$', 'MXN', 'mxn', ' '], '', $maxPriceRaw);

        $buscarNorm = $this->normalizeText($buscar);
        $cityNorm = $this->normalizeText($city);
        $typeNorm = $this->normalizeType($type);
        $hasValidMaxPrice = $maxPrice !== null && is_numeric($maxPrice) && (float) $maxPrice >= 0;

        return $places->filter(function ($place) use ($buscarNorm, $cityNorm, $typeNorm, $type, $maxPrice, $hasValidMaxPrice) {
            $placeName = (string) $this->placeValue($place, 'name', '');
            $placeCity = (string) $this->placeValue($place, 'city', '');
            $placeType = (string) $this->placeValue($place, 'type', 'OTRO');
            $placeDescription = (string) $this->placeValue($place, 'description', '');
            $placeAddress = (string) $this->placeValue($place, 'address', '');
            $placeReference = (string) $this->placeValue($place, 'reference', '');
            $placePrice = $this->placeValue($place, 'price', 0);

            $placeNameNorm = $this->normalizeText($placeName);
            $placeCityNorm = $this->normalizeText($placeCity);
            $placeTypeNorm = $this->normalizeType($placeType);
            $placeDescriptionNorm = $this->normalizeText($placeDescription);
            $placeAddressNorm = $this->normalizeText($placeAddress);
            $placeReferenceNorm = $this->normalizeText($placeReference);

            if ($buscarNorm !== '') {
                $matchesBuscar =
                    str_contains($placeNameNorm, $buscarNorm) ||
                    str_contains($placeCityNorm, $buscarNorm) ||
                    str_contains($placeTypeNorm, $buscarNorm) ||
                    str_contains($placeDescriptionNorm, $buscarNorm) ||
                    str_contains($placeAddressNorm, $buscarNorm) ||
                    str_contains($placeReferenceNorm, $buscarNorm);

                if (!$matchesBuscar) {
                    return false;
                }
            }

            if ($cityNorm !== '' && !str_contains($placeCityNorm, $cityNorm)) {
                return false;
            }

            if ($type !== '' && $placeTypeNorm !== $typeNorm) {
                return false;
            }

            if ($hasValidMaxPrice) {
                $price = is_numeric($placePrice) ? (float) $placePrice : 0;

                if ($price > (float) $maxPrice) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    public function index(Request $request, FastApiService $api): View
    {
        $data = $this->fetchPlaces($api);
        $data['places'] = $this->applyFilters($data['places'], $request);

        return view('places.index', [
            'places' => $data['places'],
            'favoritePlaceIds' => $data['favoritePlaceIds'],
            'error' => $data['error'],
        ]);
    }

    public function dashboard(Request $request, FastApiService $api): View
    {
        $data = $this->fetchPlaces($api);
        $data['places'] = $this->applyFilters($data['places'], $request);

        return view('dashboard', [
            'places' => $data['places'],
            'favoritePlaceIds' => $data['favoritePlaceIds'],
            'error' => $data['error'],
        ]);
    }

    public function myPlaces(Request $request, FastApiService $api): View|RedirectResponse
    {
        $data = $this->fetchMyPlaces($api);
        $data['places'] = $this->applyFilters($data['places'], $request);

        if ($data['error'] && $data['error'] === 'No hay sesión activa en la API.') {
            return redirect()->route('login')->with('error', $data['error']);
        }

        return view('places.mine', [
            'places' => $data['places'],
            'favoritePlaceIds' => $data['favoritePlaceIds'],
            'profileFavoritePlaces' => $this->fetchFavoritePlaces($api),
            'profileCreatedPlaces' => $this->fetchProfileCreatedPlaces(),
            'profileSubmissions' => $this->fetchProfileSubmissions(),
            'profileMemories' => $this->fetchProfileMemories($api),
            'profileFollowers' => auth()->user()?->followers()->latest('user_follows.created_at')->take(12)->get() ?? collect(),
            'profileFollowing' => auth()->user()?->following()->latest('user_follows.created_at')->take(12)->get() ?? collect(),
            'error' => $data['error'],
        ]);
    }

    public function create(): View
    {
        return view('places.create');
    }

    public function store(Request $request, FastApiService $api): RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric'],
            'photo' => ['nullable', 'string', 'max:255'],
            'photos' => ['nullable'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $response = $api->post('/places', $payload, $token);

            if (!$response->successful()) {
                return back()
                    ->withInput()
                    ->with('error', 'No se pudo guardar el lugar.');
            }

            UserNotification::sendTo(
                user: auth()->id(),
                type: 'place_created',
                title: 'Lugar creado',
                body: "Tu lugar '{$payload['name']}' fue guardado correctamente.",
                url: route('places.mine'),
                actor: auth()->user()
            );

            return redirect()
                ->route('places.mine')
                ->with('success', 'Lugar creado correctamente.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo conectar con la API para guardar el lugar.');
        }
    }

    public function edit(int $place, FastApiService $api): View|RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->get("/places/{$place}", $token);

            if (!$response->successful()) {
                return redirect()
                    ->route('places.mine')
                    ->with('error', 'No se pudo cargar el lugar para editar.');
            }

            $placeData = $response->json();

            if (!is_array($placeData)) {
                return redirect()
                    ->route('places.mine')
                    ->with('error', 'La respuesta del lugar no es válida.');
            }

            return view('places.edit', [
                'place' => $placeData,
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->route('places.mine')
                ->with('error', 'No se pudo conectar con la API para editar el lugar.');
        }
    }

    public function update(Request $request, int $place, FastApiService $api): RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric'],
            'photo' => ['nullable', 'string', 'max:255'],
            'photos' => ['nullable'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $response = $api->put("/places/{$place}", $payload, $token);

            if (!$response->successful()) {
                return back()
                    ->withInput()
                    ->with('error', 'No se pudo actualizar el lugar.');
            }

            return redirect()
                ->route('places.mine')
                ->with('success', 'Lugar actualizado correctamente.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo conectar con la API para actualizar el lugar.');
        }
    }

    public function destroy(int $place, FastApiService $api): RedirectResponse
    {
        $token = $this->getApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'No hay sesión activa en la API.');
        }

        try {
            $response = $api->delete("/places/{$place}", $token);

            if (!$response->successful()) {
                return redirect()
                    ->route('places.mine')
                    ->with('error', 'No se pudo eliminar el lugar.');
            }

            UserNotification::sendTo(
                user: auth()->id(),
                type: 'place_deleted',
                title: 'Lugar eliminado',
                body: 'El lugar fue eliminado correctamente.',
                url: route('places.mine'),
                actor: auth()->user(),
                data: ['place_id' => $place]
            );

            return redirect()
                ->route('places.mine')
                ->with('success', 'Lugar eliminado correctamente.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('places.mine')
                ->with('error', 'No se pudo conectar con la API para eliminar el lugar.');
        }
    }

    public function show($place, FastApiService $api): View|RedirectResponse
    {
        try {
            $response = $api->get("/places/{$place}");

            if (!$response->successful()) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'No se pudo cargar el detalle del lugar.');
            }

            $placeData = $response->json();

            if (!is_array($placeData)) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'La respuesta del lugar no es válida.');
            }

            return view('places.show', [
                'place' => $placeData,
                'favoritePlaceIds' => $this->fetchFavoritePlaceIds($api),
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No se pudo conectar con la API para cargar el lugar.');
        }
    }
}
