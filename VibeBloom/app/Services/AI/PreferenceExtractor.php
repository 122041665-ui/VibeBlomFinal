<?php

namespace App\Services\AI;

use OpenAI;

class PreferenceExtractor
{
    // Defaults “suaves” para que sí filtre cuando dicen “barato/caro” sin número
    private const DEFAULT_CHEAP_MAX = 300; // ajústalo a tu realidad
    private const DEFAULT_EXPENSIVE_MIN = 450;

    public function extraer(string $texto, ?string $ciudadForzada = null): array
    {
        $cliente = OpenAI::client(config('services.openai.key'));
        $textoL = mb_strtolower($texto);

        // ✅ Tipos reales (los mismos que usas en VibeBloom)
        $tiposValidos = [
            'RESTAURANTE', 'CAFETERIA', 'BAR', 'ANTRO',
            'PARQUE', 'PLAZA', 'MIRADOR', 'MUSEO', 'OTRO'
        ];

        // ✅ Prompt: JSON SI o SI
        $instrucciones = "Eres un extractor de preferencias para recomendar lugares.
Devuelve SOLO JSON válido, sin texto extra, sin markdown.

Campos (TODOS deben existir aunque sea null):
- type: uno de estos valores EXACTOS: " . implode(', ', array_map(fn($t) => "\"{$t}\"", $tiposValidos)) . " | null
- city: string|null
- zone: string|null
- price: \"bajo\"|\"medio\"|\"alto\"|null
- max_price: number|null
- min_price: number|null
- rating_max: number|null
- rating_min: number|null
- features: array de strings
- avoid: array de strings
- noise_pref: \"bajo\"|\"medio\"|\"alto\"|null
- crowd_pref: \"bajo\"|\"medio\"|\"alto\"|null
- priorities: array de strings

REGLAS IMPORTANTES:
- Si el usuario dice 'cafetería' o 'café' => type=\"CAFETERIA\"
- Si el usuario dice 'restaurante' o 'comida' => type=\"RESTAURANTE\"
- Si el usuario dice 'bar' => type=\"BAR\"
- Si el usuario dice 'antro' o 'club' => type=\"ANTRO\"
- Si el usuario dice 'parque' => type=\"PARQUE\"
- Si el usuario dice 'plaza' => type=\"PLAZA\"
- Si el usuario dice 'mirador' o 'vista' => type=\"MIRADOR\"
- Si el usuario dice 'museo' => type=\"MUSEO\"
- Si no queda claro => type=null

- 'máximo 1 estrella' => rating_max = 1
- 'mínimo 4 estrellas' => rating_min = 4
- 'de 4 a 5 estrellas' => rating_min=4, rating_max=5
- 'menos de 300', 'máximo 300', '$300', '300 pesos' => max_price=300
- 'entre 100 y 300' => min_price=100, max_price=300

