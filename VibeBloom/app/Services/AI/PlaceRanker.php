<?php

namespace App\Services\AI;

use App\Models\Place;
use Illuminate\Support\Facades\Schema;

class PlaceRanker
{
    /**
     * Devuelve array de items: [ ['id'=>int,'score'=>float], ... ]
     */
    public function obtenerTop(array $prefs, int $limit = 6): array
    {
        $limit = max(1, min(200, (int)$limit));

        $type = $this->normType($prefs['type'] ?? null);
        $city = $this->normStr($prefs['city'] ?? null);
        $zone = $this->normStr($prefs['zone'] ?? null);

        $minPrice  = $this->toNum($prefs['min_price'] ?? null);
        $maxPrice  = $this->toNum($prefs['max_price'] ?? null);
        $minRating = $this->toNum($prefs['rating_min'] ?? null);
        $maxRating = $this->toNum($prefs['rating_max'] ?? null);

        $features   = is_array($prefs['features'] ?? null) ? $prefs['features'] : [];
        $avoid      = is_array($prefs['avoid'] ?? null) ? $prefs['avoid'] : [];
        $priorities = is_array($prefs['priorities'] ?? null) ? $prefs['priorities'] : [];

        // ✅ intents
        $wantsCheapest = $this->hasPriority($priorities, 'cheapest'); // desde extractor
        $wantsPremium  = $this->hasPriority($priorities, 'premium');

        $hasPriceCol  = $this->hasColumn('places', 'price');
        $hasRatingCol = $this->hasColumn('places', 'rating');
        $hasZoneCol   = $this->hasColumn('places', 'zone');
        $hasTagsCol   = $this->hasColumn('places', 'tags');

        // =========
        // 1) HARD FILTERS (estrictos)
        // =========
        $q = Place::query();

        // 🔥 TYPE duro si existe
        if ($type) {
            $q->where('type', $type);
        }

        // Ciudad
        if ($city) {
            $q->where('city', 'like', "%{$city}%");
        }

        // Zona
        if ($zone && $hasZoneCol) {
            $q->where('zone', 'like', "%{$zone}%");
        }

        // Rating bounds
        if ($hasRatingCol) {
            if ($minRating !== null) $q->where('rating', '>=', $minRating);
            if ($maxRating !== null) $q->where('rating', '<=', $maxRating);
        }

        // Price bounds + “barato/económico” debe tener price (si existe la col)
        if ($hasPriceCol) {
            if ($minPrice !== null) $q->where('price', '>=', $minPrice);
            if ($maxPrice !== null) $q->where('price', '<=', $maxPrice);

            // ✅ Si pide barato/económico, no me traigas lugares sin precio (si no, se siente random)
            if ($wantsCheapest || $maxPrice !== null) {
                $q->whereNotNull('price');
            }

            // ✅ Si pide premium y hay minPrice, también exige price
            if ($wantsPremium || $minPrice !== null) {
                $q->whereNotNull('price');
            }
        }

        // ✅ Prefiltro por tags (si existe) para mejorar precisión sin romper (solo cuando el usuario pidió features/avoid)
        if ($hasTagsCol && (!empty($features) || !empty($avoid))) {
            // Suave: no lo hago hard (porque puede vaciar), solo reduce ruido
            foreach (array_slice($features, 0, 2) as $f) {
                $f = $this->normStr($f);
                if ($f) $q->where('tags', 'like', '%' . $f . '%');
            }
        }

        // ✅ Candidatos iniciales
        $candidates = $q->limit(250)->get();

        // =========
        // 2) RELAJACIÓN CONTROLADA (solo si no hay nada)
        // =========
        if ($candidates->isEmpty()) {
            $candidates = $this->fallbackCandidates(
                $prefs,
                $type,
                $city,
                $hasZoneCol,
                $hasPriceCol,
                $hasRatingCol,
                $wantsCheapest,
                $wantsPremium
            );
        }

        if ($candidates->isEmpty()) return [];

        // =========
        // 3) SCORING (sobre set coherente)
        // =========
        $scored = $candidates->map(function (Place $p) use (
            $type, $city, $zone,
            $minPrice, $maxPrice,
            $minRating, $maxRating,
            $features, $avoid, $priorities,
            $hasZoneCol, $hasPriceCol, $hasRatingCol, $hasTagsCol,
            $wantsCheapest, $wantsPremium
        ) {
            $score = 0.0;

            // TYPE
            if ($type && $p->type === $type) $score += 60; // sube para evitar mezclas

            // Ciudad (solo si se pidió)
            if ($city && stripos((string)$p->city, $city) !== false) $score += 18;

            // Zona
            if ($zone && $hasZoneCol && stripos((string)$p->zone, $zone) !== false) $score += 10;

            // Rating
            if ($hasRatingCol && $p->rating !== null) {
                $r = (float)$p->rating;
                $score += $r * 2.4; // un poco más fuerte
                if ($minRating !== null && $r >= $minRating) $score += 6;
                if ($maxRating !== null && $r <= $maxRating) $score += 3;
            }

            // Price (precisión + intención barato/premium)
            if ($hasPriceCol && $p->price !== null) {
                $price = (float)$p->price;

                // Dentro de rango = premio fuerte
                if ($minPrice !== null && $price < $minPrice) $score -= 14;
                if ($maxPrice !== null && $price > $maxPrice) $score -= 14;
                if ($minPrice !== null && $maxPrice !== null && $price >= $minPrice && $price <= $maxPrice) $score += 12;
                if ($maxPrice !== null && $price <= $maxPrice) $score += 6;

                // 🔥 “más barato / económico”: empuja hacia price bajo
                if ($wantsCheapest) {
                    // cuanto más barato, más score
                    $score += max(0, 25 - ($price / 20)); // ajustable, pero funciona bien
                }

                // “premium”: empuja a price alto
                if ($wantsPremium) {
                    $score += min(18, ($price / 60));
                }
            } else {
                // Si pidió barato/premium y el lugar no tiene precio, penaliza
                if ($wantsCheapest || $wantsPremium || $minPrice !== null || $maxPrice !== null) {
                    $score -= 18;
                }
            }

            // Tags features/avoid
            if ($hasTagsCol) {
                $tags = $this->explodeTags($p->tags);

                foreach ($features as $f) {
                    $f = mb_strtolower(trim((string)$f));
                    if ($f !== '' && in_array($f, $tags, true)) $score += 4;
                }
                foreach ($avoid as $a) {
                    $a = mb_strtolower(trim((string)$a));
                    if ($a !== '' && in_array($a, $tags, true)) $score -= 8;
                }
            }

            return [
                'id' => $p->id,
                'score' => $score,
                // ✅ tiebreakers útiles (sin afectar tu retorno final)
                '_price' => ($hasPriceCol && $p->price !== null) ? (float)$p->price : null,
                '_rating' => ($hasRatingCol && $p->rating !== null) ? (float)$p->rating : null,
            ];
        });

        // =========
        // 4) Orden final (score + cheapest/premium)
        // =========
        $sorted = $scored->sort(function ($a, $b) use ($wantsCheapest, $wantsPremium) {
            // 1) score desc
            if ($a['score'] !== $b['score']) return $b['score'] <=> $a['score'];

            // 2) si pidió cheapest: price asc (null al final)
            if ($wantsCheapest) {
                $ap = $a['_price']; $bp = $b['_price'];
                if ($ap === null && $bp !== null) return 1;
                if ($ap !== null && $bp === null) return -1;
                if ($ap !== null && $bp !== null && $ap !== $bp) return $ap <=> $bp;
            }

            // 3) si pidió premium: price desc
            if ($wantsPremium) {
                $ap = $a['_price']; $bp = $b['_price'];
                if ($ap === null && $bp !== null) return 1;
                if ($ap !== null && $bp === null) return -1;
                if ($ap !== null && $bp !== null && $ap !== $bp) return $bp <=> $ap;
            }

            // 4) rating desc
            $ar = $a['_rating']; $br = $b['_rating'];
            if ($ar === null && $br !== null) return 1;
            if ($ar !== null && $br === null) return -1;
            if ($ar !== null && $br !== null && $ar !== $br) return $br <=> $ar;

            return 0;
        });

        return $sorted
            ->take($limit)
            ->map(fn($x) => ['id' => $x['id'], 'score' => $x['score']])
            ->values()
            ->all();
    }

