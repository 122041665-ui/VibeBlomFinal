<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\PlaceSubmission;
use App\Services\FastApiService;
use Illuminate\Support\Collection;

class PlacesMapController extends Controller
{
    /**
     * Vista del mapa
     */
    public function map()
    {
        return view('places.map');
    }

    /**
     * GeoJSON de lugares para Mapbox
     * - iconKey normalizado para UI (pills + cards)
     * - photo_url para mini-card
     */
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

    private function normalizeType(string $typeOriginal): string
    {
        $type = mb_strtolower(trim($typeOriginal), 'UTF-8');
        $type = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', '_'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', ' '],
            $type
        );

        $type = preg_replace('/\s+/', ' ', $type) ?: 'otro';

        if (in_array($type, ['cafeteria', 'cafe'], true)) return 'cafeteria';
        if (in_array($type, ['centrocomercial', 'centro comercial', 'mall'], true)) return 'centro comercial';

        return $type;
    }

    private function normalizePhotoUrl(?string $photo): ?string
    {
        $photo = trim((string) $photo);

        if ($photo === '') return null;

        if (
            str_starts_with($photo, 'http://') ||
            str_starts_with($photo, 'https://') ||
            str_starts_with($photo, '/') ||
            str_starts_with($photo, 'data:')
        ) {
            return $photo;
        }

        if (str_starts_with($photo, 'storage/')) {
            return asset($photo);
        }

        return asset('storage/' . ltrim($photo, '/'));
    }

    private function featureFromArray(array $place, array $validTypes, array $typeToMaki): ?array
    {
        $lat = $place['lat'] ?? $place['latitude'] ?? null;
        $lng = $place['lng'] ?? $place['longitude'] ?? null;

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }

        $typeOriginal = trim((string) ($place['type'] ?? 'Otro')) ?: 'Otro';
        $type = $this->normalizeType($typeOriginal);

        if (!in_array($type, $validTypes, true)) {
            $type = 'otro';
        }

        $id = $place['id'] ?? null;
        $photo = $place['photo_url'] ?? $place['photo'] ?? null;

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [(float) $lng, (float) $lat],
            ],
            'properties' => [
                'id' => $id ? 'api-' . $id : uniqid('api-', false),
                'real_id' => $id,
                'source' => 'api',
                'name' => (string) ($place['name'] ?? 'Lugar'),
                'type' => $typeOriginal,
                'iconKey' => $type,
                'city' => (string) ($place['city'] ?? ''),
                'rating' => $place['rating'] ?? null,
                'price' => $place['price'] ?? null,
                'maki' => $typeToMaki[$type] ?? 'marker-15',
                'photo_url' => $this->normalizePhotoUrl($photo),
                'url' => $id ? route('places.show', $id) : null,
            ],
        ];
    }

    public function geojson(FastApiService $api)
    {
        $validTypes = [
            'restaurante',
            'cafeteria',
            'bar',
            'antro',
            'parque',
            'mirador',
            'museo',
            'plaza',
            'centro comercial',
            'otro',
        ];

        $typeToMaki = [
            'restaurante'       => 'restaurant-15',
            'cafeteria'         => 'cafe-15',
            'bar'               => 'bar-15',
            'antro'             => 'music-15',
            'parque'            => 'park-15',
            'mirador'           => 'viewpoint-15',
            'museo'             => 'museum-15',
            'plaza'             => 'town-hall-15',
            'centro comercial'  => 'shop-15',
            'otro'              => 'marker-15',
        ];

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        try {
            $response = $api->get('/places');

            if ($response->successful()) {
                $this->normalizePlacesResponse($response->json())
                    ->each(function ($place) use (&$geojson, $validTypes, $typeToMaki) {
                        if (!is_array($place)) return;

                        $feature = $this->featureFromArray($place, $validTypes, $typeToMaki);
                        if ($feature) $geojson['features'][] = $feature;
                    });
            }
        } catch (\Throwable $e) {
            // Si la API no responde, mantenemos el mapa con datos locales.
        }

        $places = Place::query()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->orderByDesc('created_at')
            ->get();

        foreach ($places as $place) {
            $typeOriginal = trim((string)($place->type ?? 'Otro'));
            if ($typeOriginal === '') $typeOriginal = 'Otro';

            $type = $this->normalizeType($typeOriginal);

            if (!in_array($type, $validTypes, true)) {
                $type = 'otro';
            }

            $maki = $typeToMaki[$type] ?? 'marker-15';

            $photoUrl = $this->normalizePhotoUrl((string) ($place->photo ?? ''));

            $geojson['features'][] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$place->lng, (float)$place->lat],
                ],
                'properties' => [
                    'id'       => 'local-' . $place->id,
                    'real_id'  => $place->id,
                    'source'   => 'local',
                    'name'     => (string) $place->name,
                    'type'     => $typeOriginal,
                    'iconKey'  => $type,
                    'city'     => (string) ($place->city ?? ''),
                    'rating'   => $place->rating ?? null,
                    'price'    => $place->price ?? null,
                    'maki'     => $maki,
                    'photo_url'=> $photoUrl,
                    'url'      => route('places.show', $place->id),
                ],
            ];
        }

        $submissions = PlaceSubmission::with('photos')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->orderByDesc('created_at')
            ->get();

        foreach ($submissions as $submission) {
            $typeOriginal = trim((string)($submission->type ?? 'Otro')) ?: 'Otro';
            $type = $this->normalizeType($typeOriginal);

            if (!in_array($type, $validTypes, true)) {
                $type = 'otro';
            }

            $photo = $submission->photos->first()?->path;

            $geojson['features'][] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $submission->lng, (float) $submission->lat],
                ],
                'properties' => [
                    'id' => 'submission-' . $submission->id,
                    'real_id' => $submission->id,
                    'source' => 'submission',
                    'status' => (string) ($submission->status ?? 'pending'),
                    'name' => (string) $submission->name,
                    'type' => $typeOriginal,
                    'iconKey' => $type,
                    'city' => (string) ($submission->city ?? ''),
                    'rating' => $submission->rating ?? null,
                    'price' => $submission->price ?? null,
                    'maki' => $typeToMaki[$type] ?? 'marker-15',
                    'photo_url' => $this->normalizePhotoUrl($photo),
                    'url' => $submission->user_id === auth()->id()
                        ? route('place-submissions.show', $submission)
                        : null,
                ],
            ];
        }

        return response()->json($geojson);
    }
}
