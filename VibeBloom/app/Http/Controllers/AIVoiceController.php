<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI;

use App\Models\Place;
use App\Services\FastApiService;
use App\Services\AI\PreferenceExtractor;
use App\Services\AI\PreferenceNormalizer;
use App\Services\AI\PlaceRanker;

class AIVoiceController extends Controller
{
    public function index()
    {
        return view('ai.voice', [
            'openAiConfigured' => filled(config('services.openai.key')),
        ]);
    }

    /**
     * Convierte Place real a estructura de tarjeta (dashboard-compatible)
     */
    private function placeToCard(Place $place): array
    {
        // Usa tus accessors del modelo (photo_url / photos_urls)
        $photoUrl = $place->photo_url ?? asset('images/vibebloom.png');

        // Por si photo viene con / al inicio
        if (!empty($place->photo)) {
            $photoUrl = asset('storage/' . ltrim((string)$place->photo, '/'));
        }

        $extras = $place->photos_urls ?? [];
        if (!is_array($extras)) $extras = [];

        return [
            'id'     => $place->id,
            'name'   => $place->name,
            'city'   => $place->city,
            'type'   => $place->type,
            'rating' => (int) ($place->rating ?? 0),
            'price'  => $place->price,

            // claves que tu frontend ya entiende
            'photo'       => $place->photo,       // "places/xxx.jpg"
            'photo_url'   => $photoUrl,           // "http://.../storage/places/xxx.jpg"
            'photos_urls' => $extras,             // [urls...]

            'url' => route('places.show', $place->id),
        ];
    }

    private function normalizePlacesResponse($json): \Illuminate\Support\Collection
    {
        if (is_array($json) && array_key_exists('data', $json) && is_array($json['data'])) {
            return collect($json['data']);
        }

        if (is_array($json)) {
            return collect($json);
        }

        return collect();
    }