    /**
     * Fallback sin romper TYPE:
     * 1) sin zone
     * 2) sin rating
     * 3) sin price (pero si pidió cheapest/premium, intenta mantener price)
     * 4) último: solo type / solo city
     */
    private function fallbackCandidates(
        array $prefs,
        ?string $type,
        ?string $city,
        bool $hasZoneCol,
        bool $hasPriceCol,
        bool $hasRatingCol,
        bool $wantsCheapest,
        bool $wantsPremium
    ) {
        $zone = $this->normStr($prefs['zone'] ?? null);

        $minPrice  = $this->toNum($prefs['min_price'] ?? null);
        $maxPrice  = $this->toNum($prefs['max_price'] ?? null);
        $minRating = $this->toNum($prefs['rating_min'] ?? null);
        $maxRating = $this->toNum($prefs['rating_max'] ?? null);

        $try = function (bool $useZone, bool $usePrice, bool $useRating) use (
            $type, $city, $zone,
            $minPrice, $maxPrice, $minRating, $maxRating,
            $hasZoneCol, $hasPriceCol, $hasRatingCol,
            $wantsCheapest, $wantsPremium
        ) {
            $q = Place::query();

            if ($type) $q->where('type', $type);
            if ($city) $q->where('city', 'like', "%{$city}%");

            if ($useZone && $zone && $hasZoneCol) {
                $q->where('zone', 'like', "%{$zone}%");
            }

            if ($useRating && $hasRatingCol) {
                if ($minRating !== null) $q->where('rating', '>=', $minRating);
                if ($maxRating !== null) $q->where('rating', '<=', $maxRating);
            }

            if ($usePrice && $hasPriceCol) {
                if ($minPrice !== null) $q->where('price', '>=', $minPrice);
                if ($maxPrice !== null) $q->where('price', '<=', $maxPrice);

                // si el usuario pidió barato/premium, intenta mantener price como señal
                if ($wantsCheapest || $wantsPremium || $minPrice !== null || $maxPrice !== null) {
                    $q->whereNotNull('price');
                }
            }

            return $q->limit(250)->get();
        };

        // 1) sin zone
        $c = $try(false, true, true);
        if ($c->isNotEmpty()) return $c;

        // 2) sin zone + sin rating
        $c = $try(false, true, false);
        if ($c->isNotEmpty()) return $c;

        // 3) sin zone + sin rating + (si no pidió barato/premium) sin price
        if (!$wantsCheapest && !$wantsPremium && $minPrice === null && $maxPrice === null) {
            $c = $try(false, false, false);
            if ($c->isNotEmpty()) return $c;
        }

        // 4) último recurso: solo type / solo city
        if ($type) return Place::where('type', $type)->limit(250)->get();
        if ($city) return Place::where('city', 'like', "%{$city}%")->limit(250)->get();

        return collect();
    }

    private function hasPriority(array $priorities, string $needle): bool
    {
        $needle = mb_strtolower(trim($needle));
        foreach ($priorities as $p) {
            if (mb_strtolower(trim((string)$p)) === $needle) return true;
        }
        return false;
    }

    private function normType($t): ?string
    {
        if (!$t) return null;
        $t = mb_strtoupper(trim((string)$t));
        $valid = ['RESTAURANTE','CAFETERIA','BAR','ANTRO','PARQUE','PLAZA','MIRADOR','MUSEO','OTRO'];
        return in_array($t, $valid, true) ? $t : null;
    }

    private function normStr($v): ?string
    {
        if (!is_string($v)) return null;
        $s = trim($v);
        return $s !== '' ? $s : null;
    }

    private function toNum($v): ?float
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v)) return (float)$v;
        return null;
    }

    private function explodeTags($tags): array
    {
        if (is_array($tags)) {
            return array_values(array_filter(array_map(
                fn($x) => mb_strtolower(trim((string)$x)),
                $tags
            )));
        }

        $s = mb_strtolower((string)$tags);
        $parts = preg_split('/[,|;]/', $s) ?: [];
        return array_values(array_filter(array_map(fn($x)=>trim($x), $parts)));
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