Texto del usuario: {$texto}
";

        if ($ciudadForzada) {
            $instrucciones .= "\nCiudad forzada por el sistema: {$ciudadForzada}\n";
        }

        $respuesta = $cliente->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Responde únicamente con JSON válido.'],
                ['role' => 'user', 'content' => $instrucciones],
            ],
            'temperature' => 0.2,
            'max_tokens' => 520,
        ]);

        // ✅ FIX: compat OBJETO / ARRAY
        $crudo = '{}';
        if (is_object($respuesta) && isset($respuesta->choices[0]->message->content)) {
            $crudo = (string) $respuesta->choices[0]->message->content;
        } elseif (is_array($respuesta) && isset($respuesta['choices'][0]['message']['content'])) {
            $crudo = (string) $respuesta['choices'][0]['message']['content'];
        }

        // Limpieza fences
        $crudo = trim($crudo);
        $crudo = preg_replace('/^```json\s*/i', '', $crudo);
        $crudo = preg_replace('/^```\s*/', '', $crudo);
        $crudo = preg_replace('/```$/', '', $crudo);
        $crudo = trim($crudo);

        // recorte seguro {...}
        $inicio = strpos($crudo, '{');
        $fin = strrpos($crudo, '}');
        if ($inicio !== false && $fin !== false && $fin > $inicio) {
            $crudo = substr($crudo, $inicio, $fin - $inicio + 1);
        } else {
            $crudo = '{}';
        }

        $prefs = json_decode($crudo, true);
        if (!is_array($prefs)) $prefs = [];

        // ✅ asegurar keys
        $prefs = array_merge([
            'type' => null,
            'city' => null,
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
        ], $prefs);

        // =========================================================
        // ✅ POST-PROCESO (PRECISIÓN)
        // =========================================================

        // ---------- TYPE: normalización fuerte a tus enums ----------
        $typeMap = [
            'cafe' => 'CAFETERIA',
            'cafeteria' => 'CAFETERIA',
            'coffee' => 'CAFETERIA',
            'bar' => 'BAR',
            'antro' => 'ANTRO',
            'club' => 'ANTRO',
            'comida' => 'RESTAURANTE',
            'restaurante' => 'RESTAURANTE',
            'restaurant' => 'RESTAURANTE',
            'parque' => 'PARQUE',
            'plaza' => 'PLAZA',
            'mirador' => 'MIRADOR',
            'vista' => 'MIRADOR',
            'museo' => 'MUSEO',
            'otro' => 'OTRO',
        ];

        if (is_string($prefs['type'])) {
            $t = trim($prefs['type']);
            $tU = mb_strtoupper($t);
            $tL = mb_strtolower($t);

            if (in_array($tU, $tiposValidos, true)) $prefs['type'] = $tU;
            elseif (isset($typeMap[$tL])) $prefs['type'] = $typeMap[$tL];
            else $prefs['type'] = null;
        } else {
            $prefs['type'] = null;
        }

        // Heurística directa por texto
        if (!$prefs['type']) {
            if (preg_match('/\b(caf[eé]|cafeter[ií]a|coffee)\b/u', $textoL)) $prefs['type'] = 'CAFETERIA';
            elseif (preg_match('/\b(restaurante|comida|restaurant)\b/u', $textoL)) $prefs['type'] = 'RESTAURANTE';
            elseif (preg_match('/\b(antro|club|discoteca)\b/u', $textoL)) $prefs['type'] = 'ANTRO';
            elseif (preg_match('/\b(bar)\b/u', $textoL)) $prefs['type'] = 'BAR';
            elseif (preg_match('/\b(parque)\b/u', $textoL)) $prefs['type'] = 'PARQUE';
            elseif (preg_match('/\b(plaza)\b/u', $textoL)) $prefs['type'] = 'PLAZA';
            elseif (preg_match('/\b(mirador|vista)\b/u', $textoL)) $prefs['type'] = 'MIRADOR';
            elseif (preg_match('/\b(museo)\b/u', $textoL)) $prefs['type'] = 'MUSEO';
        }

        // ---------- CIUDAD ----------
        if (is_string($prefs['city'])) {
            $c = trim($prefs['city']);
            if (mb_strtolower($c) === 'cdmx') $prefs['city'] = 'CDMX';
        }
        if ($ciudadForzada) $prefs['city'] = $ciudadForzada;

        // ---------- ESTRELLAS ----------
        if (preg_match('/\b(máximo|maximo|max|no\s*m[aá]s\s*de)\s*(de\s*)?([1-5])\s*estrellas?\b/u', $textoL, $m)) {
            $prefs['rating_max'] = (int)$m[3];
        }
        if (preg_match('/\b(mínimo|minimo|min|al\s*menos)\s*(de\s*)?([1-5])\s*estrellas?\b/u', $textoL, $m)) {
            $prefs['rating_min'] = (int)$m[3];
        }
        if (preg_match('/\bde\s*([1-5])\s*a\s*([1-5])\s*estrellas?\b/u', $textoL, $m)) {
            $a = (int)$m[1]; $b = (int)$m[2];
            $prefs['rating_min'] = min($a, $b);
            $prefs['rating_max'] = max($a, $b);
        }

        // ---------- PRECIOS (más completo) ----------
        // $300
        if (preg_match('/\$\s*([0-9]{2,6})/u', $texto, $m)) {
            $prefs['max_price'] = (int)$m[1];
        }

        // 300 pesos / 300 mxn
        if (preg_match('/\b([0-9]{2,6})\s*(pesos|mxn)\b/iu', $texto, $m)) {
            $prefs['max_price'] = (int)$m[1];
        }

        // menos de 300 / máximo 300 / hasta 300
        if (preg_match('/\b(menos\s*de|máximo|maximo|max|hasta)\s*\$?\s*([0-9]{2,6})\b/u', $textoL, $m)) {
            $prefs['max_price'] = (int)$m[2];
        }

        // entre 100 y 300
        if (preg_match('/\bentre\s*\$?\s*([0-9]{2,6})\s*y\s*\$?\s*([0-9]{2,6})\b/u', $textoL, $m)) {
            $a = (int)$m[1]; $b = (int)$m[2];
            $prefs['min_price'] = min($a, $b);
            $prefs['max_price'] = max($a, $b);
        }

        // ✅ Intención “barato/económico/lo más barato”
        $wantsCheap = (bool) preg_match('/\b(barat[oa]s?|barat[ií]sim[oa]s?|econ[oó]mic[oa]s?|accesible|ahorrar|low\s*cost|m[aá]s\s*barat[oa]|lo\s*m[aá]s\s*barat[oa])\b/u', $textoL);
        $wantsExpensive = (bool) preg_match('/\b(caro|car[ií]simo|costoso|de\s*lujo|premium|fine\s*dining)\b/u', $textoL);

        if ($wantsCheap) {
            $prefs['price'] = 'bajo';
            // Si no dieron rango ni número, ponemos un default para que el filtro “pegue”
            if ($prefs['max_price'] === null && $prefs['min_price'] === null) {
                $prefs['max_price'] = self::DEFAULT_CHEAP_MAX;
            }
            // prioridad útil para ranking
            $prefs['priorities'] = array_values(array_unique(array_merge((array)$prefs['priorities'], ['cheapest'])));
        }

        if ($wantsExpensive) {
            $prefs['price'] = 'alto';
            if ($prefs['min_price'] === null && $prefs['max_price'] === null) {
                $prefs['min_price'] = self::DEFAULT_EXPENSIVE_MIN;
            }
            $prefs['priorities'] = array_values(array_unique(array_merge((array)$prefs['priorities'], ['premium'])));
        }

        // Si hay números explícitos, no dejes que “bajo/alto” los contradiga
        if ($prefs['min_price'] !== null || $prefs['max_price'] !== null) {
            // price se mantiene como etiqueta, pero ya tienes límites reales
        }

        // ---------- normaliza arrays ----------
        if (!is_array($prefs['features'])) $prefs['features'] = [];
        if (!is_array($prefs['avoid'])) $prefs['avoid'] = [];
        if (!is_array($prefs['priorities'])) $prefs['priorities'] = [];

        return $prefs;
    }
}
