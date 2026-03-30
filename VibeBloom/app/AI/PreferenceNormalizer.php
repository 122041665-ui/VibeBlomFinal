<?php

namespace App\Services\AI;

class PreferenceNormalizer
{
    public function normalizar(array $prefs): array
    {
        $salida = [
            'type' => $prefs['type'] ?? null,
            'city' => $prefs['city'] ?? null,
            'zone' => $prefs['zone'] ?? null,
            'price' => $prefs['price'] ?? null,

            // ✅ restricciones duras
            'max_price'  => $prefs['max_price']  ?? $prefs['price_max'] ?? null,
            'min_price'  => $prefs['min_price']  ?? $prefs['price_min'] ?? null,
            'rating_max' => $prefs['rating_max'] ?? $prefs['max_rating'] ?? $prefs['stars_max'] ?? null,
            'rating_min' => $prefs['rating_min'] ?? $prefs['min_rating'] ?? $prefs['stars_min'] ?? null,

            'features' => $prefs['features'] ?? [],
            'avoid' => $prefs['avoid'] ?? [],
            'noise_pref' => $prefs['noise_pref'] ?? null,
            'crowd_pref' => $prefs['crowd_pref'] ?? null,
            'priorities' => $prefs['priorities'] ?? [],
        ];

        // =========================
        // ✅ Strings base
        // =========================

        // type: lo normalizamos a ENUM real (MAYÚSCULAS)
        if (is_string($salida['type'])) {
            $salida['type'] = $this->normalizeType($salida['type']);
        } else {
            $salida['type'] = null;
        }

        // price / noise_pref / crowd_pref: se quedan en bajo|medio|alto
        foreach (['price', 'noise_pref', 'crowd_pref'] as $k) {
            if (is_string($salida[$k])) $salida[$k] = strtolower(trim($salida[$k]));
        }

        // city/zone: solo trim (no lower porque en DB puede estar "CDMX")
        if (is_string($salida['city'])) $salida['city'] = trim($salida['city']);
        if (is_string($salida['zone'])) $salida['zone'] = trim($salida['zone']);

        // =========================
        // ✅ Arrays normalizados
        // =========================
        $salida['features'] = $this->normalizeStringArray($salida['features']);
        $salida['avoid'] = $this->normalizeStringArray($salida['avoid']);
        $salida['priorities'] = $this->normalizeStringArray($salida['priorities']);

        // =========================
        // ✅ Validaciones (ACTUALIZADAS)
        // =========================
        $tiposPermitidos = ['RESTAURANTE','CAFETERIA','BAR','ANTRO','PARQUE','PLAZA','MIRADOR','MUSEO','OTRO'];
        $preciosPermitidos = ['bajo', 'medio', 'alto'];
        $nivelesPermitidos = ['bajo', 'medio', 'alto'];

        if (!in_array($salida['type'], $tiposPermitidos, true)) $salida['type'] = null;
        if (!in_array($salida['price'], $preciosPermitidos, true)) $salida['price'] = null;
        if (!in_array($salida['noise_pref'], $nivelesPermitidos, true)) $salida['noise_pref'] = null;
        if (!in_array($salida['crowd_pref'], $nivelesPermitidos, true)) $salida['crowd_pref'] = null;

        // =========================
        // ✅ Normalización numérica
        // =========================
        $salida['max_price'] = $this->toNumberOrNull($salida['max_price']);
        $salida['min_price'] = $this->toNumberOrNull($salida['min_price']);

        if (!is_null($salida['max_price']) && $salida['max_price'] < 0) $salida['max_price'] = null;
        if (!is_null($salida['min_price']) && $salida['min_price'] < 0) $salida['min_price'] = null;

        // rating: 1..5
        $salida['rating_max'] = $this->toNumberOrNull($salida['rating_max']);
        $salida['rating_min'] = $this->toNumberOrNull($salida['rating_min']);

        if (!is_null($salida['rating_max'])) {
            $salida['rating_max'] = max(1, min(5, (int)round($salida['rating_max'])));
        }
        if (!is_null($salida['rating_min'])) {
            $salida['rating_min'] = max(1, min(5, (int)round($salida['rating_min'])));
        }

        // Si vienen invertidos (min>max), los acomodamos
        if (!is_null($salida['rating_min']) && !is_null($salida['rating_max']) && $salida['rating_min'] > $salida['rating_max']) {
            $tmp = $salida['rating_min'];
            $salida['rating_min'] = $salida['rating_max'];
            $salida['rating_max'] = $tmp;
        }

        // Si min_price > max_price, swap
        if (!is_null($salida['min_price']) && !is_null($salida['max_price']) && $salida['min_price'] > $salida['max_price']) {
            $tmp = $salida['min_price'];
            $salida['min_price'] = $salida['max_price'];
            $salida['max_price'] = $tmp;
        }

        return $salida;
    }

    /**
     * Normaliza "cafe/comida/bar/antro/..." a ENUM real en MAYÚSCULAS
     */
    private function normalizeType(string $type): ?string
    {
        $t = trim($type);
        if ($t === '') return null;

        $u = mb_strtoupper($t);
        $l = mb_strtolower($t);

        // ya viene en enum correcto
        $valid = ['RESTAURANTE','CAFETERIA','BAR','ANTRO','PARQUE','PLAZA','MIRADOR','MUSEO','OTRO'];
        if (in_array($u, $valid, true)) return $u;

        // mapeos comunes
        $map = [
            'cafe' => 'CAFETERIA',
            'cafeteria' => 'CAFETERIA',
            'cafetería' => 'CAFETERIA',
            'coffee' => 'CAFETERIA',

            'comida' => 'RESTAURANTE',
            'restaurante' => 'RESTAURANTE',
            'restaurant' => 'RESTAURANTE',

            'bar' => 'BAR',

            'antro' => 'ANTRO',
            'club' => 'ANTRO',
            'discoteca' => 'ANTRO',

            'parque' => 'PARQUE',
            'plaza' => 'PLAZA',
            'mirador' => 'MIRADOR',
            'vista' => 'MIRADOR',
            'museo' => 'MUSEO',
            'otro' => 'OTRO',
        ];

        return $map[$l] ?? null;
    }

    private function normalizeStringArray($arr): array
    {
        $a = (array)$arr;
        $a = array_map(fn($x) => strtolower(trim((string)$x)), $a);
        $a = array_values(array_filter($a, fn($x) => $x !== ''));
        return $a;
    }

    private function toNumberOrNull($v): ?float
    {
        if ($v === null || $v === '') return null;
        if (is_int($v) || is_float($v)) return (float)$v;

        $s = trim((string)$v);
        // quita "mxn", "$", comas
        $s = str_replace(['mxn', 'MXN', '$', ','], '', $s);
        $s = trim($s);

        if ($s === '' || !is_numeric($s)) return null;
        return (float)$s;
    }
}
