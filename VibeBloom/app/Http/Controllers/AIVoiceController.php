<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI;

use App\Models\Place;
use App\Services\AI\PreferenceExtractor;
use App\Services\AI\PreferenceNormalizer;
use App\Services\AI\PlaceRanker;

class AIVoiceController extends Controller
{
    public function index()
    {
        return view('ai.voice');
    }

    /**
     * Convierte Place real a estructura de tarjeta (dashboard-compatible)
     */
    private function placeToCard(Place $place): array
    {
        // Usa tus accessors del modelo (photo_url / photos_urls)
        $photoUrl = $place->photo_url ?? asset('images/default.jpg');

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
            $prefsCrudas = $extractor->extraer($texto, $request->input('city'));
            $prefs       = $normalizer->normalizar($prefsCrudas);

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

            if ($ids->isEmpty()) {
                return response()->json([
                    'transcripcion' => $texto,
                    'assistant_reply' => "No encontré resultados con eso 😕 ¿me dices la ciudad y qué tipo de lugar prefieres?",
                    'preferencias_extraidas' => $prefs,
                    'limit' => $limit,
                    'resultados' => [],
                ]);
            }

            $places = Place::whereIn('id', $ids)->get()->keyBy('id');

            $cards = $ids->map(function ($id) use ($places) {
                $p = $places->get($id);
                return $p ? $this->placeToCard($p) : null;
            })->filter()->values();

            return response()->json([
                'transcripcion' => $texto,
                'assistant_reply' => $this->construirIntroResultados($texto, $prefs, $request->input('city')),
                'preferencias_extraidas' => $prefs,
                'limit' => $limit,
                'resultados' => $cards,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Ocurrió un error al generar recomendaciones.',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }
}
