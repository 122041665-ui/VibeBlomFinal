<?php

namespace App\Http\Controllers;

use App\Models\Place;

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
    public function geojson()
    {
        // ✅ Lista de tipos válidos (según tu dropdown)
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

        // (se queda por si lo usas en capas mapbox, aunque ahorita ya usas HTML markers)
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

        // Solo lugares con coordenadas
        $places = Place::query()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->orderByDesc('created_at')
            ->get();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($places as $place) {
            // Tipo original (lo que verá el usuario)
            $typeOriginal = trim((string)($place->type ?? 'Otro'));
            if ($typeOriginal === '') $typeOriginal = 'Otro';

            // ✅ Normalizar tipo: minúsculas + sin acentos + trim
            $type = mb_strtolower($typeOriginal, 'UTF-8');
            $type = trim($type);

            // Reemplazo de acentos / ñ / ü
            $type = str_replace(
                ['á','é','í','ó','ú','ü','ñ'],
                ['a','e','i','o','u','u','n'],
                $type
            );

            // ✅ Normaliza casos comunes (por si guardaron variantes)
            if ($type === 'cafeteria' || $type === 'cafe') $type = 'cafeteria';
            if ($type === 'centrocomercial' || $type === 'centro_comercial') $type = 'centro comercial';

            // ✅ si no está en lista válida, lo mandamos a "otro"
            if (!in_array($type, $validTypes, true)) {
                $type = 'otro';
            }

            $maki = $typeToMaki[$type] ?? 'marker-15';

            // Foto para mini-card (storage)
            $photoUrl = $place->photo
                ? asset('storage/' . ltrim((string)$place->photo, '/'))
                : null;

            $geojson['features'][] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$place->lng, (float)$place->lat],
                ],
                'properties' => [
                    'id'       => $place->id,
                    'name'     => (string) $place->name,
                    'type'     => $typeOriginal,        // para mostrar
                    'iconKey'  => $type,                // para tu UI (pills + cards)
                    'city'     => (string) ($place->city ?? ''),
                    'rating'   => $place->rating ?? null,
                    'price'    => $place->price ?? null,

                    // por si vuelves a usar capas symbol alguna vez
                    'maki'     => $maki,

                    //  para mini-card con imagen
                    'photo_url'=> $photoUrl,

                    'url'      => route('places.show', $place->id),
                ],
            ];
        }

        return response()->json($geojson);
    }
}