    private function normalizeText(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value), 'UTF-8');
        $value = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', '_'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', ' '],
            $value
        );

        return preg_replace('/\s+/', ' ', $value) ?: '';
    }

    private function normalizeTypeValue(?string $value): ?string
    {
        $type = $this->normalizeText($value);

        return match (true) {
            str_contains($type, 'rest') || str_contains($type, 'comida') => 'RESTAURANTE',
            str_contains($type, 'cafe') || str_contains($type, 'cafeteria') => 'CAFETERIA',
            str_contains($type, 'antro') || str_contains($type, 'club') || str_contains($type, 'discoteca') => 'ANTRO',
            str_contains($type, 'bar') => 'BAR',
            str_contains($type, 'parque') || str_contains($type, 'natur') => 'PARQUE',
            str_contains($type, 'plaza') || str_contains($type, 'centro comercial') || str_contains($type, 'mall') => 'PLAZA',
            str_contains($type, 'mirador') || str_contains($type, 'vista') => 'MIRADOR',
            str_contains($type, 'museo') || str_contains($type, 'arte') => 'MUSEO',
            $type !== '' => 'OTRO',
            default => null,
        };
    }

    private function normalizePhotoUrl(?string $photo): string
    {
        $photo = trim((string) $photo);

        if ($photo === '') return asset('images/vibebloom.png');

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

    private function apiPlaceToCard(array $place, array $matchReasons = [], ?float $score = null): array
    {
        $id = $place['id'] ?? null;
        $photo = $place['photo_url'] ?? $place['photo'] ?? null;
        $type = $this->normalizeTypeValue($place['type'] ?? null) ?: 'OTRO';
        $lat = $place['lat'] ?? $place['latitude'] ?? null;
        $lng = $place['lng'] ?? $place['longitude'] ?? null;

        return [
            'id' => $id,
            'name' => (string) ($place['name'] ?? 'Lugar'),
            'city' => (string) ($place['city'] ?? 'Sin ciudad'),
            'address' => (string) ($place['address'] ?? ''),
            'description' => (string) ($place['description'] ?? ''),
            'type' => $type,
            'rating' => (int) ($place['rating'] ?? 0),
            'price' => $place['price'] ?? null,
            'lat' => is_numeric($lat) ? (float) $lat : null,
            'lng' => is_numeric($lng) ? (float) $lng : null,
            'photo' => $place['photo'] ?? null,
            'photo_url' => $this->normalizePhotoUrl($photo),
            'photos_urls' => is_array($place['photos_urls'] ?? null) ? $place['photos_urls'] : [],
            'url' => $id ? route('places.show', $id) : '#',
            'map_url' => route('places.map'),
            'match_reasons' => array_values(array_unique(array_filter($matchReasons))),
            'match_score' => $score !== null ? round($score, 1) : null,
        ];
    }

    private function scoreApiPlace(array $place, array $prefs, string $text): ?array
    {
        $score = 0.0;
        $type = $this->normalizeTypeValue($prefs['type'] ?? null);
        $placeType = $this->normalizeTypeValue($place['type'] ?? null);
        $city = $this->normalizeText($prefs['city'] ?? null);
        $zone = $this->normalizeText($prefs['zone'] ?? null);
        $name = $this->normalizeText($place['name'] ?? null);
        $placeCity = $this->normalizeText($place['city'] ?? null);
        $description = $this->normalizeText($place['description'] ?? null);

        $price = is_numeric($place['price'] ?? null) ? (float) $place['price'] : null;
        $rating = is_numeric($place['rating'] ?? null) ? (float) $place['rating'] : null;
        $minPrice = is_numeric($prefs['min_price'] ?? null) ? (float) $prefs['min_price'] : null;
        $maxPrice = is_numeric($prefs['max_price'] ?? null) ? (float) $prefs['max_price'] : null;
        $minRating = is_numeric($prefs['rating_min'] ?? null) ? (float) $prefs['rating_min'] : null;
        $maxRating = is_numeric($prefs['rating_max'] ?? null) ? (float) $prefs['rating_max'] : null;

        if ($type && $placeType !== $type) return null;
        if ($city && !str_contains($placeCity, $city)) return null;
        if ($zone && !str_contains($placeCity . ' ' . $description . ' ' . $name, $zone)) return null;
        if ($minPrice !== null && ($price === null || $price < $minPrice)) return null;
        if ($maxPrice !== null && ($price === null || $price > $maxPrice)) return null;
        if ($minRating !== null && ($rating === null || $rating < $minRating)) return null;
        if ($maxRating !== null && ($rating === null || $rating > $maxRating)) return null;

        $reasons = [];

        if ($type && $placeType === $type) {
            $score += 60;
            $reasons[] = 'Tipo ideal';
        }

        if ($city && str_contains($placeCity, $city)) {
            $score += 24;
            $reasons[] = 'Ciudad coincide';
        }

        if ($rating !== null) {
            $score += $rating * 3;
            if ($rating >= 4) $reasons[] = 'Buena calificación';
        }

        if ($price !== null) {
            if ($maxPrice !== null) {
                $score += max(0, 18 - ($price / max(1, $maxPrice)) * 8);
                $reasons[] = 'Dentro del presupuesto';
            }

            if (in_array('cheapest', (array) ($prefs['priorities'] ?? []), true)) {
                $score += max(0, 28 - ($price / 20));
                $reasons[] = 'Opción económica';
            }

            if (in_array('premium', (array) ($prefs['priorities'] ?? []), true)) {
                $score += min(18, $price / 60);
                $reasons[] = 'Perfil premium';
            }
        }

        $haystack = $name . ' ' . $placeCity . ' ' . $description . ' ' . $this->normalizeText($place['type'] ?? null);
        foreach ((array) ($prefs['features'] ?? []) as $feature) {
            $feature = $this->normalizeText($feature);
            if ($feature !== '' && str_contains($haystack, $feature)) {
                $score += 6;
                $reasons[] = 'Coincide con detalles pedidos';
            }
        }

        foreach ((array) ($prefs['avoid'] ?? []) as $avoid) {
            $avoid = $this->normalizeText($avoid);
            if ($avoid !== '' && str_contains($haystack, $avoid)) $score -= 12;
        }

        $tokens = array_filter(explode(' ', $this->normalizeText($text)), fn ($token) => mb_strlen($token) >= 4);
        foreach (array_slice($tokens, 0, 8) as $token) {
            if (str_contains($haystack, $token)) $score += 2;
        }

        return [
            'place' => $place,
            'score' => $score,
            'price' => $price,
            'rating' => $rating,
            'reasons' => $reasons ?: ['Coincidencia general'],
        ];
    }

    private function recommendFromApiPlaces(FastApiService $api, array $prefs, string $text, int $limit): array
    {
        try {
            $response = $api->get('/places');
            if (!$response->successful()) return [];

            return $this->normalizePlacesResponse($response->json())
                ->filter(fn ($place) => is_array($place))
                ->map(fn ($place) => $this->scoreApiPlace($place, $prefs, $text))
                ->filter()
                ->sort(function ($a, $b) use ($prefs) {
                    if ($a['score'] !== $b['score']) return $b['score'] <=> $a['score'];

                    $priorities = (array) ($prefs['priorities'] ?? []);
                    if (in_array('cheapest', $priorities, true)) {
                        if ($a['price'] === null && $b['price'] !== null) return 1;
                        if ($a['price'] !== null && $b['price'] === null) return -1;
                        if ($a['price'] !== null && $b['price'] !== null && $a['price'] !== $b['price']) {
                            return $a['price'] <=> $b['price'];
                        }
                    }

                    return ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
                })
                ->take($limit)
                ->map(fn ($item) => $this->apiPlaceToCard($item['place'], $item['reasons'] ?? [], $item['score'] ?? null))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function summarizeAppliedFilters(array $prefs, int $limit): array
    {
        $filters = [];

        if (!empty($prefs['type'])) $filters[] = 'Tipo: ' . $prefs['type'];
        if (!empty($prefs['city'])) $filters[] = 'Ciudad: ' . $prefs['city'];
        if (!empty($prefs['zone'])) $filters[] = 'Zona: ' . $prefs['zone'];
        if (!empty($prefs['max_price'])) $filters[] = 'Máx: MXN $' . number_format((float) $prefs['max_price'], 0);
        if (!empty($prefs['min_price'])) $filters[] = 'Mín: MXN $' . number_format((float) $prefs['min_price'], 0);
        if (!empty($prefs['rating_min'])) $filters[] = 'Rating mín: ' . $prefs['rating_min'];
        if (!empty($prefs['rating_max'])) $filters[] = 'Rating máx: ' . $prefs['rating_max'];
        if (!empty($prefs['price'])) $filters[] = 'Presupuesto: ' . $prefs['price'];

        $filters[] = 'Límite: ' . $limit;

        return $filters;
    }

    private function fallbackPreferencesFromText(string $text, ?string $forcedCity = null): array
    {
        $normalized = $this->normalizeText($text);

        $type = $this->normalizeTypeValue($text);
        $city = $forcedCity ?: null;

        $knownCities = [
            'queretaro' => 'Querétaro',
            'santiago de queretaro' => 'Santiago de Querétaro',
            'ciudad de mexico' => 'Ciudad de México',
            'cdmx' => 'Ciudad de México',
            'mexico' => 'Ciudad de México',
        ];

        if (!$city) {
            foreach ($knownCities as $needle => $label) {
                if (str_contains($normalized, $needle)) {
                    $city = $label;
                    break;
                }
            }
        }

        $prefs = [
            'type' => $type,
            'city' => $city,
            'zone' => null,
            'price' => null,
            'max_price' => null,
            'min_price' => null,
            'rating_max' => null,
            'rating_min' => null,
            'features' => [],
            'avoid' => [],
            'noise_pref' => null,
            'crowd_pref' => null,
            'priorities' => [],
        ];

        if (preg_match('/\b(entre)\s*\$?\s*([0-9]{2,6})\s*y\s*\$?\s*([0-9]{2,6})\b/u', $normalized, $m)) {
            $a = (int) $m[2];
            $b = (int) $m[3];
            $prefs['min_price'] = min($a, $b);
            $prefs['max_price'] = max($a, $b);
        } elseif (preg_match('/\b(menos de|maximo|max|hasta)\s*\$?\s*([0-9]{2,6})\b/u', $normalized, $m)) {
            $prefs['max_price'] = (int) $m[2];
        } elseif (preg_match('/\$\s*([0-9]{2,6})|\b([0-9]{2,6})\s*(pesos|mxn)\b/u', $normalized, $m)) {
            $prefs['max_price'] = (int) ($m[1] ?: $m[2]);
        }

        if (preg_match('/\b(barato|barata|baratos|baratas|economico|economica|economicos|economicas)\b/u', $normalized)) {
            $prefs['price'] = 'bajo';
            $prefs['max_price'] = $prefs['max_price'] ?? 300;
            $prefs['priorities'][] = 'cheapest';
        }

        if (preg_match('/\b(premium|lujo|caro|cara|caros|caras)\b/u', $normalized)) {
            $prefs['price'] = 'alto';
            $prefs['min_price'] = $prefs['min_price'] ?? 450;
            $prefs['priorities'][] = 'premium';
        }

        if (preg_match('/\b(minimo|mínimo|al menos)\s*([1-5])\s*estrellas?\b/u', $normalized, $m)) {
            $prefs['rating_min'] = (int) $m[2];
        }

        if (preg_match('/\b(maximo|máximo|no mas de|no más de)\s*([1-5])\s*estrellas?\b/u', $normalized, $m)) {
            $prefs['rating_max'] = (int) $m[2];
        }

        return $prefs;
    }

    /**
     * Decide cuántos resultados devolver según el texto del usuario
     * - si el usuario pide un número, se respeta (1..50)
     * - si pide "todos", devuelve 30 (seguro)
     * - default: 6
     */
    private function resolverLimiteDesdeTexto(string $texto): int
    {
        $t = mb_strtolower(trim($texto));

        // “solo uno”, “una opción”, “una recomendación”
        if (preg_match('/\b(solo\s*uno|una\s*opci[oó]n|una\s*recomendaci[oó]n|[uú]nica)\b/u', $t)) {
            return 1;
        }

        // “dame 5”, “quiero 10”, “muéstrame 7”, “recomiéndame 3”
        if (preg_match('/\b(dame|quiero|muestrame|mu[eé]strame|recomiendame|recomi[eé]ndame)\s*(\d{1,3})\b/u', $t, $m)) {
            return max(1, min(50, (int) $m[2]));
        }

        // “top 10”
        if (preg_match('/\btop\s*(\d{1,3})\b/u', $t, $m)) {
            return max(1, min(50, (int) $m[1]));
        }

        // “varios”, “opciones”, “dame más”
        if (preg_match('/\b(varios|opciones|recomendaciones|dame\s*m[aá]s)\b/u', $t)) {
            return 8;
        }

        // “todos”
        if (preg_match('/\b(todos|todas)\b/u', $t)) {
            return 30;
        }

        return 6;
    }

    /**
     * Detecta saludos simples
     */
    private function esSaludo(string $texto): bool
    {
        $t = mb_strtolower(trim($texto));

        return (bool) preg_match(
            '/\b(hola|holi|hey|buenas|buenos\s*d[ií]as|buenas\s*tardes|buenas\s*noches|qu[eé]\s*onda|que\s*onda|saludos)\b/u',
            $t
        );
    }

    /**
     * Respuesta humana para saludo (mini conversación)
     */
    private function respuestaSaludo(): string
    {
        $variantes = [
            "¡Hey! 👋 ¿Qué vibe traes hoy? ¿Café tranqui, comida rica o plan noche?",
            "¡Buenas! 😄 ¿Qué buscas hoy: algo relax, algo para comer o un lugar con vista?",
            "¡Qué onda! 👋 Dime qué se te antoja: cafetería, restaurante, bar o un plan al aire libre."
        ];

        return $variantes[array_rand($variantes)];
    }

    /**
     * Construye la línea obligatoria: “A continuación se muestran resultados de: …”
     */
    private function construirIntroResultados(string $texto, array $prefs, ?string $city = null): string
    {
        $tipo = $prefs['type'] ?? $prefs['tipo'] ?? null;
        $vibe = $prefs['vibe'] ?? $prefs['ambiente'] ?? null;

        $partes = [];

        if ($tipo) $partes[] = is_array($tipo) ? implode(', ', $tipo) : $tipo;
        if ($vibe) $partes[] = is_array($vibe) ? implode(', ', $vibe) : $vibe;
        if ($city) $partes[] = $city;

        $resumen = trim(implode(' · ', array_filter($partes, fn($x) => $x !== '')));

        // fallback: usa el texto del usuario
        if ($resumen === '') {
            $resumen = mb_substr($texto, 0, 80);
        }

        return "A continuación se muestran resultados de: {$resumen}.";
    }

    /**
     * POST /ai/voz/recomendar
     */
    public function recommendFromAudio(
        Request $request,
        FastApiService $api,
        PreferenceExtractor $extractor,
        PreferenceNormalizer $normalizer,
        PlaceRanker $ranker
    ) {
        $request->validate([
            'audio' => 'nullable|file|max:10240',
            'text'  => 'nullable|string|max:1000',
            'city'  => 'nullable|string|max:60',

            // opcional: si lo mandas desde el frontend, lo respetamos
            'limit' => 'nullable|integer|min:1|max:200',
        ]);

        $texto = trim((string) $request->input('text', ''));

        // =========================
        // AUDIO → TEXTO (Whisper)
        // =========================
        if ($texto === '') {
            if (!$request->hasFile('audio')) {
                return response()->json(['error' => 'No se recibió texto ni audio.'], 422);
            }

            $rutaTemporal = $request->file('audio')->store('voz');
            $rutaAbsoluta = Storage::path($rutaTemporal);

            try {
                $cliente = OpenAI::client(config('services.openai.key'));

                $res = $cliente->audio()->transcribe([
                    'model' => 'whisper-1',
                    'file'  => fopen($rutaAbsoluta, 'r'),
                ]);

                // compat array / object
                if (is_array($res) && isset($res['text'])) {
                    $texto = trim((string)$res['text']);
                } else {
                    $texto = trim((string)($res->text ?? ''));
                }
            } catch (\Throwable $e) {
                Storage::delete($rutaTemporal);
                return response()->json([
                    'error'   => 'No se pudo transcribir el audio.',
                    'detalle' => $e->getMessage(),
                ], 500);
            }

            Storage::delete($rutaTemporal);

            if ($texto === '') {
                return response()->json(['error' => 'No se detectó voz o no se pudo transcribir.'], 422);
            }
        }

        // =========================
        // CHAT: saludo → respuesta humana (sin ranking)
        // =========================
        if ($this->esSaludo($texto)) {
            return response()->json([
                'transcripcion' => $texto,
                'assistant_reply' => $this->respuestaSaludo(),
                'preferencias_extraidas' => [],
                'limit' => 0,
                'resultados' => [],
            ]);
        }

        // =========================
        // IA: extraer + normalizar
        // =========================
        try {
            try {
                $prefsCrudas = $extractor->extraer($texto, $request->input('city'));
            } catch (\Throwable $e) {
                $prefsCrudas = $this->fallbackPreferencesFromText($texto, $request->input('city'));
            }

            $prefs = $normalizer->normalizar($prefsCrudas);

            // ✅ límite variable:
            // 1) si viene por request (front) se respeta
            // 2) si no, se infiere del texto
            $limit = $request->filled('limit')
                ? max(1, min(200, (int)$request->input('limit')))
                : $this->resolverLimiteDesdeTexto($texto);

            // =========================
            // Ranking (devuelve ids + score)
            // =========================
            $ranking = $ranker->obtenerTop($prefs, $limit);

            // =========================
            // Traer Places reales y formar tarjetas
            // (manteniendo el orden del ranking)
            // =========================
            $ids = collect($ranking)->map(function ($item) {
                if (is_array($item)) return $item['id'] ?? null;
                if (is_object($item)) return $item->id ?? null;
                return null;
            })->filter()->unique()->values();

            $apiCards = [];

            if ($ids->isEmpty()) {
                $apiCards = $this->recommendFromApiPlaces($api, $prefs, $texto, $limit);

                return response()->json([
                    'transcripcion' => $texto,
                    'assistant_reply' => $apiCards
                        ? $this->construirIntroResultados($texto, $prefs, $request->input('city'))
                        : "No encontré resultados con eso. Prueba cambiando ciudad, tipo de lugar o presupuesto.",
                    'preferencias_extraidas' => $prefs,
                    'filtros_aplicados' => $this->summarizeAppliedFilters($prefs, $limit),
                    'limit' => $limit,
                    'resultados' => $apiCards,
                ]);
            }

            $places = Place::whereIn('id', $ids)->get()->keyBy('id');

            $cards = $ids->map(function ($id) use ($places) {
                $p = $places->get($id);
                return $p ? $this->placeToCard($p) : null;
            })->filter()->values();

            if ($cards->isEmpty()) {
                $apiCards = $this->recommendFromApiPlaces($api, $prefs, $texto, $limit);
                $cards = collect($apiCards);
            }

            return response()->json([
                'transcripcion' => $texto,
                'assistant_reply' => $this->construirIntroResultados($texto, $prefs, $request->input('city')),
                'preferencias_extraidas' => $prefs,
                'filtros_aplicados' => $this->summarizeAppliedFilters($prefs, $limit),
                'limit' => $limit,
                'resultados' => $cards->values(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Ocurrió un error al generar recomendaciones.',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }
}
