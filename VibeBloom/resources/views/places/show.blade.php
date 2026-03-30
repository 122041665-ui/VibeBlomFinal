<x-app-layout>

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet" />

    @php
        $container  = "max-w-7xl mx-auto px-6 py-6 pb-36";
        $card       = "bg-white dark:bg-slate-900 shadow-sm rounded-2xl p-6 border border-gray-100 dark:border-slate-800";
        $cardSoft   = "bg-slate-50/80 dark:bg-slate-800/60 rounded-2xl border border-gray-200 dark:border-slate-700";
        $label      = "font-semibold text-gray-800 dark:text-slate-100";
        $hint       = "text-xs text-gray-500 dark:text-slate-400";
        $muted      = "text-sm text-gray-600 dark:text-slate-400";

        $fieldBase  = "w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                       text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                       focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-gray-300 dark:focus:border-slate-600 transition";

        $btnPrimary = "inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnGhost   = "inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnDanger  = "inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20
                       text-red-700 dark:text-red-300 font-semibold rounded-xl border border-red-100 dark:border-red-500/20 transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";

        $pill       = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20";

        $textarea = $fieldBase;
        $defaultPhoto = asset('images/default.jpg');
        $defaultUserPhoto = asset('images/default-user.png');

        $resolveUserPhoto = function ($user) use ($defaultUserPhoto) {
            if (!$user) return $defaultUserPhoto;

            $photo = null;

            if (is_array($user)) {
                $photo = $user['profile_photo_url']
                    ?? $user['photo_url']
                    ?? $user['profile_photo']
                    ?? $user['avatar']
                    ?? $user['photo']
                    ?? null;
            } else {
                $photo = $user->profile_photo_url
                    ?? $user->photo_url
                    ?? $user->profile_photo
                    ?? $user->avatar
                    ?? $user->photo
                    ?? null;
            }

            if (!$photo || trim((string) $photo) === '') {
                return $defaultUserPhoto;
            }

            $photo = trim((string) $photo);

            if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, '/')) {
                return $photo;
            }

            return asset('storage/' . ltrim($photo, '/'));
        };

        $resolveUserName = function ($user, $fallback = 'Usuario no disponible') {
            if (!$user) return $fallback;

            if (is_array($user)) {
                return $user['name'] ?? $fallback;
            }

            return $user->name ?? $fallback;
        };

        $resolveHumanDate = function ($value) {
            if (!$value) return 'Sin fecha';

            try {
                return \Illuminate\Support\Carbon::parse($value)->locale('es')->diffForHumans();
            } catch (\Throwable $e) {
                try {
                    return \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', (string) $value)->locale('es')->diffForHumans();
                } catch (\Throwable $e) {
                    return 'Sin fecha';
                }
            }
        };

        $resolveMonthYear = function ($value) {
            if (!$value) return 'Sin fecha';

            try {
                return \Illuminate\Support\Carbon::parse($value)->locale('es')->translatedFormat('M Y');
            } catch (\Throwable $e) {
                return 'Sin fecha';
            }
        };

        $placeId = is_array($place) ? ($place['id'] ?? null) : ($place->id ?? null);
        $placeName = is_array($place) ? ($place['name'] ?? 'Sin nombre') : ($place->name ?? 'Sin nombre');
        $placeCity = is_array($place) ? ($place['city'] ?? 'Sin ciudad') : ($place->city ?? 'Sin ciudad');
        $placeType = is_array($place) ? ($place['type'] ?? 'OTRO') : ($place->type ?? 'OTRO');
        $placeRating = is_array($place) ? ($place['rating'] ?? 0) : ($place->rating ?? 0);
        $placePrice = is_array($place) ? ($place['price'] ?? 0) : ($place->price ?? 0);
        $placeDescription = is_array($place) ? ($place['description'] ?? null) : ($place->description ?? null);
        $placeAddress = is_array($place) ? ($place['address'] ?? null) : ($place->address ?? null);

        $placeLat = is_array($place) ? ($place['lat'] ?? ($place['latitude'] ?? null)) : ($place->lat ?? ($place->latitude ?? null));
        $placeLng = is_array($place) ? ($place['lng'] ?? ($place['longitude'] ?? null)) : ($place->lng ?? ($place->longitude ?? null));

        $placePhoto = is_array($place) ? ($place['photo'] ?? null) : ($place->photo ?? null);
        $placePhotoUrl = is_array($place) ? ($place['photo_url'] ?? null) : ($place->photo_url ?? null);
        $placePhotosUrls = is_array($place) ? ($place['photos_urls'] ?? null) : ($place->photos_urls ?? null);
        $placePhotos = is_array($place) ? ($place['photos'] ?? null) : ($place->photos ?? null);

        $placeUser = is_array($place) ? ($place['user'] ?? null) : ($place->user ?? null);
        $placeReviews = is_array($place) ? ($place['reviews'] ?? []) : ($place->reviews ?? collect());

        $allPhotos = [];
        $main = !empty($placePhotoUrl)
            ? $placePhotoUrl
            : (!empty($placePhoto) ? asset('storage/' . ltrim($placePhoto, '/')) : null);

        if ($main) $allPhotos[] = $main;

        $extras = [];
        if (!empty($placePhotosUrls) && is_array($placePhotosUrls)) {
            $extras = $placePhotosUrls;
        } elseif (is_array($placePhotos)) {
            $extras = array_map(fn ($p) => asset('storage/' . ltrim($p, '/')), $placePhotos);
        }

        if (is_array($extras)) {
            foreach ($extras as $u) {
                if ($u) $allPhotos[] = $u;
            }
        }

        $allPhotos    = array_values(array_unique($allPhotos));
        $allPhotos    = array_slice($allPhotos, 0, 3);
        $countPhotos  = count($allPhotos);
        $initialPhoto = $countPhotos ? $allPhotos[0] : $defaultPhoto;

        $rating = (int) $placeRating;
        if ($rating < 0) $rating = 0;
        if ($rating > 5) $rating = 5;

        $address = trim((string)($placeAddress ?? ''));
        $address = $address !== '' ? $address : null;

        $hasCoords = is_numeric($placeLat) && is_numeric($placeLng) && $placeLat !== null && $placeLng !== null;

        $iconBase = 'w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0';

        $type = strtolower(trim((string)($placeType ?? '')));

        $typeIcons = [
            'RESTAURANTE' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 3v8.5M10 3v8.5M7 7h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M8.5 11.5v8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M15 3v8.5a3 3 0 0 0 6 0V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M18 11.5v8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>',
            'CAFETERIA' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 10h9v5a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4v-5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M14 11h2.2a2.8 2.8 0 0 1 0 5.6H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M8 5.5c0 1 .8 1.5.8 2.5M11 5.5c0 1 .8 1.5.8 2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".75"/>
                </svg>',
            'BAR' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 4h12l-4.4 5.2v4.1l2 6.7h-7.2l2-6.7V9.2L6 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>',
            'ANTRO' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M7 12h10l-1 8H8l-1-8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M9 8.5l6-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".75"/>
                </svg>',
            'PARQUE' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3l4.5 6H7.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M8 9l4 5 4-5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M12 14v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M9 21h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>',
            'PLAZA' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 10h16v9H4v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M7 10V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M9 14h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".75"/>
                </svg>',
            'MIRADOR' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 19.5h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M5 16l4-4 3 3 6-7 1 1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>',
            'MUSEO' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3l8.5 5H3.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M6 9.5v8M10 9.5v8M14 9.5v8M18 9.5v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M4.5 18.5h15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>',
            'OTRO' => '
                <svg class="'.$iconBase.'" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 8v4.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1" fill="currentColor"/>
                </svg>',
        ];

        $typeIcon = match (true) {
            str_contains($type, 'rest') || str_contains($type, 'comida') => $typeIcons['RESTAURANTE'],
            str_contains($type, 'cafe') || str_contains($type, 'caf') => $typeIcons['CAFETERIA'],
            str_contains($type, 'antro') => $typeIcons['ANTRO'],
            str_contains($type, 'bar') => $typeIcons['BAR'],
            str_contains($type, 'parque') || str_contains($type, 'natur') => $typeIcons['PARQUE'],
            str_contains($type, 'plaza') => $typeIcons['PLAZA'],
            str_contains($type, 'mirador') => $typeIcons['MIRADOR'],
            str_contains($type, 'museo') || str_contains($type, 'arte') => $typeIcons['MUSEO'],
            default => $typeIcons['OTRO'],
        };

        $publisherName = $resolveUserName($placeUser);
        $publisherPhoto = $resolveUserPhoto($placeUser);
        $publisherCreated = is_array($placeUser) ? ($placeUser['created_at'] ?? null) : ($placeUser->created_at ?? null);

        $favoritePlaceIds = $favoritePlaceIds ?? [];
        $isFavorite = $placeId ? in_array((int) $placeId, $favoritePlaceIds, true) : false;
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.12),transparent_55%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.12),transparent_55%)]"></div>

        <div class="{{ $container }} relative">

            <section class="mb-8">
                <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur shadow-sm p-5 sm:p-6 lg:p-7">
                    <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                <span class="{{ $pill }}">
                                    {!! $typeIcon !!}
                                    <span class="leading-none">{{ $placeType ?: 'Sin tipo' }}</span>
                                </span>

                                <div class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 px-3 py-1.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-500' : 'text-gray-300 dark:text-slate-600' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-xs font-semibold text-amber-700 dark:text-amber-300 ml-1">{{ $rating }}/5</span>
                                </div>

                                @if($address)
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 21s7-4.6 7-11a7 7 0 1 0-14 0c0 6.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        {{ $placeCity }}
                                    </span>
                                @endif
                            </div>

                            <h1 class="text-3xl md:text-4xl xl:text-[2.7rem] font-extrabold text-gray-900 dark:text-slate-100 leading-tight tracking-tight break-words">
                                {{ $placeName }}
                            </h1>

                            <p class="mt-3 text-base text-gray-600 dark:text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 21s7-4.6 7-11a7 7 0 1 0-14 0c0 6.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                <span class="break-words">{{ $placeCity }}</span>
                            </p>

                            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-slate-400 leading-relaxed max-w-3xl break-words">
                                {{ $placeDescription ?: 'Este lugar aún no tiene descripción registrada.' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 xl:justify-end">
                            @auth
                                @if($placeId)
                                    <form action="{{ route('favorite.toggle', ['place' => $placeId]) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-blue-50 dark:hover:bg-slate-800 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                            title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                                            aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                            @if ($isFavorite)
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M12 21s-7-4.4-9.3-8.5C.7 8.2 2.2 5.3 6 4.8c2-.3 3.7.7 4.7 2 1-1.3 2.7-2.3 4.7-2c3.8.5 5.3 3.4 3.3 6.7C19 16.6 12 21 12 21Z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 20s-7-4.4-9.3-8.5C.7 8.2 2.2 5.3 6 4.8c2-.3 3.7.7 4.7 2 1-1.3 2.7-2.3 4.7-2c3.8.5 5.3 3.4 3.3 6.7C19 15.6 12 20 12 20Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            @endif
                                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-200">
                                                {{ $isFavorite ? 'Guardado' : 'Guardar' }}
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            @endauth

                            <a href="{{ route('dashboard') }}" class="{{ $btnGhost }}">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Volver
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                <div class="xl:col-span-7 space-y-6">

                    <div class="bg-white dark:bg-slate-900 shadow-sm rounded-[28px] overflow-hidden border border-gray-100 dark:border-slate-800 relative">
                        @if ($countPhotos > 1)
                            <div class="absolute top-4 left-4 z-20">
                                <div class="relative photo-dd">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur border border-gray-200 dark:border-slate-700 shadow-sm text-xs font-semibold text-gray-800 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-800 transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                        data-toggle
                                    >
                                        <svg class="w-4 h-4 text-gray-700 dark:text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 7l1.2-2h5.6L16 7h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        Fotos
                                        <span class="text-gray-500 dark:text-slate-400 font-medium" data-counter>(1/{{ $countPhotos }})</span>
                                        <svg class="w-4 h-4 text-gray-600 dark:text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>

                                    <div class="hidden absolute mt-2 w-56 rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg overflow-hidden" data-menu>
                                        <div class="p-3 grid grid-cols-3 gap-2">
                                            @foreach ($allPhotos as $idx => $url)
                                                <button
                                                    type="button"
                                                    class="group rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                    data-photo="{{ $url }}"
                                                    data-index="{{ $idx + 1 }}"
                                                >
                                                    <img src="{{ $url }}" class="w-full h-14 object-cover group-hover:scale-[1.02] transition" alt="Miniatura {{ $idx + 1 }}">
                                                </button>
                                            @endforeach
                                        </div>

                                        <div class="px-3 pb-3">
                                            <p class="text-[11px] text-gray-500 dark:text-slate-400">Selecciona una miniatura para cambiar la foto.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <button type="button" class="block w-full" id="openPhotoModal" aria-label="Abrir foto en grande">
                            <img
                                src="{{ $initialPhoto }}"
                                class="w-full h-[19rem] sm:h-[24rem] lg:h-[30rem] object-cover"
                                alt="Imagen del lugar"
                                id="mainPhoto"
                                onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';"
                            >
                        </button>
                    </div>

                    <div class="{{ $card }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="{{ $cardSoft }} p-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Precio aproximado</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-slate-100">
                                    MXN ${{ number_format((float)($placePrice ?? 0), 2) }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Costo estimado por persona</p>
                            </div>

                            <div class="{{ $cardSoft }} p-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Resumen</p>
                                <p class="mt-2 text-sm leading-relaxed text-gray-700 dark:text-slate-300 break-words">
                                    {{ $placeDescription ?: 'Este lugar aún no tiene descripción registrada.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="{{ $card }} space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-100">Reseñas</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                    {{ is_array($placeReviews) ? count($placeReviews) : ($placeReviews?->count() ?? 0) }} reseña(s) registradas
                                </p>
                            </div>
                        </div>

                        @auth
                            @if($placeId)
                                <div class="{{ $cardSoft }} p-4 sm:p-5">
                                    <form action="{{ route('places.reviews.store', ['place' => $placeId]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <label class="{{ $label }}">Compartir experiencia</label>
                                        <textarea
                                            name="body"
                                            rows="4"
                                            class="{{ $textarea }}"
                                            placeholder="Escribe una reseña útil y clara sobre este lugar…"
                                            required
                                        >{{ old('body') }}</textarea>

                                        <div class="flex justify-end">
                                            <button class="{{ $btnPrimary }}">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 12h16M12 4v16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                                Publicar reseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        <div class="space-y-4">
                            @php
                                $reviewsList = is_array($placeReviews) ? $placeReviews : collect($placeReviews)->toArray();
                            @endphp

                            @forelse ($reviewsList as $review)
                                @php
                                    $reviewId = is_array($review) ? ($review['id'] ?? null) : ($review->id ?? null);
                                    $reviewUserId = is_array($review) ? ($review['user_id'] ?? null) : ($review->user_id ?? null);
                                    $reviewBody = is_array($review) ? ($review['body'] ?? 'Sin contenido') : ($review->body ?? 'Sin contenido');
                                    $reviewCreatedAt = is_array($review) ? ($review['created_at'] ?? null) : ($review->created_at ?? null);

                                    $reviewUser = is_array($review) ? ($review['user'] ?? null) : ($review->user ?? null);
                                    $reviewReplies = is_array($review) ? ($review['replies'] ?? []) : ($review->replies ?? collect());

                                    $reviewUserName = $resolveUserName($reviewUser, $reviewUserId ? 'Usuario #'.$reviewUserId : 'Usuario no disponible');
                                    $reviewUserPhoto = $resolveUserPhoto($reviewUser);
                                    $reviewTimeText = $resolveHumanDate($reviewCreatedAt);

                                    $repliesList = is_array($reviewReplies) ? $reviewReplies : collect($reviewReplies)->toArray();
                                @endphp

                                <article class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 sm:p-5 shadow-sm space-y-4 overflow-hidden">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                        <div class="flex items-start gap-3 min-w-0 flex-1">
                                            <img
                                                src="{{ $reviewUserPhoto }}"
                                                class="w-11 h-11 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-800"
                                                alt="Foto de {{ $reviewUserName }}"
                                                onerror="this.onerror=null;this.src='{{ $defaultUserPhoto }}';"
                                            >

                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-slate-100 break-words leading-5">
                                                    {{ $reviewUserName }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 break-words">
                                                    {{ $reviewTimeText }}
                                                </p>
                                            </div>
                                        </div>

                                        @auth
                                            @if ($reviewId && auth()->id() === $reviewUserId)
                                                <form
                                                    action="{{ route('places.reviews.destroy', ['place' => $placeId, 'review' => $reviewId]) }}"
                                                    method="POST"
                                                    class="shrink-0"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="{{ $btnDanger }} whitespace-nowrap" type="submit" onclick="return confirm('¿Eliminar tu reseña?')">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            <path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                            <path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    <div class="pl-0 sm:pl-14">
                                        <p class="text-[15px] leading-7 text-gray-800 dark:text-slate-300 break-words whitespace-pre-line">
                                            {{ $reviewBody }}
                                        </p>
                                    </div>

                                    @auth
                                        @if ($reviewId && $placeId)
                                            <div class="pl-0 sm:pl-14">
                                                <div class="{{ $cardSoft }} p-4">
                                                    <form action="{{ route('places.reviews.replies.store', ['place' => $placeId, 'review' => $reviewId]) }}" method="POST" class="space-y-3">
                                                        @csrf
                                                        <label class="text-sm font-semibold text-gray-800 dark:text-slate-100">Responder</label>
                                                        <textarea
                                                            name="body"
                                                            rows="2"
                                                            class="{{ $textarea }}"
                                                            placeholder="Escribe una respuesta clara y útil…"
                                                            required
                                                        ></textarea>

                                                        <div class="flex justify-end">
                                                            <button class="{{ $btnGhost }}" type="submit">
                                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M10 8l-4 4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M20 17v-1a4 4 0 0 0-4-4H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                                Responder
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    @endauth

                                    @if (!empty($repliesList))
                                        <div class="pl-0 sm:pl-14 space-y-3">
                                            @foreach ($repliesList as $reply)
                                                @php
                                                    $replyId = is_array($reply) ? ($reply['id'] ?? null) : ($reply->id ?? null);
                                                    $replyUserId = is_array($reply) ? ($reply['user_id'] ?? null) : ($reply->user_id ?? null);
                                                    $replyBody = is_array($reply) ? ($reply['body'] ?? 'Sin contenido') : ($reply->body ?? 'Sin contenido');
                                                    $replyCreatedAt = is_array($reply) ? ($reply['created_at'] ?? null) : ($reply->created_at ?? null);
                                                    $replyUser = is_array($reply) ? ($reply['user'] ?? null) : ($reply->user ?? null);

                                                    $replyUserName = $resolveUserName($replyUser, $replyUserId ? 'Usuario #'.$replyUserId : 'Usuario no disponible');
                                                    $replyUserPhoto = $resolveUserPhoto($replyUser);
                                                    $replyTimeText = $resolveHumanDate($replyCreatedAt);
                                                @endphp

                                                <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 p-4 space-y-3 overflow-hidden">
                                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                                        <div class="flex items-start gap-3 min-w-0 flex-1">
                                                            <img
                                                                src="{{ $replyUserPhoto }}"
                                                                class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-800"
                                                                alt="Foto de {{ $replyUserName }}"
                                                                onerror="this.onerror=null;this.src='{{ $defaultUserPhoto }}';"
                                                            >

                                                            <div class="min-w-0 flex-1">
                                                                <p class="font-semibold text-sm text-gray-900 dark:text-slate-100 break-words leading-5">
                                                                    {{ $replyUserName }}
                                                                </p>
                                                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 break-words">
                                                                    {{ $replyTimeText }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        @auth
                                                            @if ($replyId && auth()->id() === $replyUserId)
                                                                <form
                                                                    action="{{ route('places.reviews.replies.destroy', ['place' => $placeId, 'review' => $reviewId, 'reply' => $replyId]) }}"
                                                                    method="POST"
                                                                    class="shrink-0"
                                                                >
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="{{ $btnDanger }} whitespace-nowrap" type="submit" onclick="return confirm('¿Eliminar tu respuesta?')">
                                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                            <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                            <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                            <path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                                            <path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                        </svg>
                                                                        Eliminar
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>

                                                    <p class="text-sm leading-6 text-gray-700 dark:text-slate-300 break-words whitespace-pre-line">
                                                        {{ $replyBody }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-8 text-center">
                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M6 4h12a2 2 0 0 1 2 2v12l-4-2-4 2-4-2-4 2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-900 dark:text-slate-100 font-bold text-lg">Aún no hay reseñas</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Sé la primera persona en compartir su experiencia.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-5 space-y-6">

                    <div class="{{ $card }} xl:sticky xl:top-6 space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-100">Ubicación y ruta</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Consulta la ubicación y abre la navegación ampliada.</p>
                            </div>

                            @if ($hasCoords)
                                <button type="button" id="btnRoute" class="{{ $btnPrimary }} whitespace-nowrap">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 12h5l2 7 4-14 2 7h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Ver ruta
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Dirección</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-100 mt-2 leading-relaxed break-words">
                                    {{ $address ?: 'No se registró dirección.' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Estado</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-100 mt-2">
                                    {{ $hasCoords ? 'Ubicación disponible' : 'Sin coordenadas' }}
                                </p>
                            </div>
                        </div>

                        <div id="map" class="w-full h-80 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm overflow-hidden"></div>

                        @if (!$hasCoords)
                            <p class="text-sm text-gray-500 dark:text-slate-400">
                                No hay coordenadas guardadas para este lugar.
                            </p>
                        @endif
                    </div>

                    <div class="{{ $card }} flex items-center gap-4">
                        <img src="{{ $publisherPhoto }}"
                             class="w-14 h-14 rounded-full object-cover shadow-sm border border-gray-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"
                             alt="Foto de {{ $publisherName }}"
                             onerror="this.onerror=null;this.src='{{ $defaultUserPhoto }}';">

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500 dark:text-slate-400">Publicado por</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-100 break-words">
                                {{ $publisherName }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">
                                Miembro desde {{ $resolveMonthYear($publisherCreated) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="photoModal" class="fixed inset-0 bg-black/75 hidden items-center justify-center z-50 px-4 sm:px-6">
        <div class="relative w-full max-w-5xl">
            <button
                type="button"
                id="closePhotoModal"
                class="absolute -top-14 right-0 {{ $btnGhost }}"
                aria-label="Cerrar"
            >
                Cerrar ✕
            </button>

            <div class="bg-white dark:bg-slate-900 rounded-[28px] overflow-hidden shadow-2xl border border-gray-100 dark:border-slate-700">
                <img id="modalPhoto" src="{{ $initialPhoto }}" class="w-full max-h-[80vh] object-contain bg-black" alt="Foto completa">
            </div>

            @if ($countPhotos > 1)
                <div class="mt-3 flex items-center justify-center gap-2 flex-wrap">
                    @foreach ($allPhotos as $idx => $url)
                        <button
                            type="button"
                            class="rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                            data-modal-thumb="{{ $url }}"
                            aria-label="Ver foto {{ $idx + 1 }}"
                        >
                            <img src="{{ $url }}" class="w-16 h-12 object-cover" alt="Thumb {{ $idx + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div id="routeModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[60] p-3 sm:p-4">
        <div class="w-full max-w-7xl h-[92vh] bg-white dark:bg-slate-900 rounded-[28px] shadow-2xl border border-gray-100 dark:border-slate-700 overflow-hidden flex flex-col">
            <div class="px-4 sm:px-5 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl font-extrabold text-gray-900 dark:text-slate-100 break-words">Ruta hacia {{ $placeName }}</h3>
                    <p id="routeSummary" class="text-sm text-gray-500 dark:text-slate-400">Calculando ruta…</p>
                </div>

                <button type="button" id="closeRouteModal" class="{{ $btnGhost }}">
                    Cerrar
                </button>
            </div>

            <div class="flex-1 grid grid-cols-1 xl:grid-cols-12 min-h-0">
                <div class="xl:col-span-8 min-h-[320px] border-b xl:border-b-0 xl:border-r border-gray-200 dark:border-slate-700">
                    <div id="routeMap" class="w-full h-full"></div>
                </div>

                <div class="xl:col-span-4 overflow-y-auto bg-slate-50 dark:bg-slate-950/40">
                    <div class="p-4 sm:p-5 space-y-4">
                        <div class="rounded-2xl border border-blue-100 dark:border-blue-500/20 bg-gradient-to-br from-blue-50 to-white dark:from-blue-500/10 dark:to-slate-900 p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">Navegación</p>
                                    <p class="text-sm text-gray-600 dark:text-slate-300 mt-1">Sigue los pasos conforme avanzas.</p>
                                </div>

                                <button
                                    type="button"
                                    id="btnPlayRouteVoice"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-200 whitespace-nowrap"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M11 5L6.5 9H3v6h3.5L11 19V5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M15 9.5a4 4 0 0 1 0 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M17.5 7a7.5 7.5 0 0 1 0 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    Escuchar paso
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Distancia</p>
                                <p id="routeDistance" class="text-lg font-bold text-gray-900 dark:text-slate-100 mt-1">—</p>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Tiempo estimado</p>
                                <p id="routeDuration" class="text-lg font-bold text-gray-900 dark:text-slate-100 mt-1">—</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-blue-100 dark:border-blue-500/20 bg-gradient-to-br from-blue-50 to-white dark:from-blue-500/10 dark:to-slate-900 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">Indicación actual</p>
                            <p id="currentInstruction" class="text-base font-semibold text-gray-900 dark:text-slate-100 mt-2 leading-relaxed">
                                Aún no hay una ruta activa.
                            </p>
                            <p id="currentDistanceToStep" class="text-sm text-gray-500 dark:text-slate-400 mt-2">—</p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Siguiente paso</p>
                            <p id="nextInstruction" class="text-sm text-gray-700 dark:text-slate-300 mt-2 leading-relaxed">
                                Esperando ruta...
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Resumen</p>
                            <p id="routeSummaryText" class="text-sm text-gray-700 dark:text-slate-300 mt-2 leading-relaxed">
                                Aún no hay una ruta calculada.
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-slate-100">Pasos</h4>
                                <span id="routeStepsCount" class="text-xs text-gray-500 dark:text-slate-400">0 pasos</span>
                            </div>

                            <div id="routeSteps" class="space-y-3">
                                <p class="text-sm text-gray-500 dark:text-slate-400">Aún no hay ruta calculada.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

    <script>
        const MAPBOX_TOKEN = @json(config('services.mapbox.token'));
        const hasCoords = {{ $hasCoords ? 'true' : 'false' }};
        const DEST = hasCoords ? [{{ $placeLng ?? 0 }}, {{ $placeLat ?? 0 }}] : null;

        let map = null;
        let routeMap = null;
        let routeUserMarker = null;
        let routeDestMarker = null;
        let watchId = null;
        let liveSteps = [];
        let currentStepIndex = 0;
        let currentUserLngLat = null;
        let lastUserLngLat = null;
        let lastKnownHeading = 0;
        let navigationMode = false;
        let autoVoiceEnabled = true;
        let spokenStepKeys = new Set();
        let rerouteInProgress = false;
        let lastRerouteAt = 0;
        let activeRouteGeometry = null;
        let isInitialRouteLoaded = false;
        let preferredVoice = null;
        let voicesLoaded = false;

        const routeModal = document.getElementById('routeModal');
        const routeSteps = document.getElementById('routeSteps');
        const routeSummary = document.getElementById('routeSummary');
        const routeDistance = document.getElementById('routeDistance');
        const routeDuration = document.getElementById('routeDuration');
        const routeSummaryText = document.getElementById('routeSummaryText');
        const routeStepsCount = document.getElementById('routeStepsCount');
        const btnPlayRouteVoice = document.getElementById('btnPlayRouteVoice');
        const currentInstruction = document.getElementById('currentInstruction');
        const nextInstruction = document.getElementById('nextInstruction');
        const currentDistanceToStep = document.getElementById('currentDistanceToStep');

        if (typeof mapboxgl !== 'undefined' && MAPBOX_TOKEN) {
            mapboxgl.accessToken = MAPBOX_TOKEN;

            map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: hasCoords ? [{{ $placeLng ?? 0 }}, {{ $placeLat ?? 0 }}] : [-100.3899, 20.5888],
                zoom: hasCoords ? 14 : 5
            });

            if (hasCoords) {
                const popupHtml = `
                    <div style="font-family: ui-sans-serif, system-ui; font-size: 12px; line-height: 1.3;">
                        <div style="font-weight: 800; margin-bottom: 4px;">{{ addslashes($placeName) }}</div>
                        @if (!empty($address))
                            <div style="color:#374151;">{{ addslashes($address) }}</div>
                        @endif
                    </div>
                `;

                new mapboxgl.Marker()
                    .setLngLat([{{ $placeLng }}, {{ $placeLat }}])
                    .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupHtml))
                    .addTo(map);
            }

            const geolocate = new mapboxgl.GeolocateControl({
                positionOptions: { enableHighAccuracy: true },
                trackUserLocation: false,
                showUserHeading: true
            });

            map.addControl(geolocate);
        } else {
            const mapEl = document.getElementById('map');
            if (mapEl) {
                mapEl.innerHTML = `
                    <div class="w-full h-full flex items-center justify-center text-sm text-gray-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800">
                        No se pudo cargar el mapa.
                    </div>
                `;
            }
        }

        function loadPreferredVoice() {
            if (!('speechSynthesis' in window)) return;

            const voices = window.speechSynthesis.getVoices();
            if (!voices.length) return;

            preferredVoice =
                voices.find(v => v.lang?.toLowerCase() === 'es-mx' && /female|helena|paulina|monica|microsoft sabina|google español/i.test(v.name)) ||
                voices.find(v => v.lang?.toLowerCase() === 'es-mx') ||
                voices.find(v => v.lang?.toLowerCase().startsWith('es') && /female|helena|paulina|monica|sabina|google español/i.test(v.name)) ||
                voices.find(v => v.lang?.toLowerCase().startsWith('es')) ||
                null;

            voicesLoaded = true;
        }

        if ('speechSynthesis' in window) {
            loadPreferredVoice();
            window.speechSynthesis.onvoiceschanged = () => {
                loadPreferredVoice();
            };
        }

        function formatDistance(meters) {
            if (meters >= 1000) return `${(meters / 1000).toFixed(1)} km`;
            return `${Math.round(meters)} m`;
        }

        function formatDuration(seconds) {
            const mins = Math.round(seconds / 60);
            if (mins >= 60) {
                const h = Math.floor(mins / 60);
                const m = mins % 60;
                return m ? `${h} h ${m} min` : `${h} h`;
            }
            return `${mins} min`;
        }

        function stripHtml(html) {
            const temp = document.createElement('div');
            temp.innerHTML = html || '';
            return temp.textContent || temp.innerText || '';
        }

        function toRad(value) {
            return (value * Math.PI) / 180;
        }

        function toDeg(value) {
            return (value * 180) / Math.PI;
        }

        function distanceBetween(a, b) {
            const R = 6371000;
            const dLat = toRad(b[1] - a[1]);
            const dLng = toRad(b[0] - a[0]);

            const lat1 = toRad(a[1]);
            const lat2 = toRad(b[1]);

            const x = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);

            const y = 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
            return R * y;
        }

        function bearingBetween(a, b) {
            const lng1 = toRad(a[0]);
            const lat1 = toRad(a[1]);
            const lng2 = toRad(b[0]);
            const lat2 = toRad(b[1]);

            const y = Math.sin(lng2 - lng1) * Math.cos(lat2);
            const x = Math.cos(lat1) * Math.sin(lat2) -
                      Math.sin(lat1) * Math.cos(lat2) * Math.cos(lng2 - lng1);

            let brng = toDeg(Math.atan2(y, x));
            brng = (brng + 360) % 360;
            return brng;
        }

        function getStepSpeakKey(step, index) {
            return `${index}-${step?.instruction || ''}-${step?.distance || ''}`;
        }

        function stopSpeechSynthesis() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
        }

        function speakTextLive(text) {
            if (!autoVoiceEnabled || !text || !('speechSynthesis' in window)) return;

            if (!voicesLoaded) loadPreferredVoice();

            stopSpeechSynthesis();

            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = preferredVoice?.lang || 'es-MX';
            utter.rate = 1.03;
            utter.pitch = 1.2;
            utter.volume = 1;

            if (preferredVoice) utter.voice = preferredVoice;

            window.speechSynthesis.speak(utter);
        }

        function maybeSpeakCurrentStep(force = false) {
            if (!liveSteps.length) return;

            const step = liveSteps[currentStepIndex];
            if (!step) return;

            const key = getStepSpeakKey(step, currentStepIndex);
            if (!force && spokenStepKeys.has(key)) return;

            spokenStepKeys.add(key);

            const text = step.distance && step.distance !== '0 m'
                ? `${step.instruction}. En ${step.distance}.`
                : step.instruction;

            speakTextLive(text);
        }

        function openRouteModal() {
            routeModal.classList.remove('hidden');
            routeModal.classList.add('flex');

            if (!routeMap) {
                if (typeof mapboxgl === 'undefined' || !MAPBOX_TOKEN) return;

                routeMap = new mapboxgl.Map({
                    container: 'routeMap',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: DEST || [-100.3899, 20.5888],
                    zoom: DEST ? 13 : 5,
                    pitch: 0,
                    bearing: 0
                });

                routeMap.addControl(new mapboxgl.NavigationControl(), 'top-right');
            }

            setTimeout(() => {
                if (routeMap) routeMap.resize();
            }, 120);
        }

        function stopLiveTracking() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            navigationMode = false;
        }

        function closeRouteModal() {
            routeModal.classList.add('hidden');
            routeModal.classList.remove('flex');
            stopLiveTracking();
            stopSpeechSynthesis();
        }

        function updateInstructionCards() {
            if (!liveSteps.length) {
                currentInstruction.textContent = 'Aún no hay una ruta activa.';
                nextInstruction.textContent = 'Esperando ruta...';
                currentDistanceToStep.textContent = '—';
                return;
            }

            const currentStep = liveSteps[currentStepIndex];
            const nextStep = liveSteps[currentStepIndex + 1];

            currentInstruction.textContent = currentStep?.instruction || 'Continúa';
            nextInstruction.textContent = nextStep?.instruction || 'Estás cerca del destino.';

            if (currentUserLngLat && currentStep?.target) {
                const meters = distanceBetween(currentUserLngLat, currentStep.target);
                currentDistanceToStep.textContent = `Distancia al siguiente movimiento: ${formatDistance(meters)}`;
            } else {
                currentDistanceToStep.textContent = 'Calculando distancia...';
            }
        }

        function highlightCurrentStep() {
            document.querySelectorAll('[data-step-card]').forEach((card, index) => {
                if (index === currentStepIndex) {
                    card.classList.add('ring-2', 'ring-blue-200', 'border-blue-300');
                    card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    card.classList.remove('ring-2', 'ring-blue-200', 'border-blue-300');
                }
            });
        }

        function updateCameraFollow(userLngLat, heading = 0, immediate = false) {
            if (!routeMap || !navigationMode) return;

            routeMap.easeTo({
                center: userLngLat,
                zoom: 17.2,
                pitch: 62,
                bearing: heading,
                duration: immediate ? 0 : 900,
                essential: true
            });
        }

        function moveUserMarker(lngLat, heading = null) {
            currentUserLngLat = lngLat;

            if (!routeMap) return;

            if (!routeUserMarker) {
                const el = document.createElement('div');
                el.className = 'relative h-5 w-5 rounded-full border-4 border-white shadow-lg bg-gray-900';

                const pulse = document.createElement('div');
                pulse.className = 'absolute inset-[-8px] rounded-full bg-blue-500/20 animate-ping';
                el.appendChild(pulse);

                const dot = document.createElement('div');
                dot.className = 'absolute inset-0 rounded-full bg-gray-900';
                el.appendChild(dot);

                routeUserMarker = new mapboxgl.Marker({ element: el })
                    .setLngLat(lngLat)
                    .addTo(routeMap);
            } else {
                routeUserMarker.setLngLat(lngLat);
            }

            let resolvedHeading = heading;

            if ((resolvedHeading === null || Number.isNaN(resolvedHeading)) && lastUserLngLat) {
                const moved = distanceBetween(lastUserLngLat, lngLat);
                if (moved > 4) resolvedHeading = bearingBetween(lastUserLngLat, lngLat);
            }

            if (resolvedHeading !== null && !Number.isNaN(resolvedHeading)) {
                lastKnownHeading = resolvedHeading;
            }

            updateCameraFollow(lngLat, lastKnownHeading, !isInitialRouteLoaded);
            lastUserLngLat = lngLat;
            isInitialRouteLoaded = true;
        }

        function getRemainingRouteDistance() {
            if (!liveSteps.length || currentStepIndex >= liveSteps.length) return 0;

            let total = 0;

            liveSteps.forEach((step, index) => {
                if (index > currentStepIndex) total += step.rawDistance || 0;
            });

            const currentStep = liveSteps[currentStepIndex];
            if (currentUserLngLat && currentStep?.target) {
                total += distanceBetween(currentUserLngLat, currentStep.target);
            }

            return total;
        }

        function getDistanceToRouteLine(point, coords) {
            if (!coords || coords.length < 2) return Infinity;

            let min = Infinity;
            for (let i = 0; i < coords.length; i++) {
                const d = distanceBetween(point, coords[i]);
                if (d < min) min = d;
            }
            return min;
        }

        async function maybeReroute(userLngLat) {
            if (!DEST || rerouteInProgress || !activeRouteGeometry?.coordinates?.length) return;

            const now = Date.now();
            if (now - lastRerouteAt < 7000) return;

            const offRouteDistance = getDistanceToRouteLine(userLngLat, activeRouteGeometry.coordinates);
            if (offRouteDistance < 55) return;

            rerouteInProgress = true;
            lastRerouteAt = now;

            try {
                routeSummary.textContent = 'Recalculando ruta…';
                routeSummaryText.textContent = 'Se detectó un cambio en tu trayecto. Ajustando navegación.';
                await drawRouteLarge(userLngLat, DEST, true);
                moveUserMarker(userLngLat, lastKnownHeading);
            } catch (e) {
                console.error('No se pudo recalcular la ruta', e);
            } finally {
                rerouteInProgress = false;
            }
        }

        function advanceStepIfNeeded() {
            if (!liveSteps.length || !currentUserLngLat) return;

            const step = liveSteps[currentStepIndex];
            if (!step?.target) return;

            const meters = distanceBetween(currentUserLngLat, step.target);

            if (meters < 28 && currentStepIndex < liveSteps.length - 1) {
                currentStepIndex++;
                highlightCurrentStep();
                updateInstructionCards();
                maybeSpeakCurrentStep(true);
            } else {
                updateInstructionCards();

                if (meters < 80 && meters > 30) {
                    const stepKey = `${getStepSpeakKey(step, currentStepIndex)}-near`;
                    if (!spokenStepKeys.has(stepKey)) {
                        spokenStepKeys.add(stepKey);
                        speakTextLive(`${step.instruction}. En aproximadamente ${formatDistance(meters)}.`);
                    }
                }

                if (meters <= 22 && currentStepIndex === liveSteps.length - 1) {
                    const arriveKey = 'arrival-message';
                    if (!spokenStepKeys.has(arriveKey)) {
                        spokenStepKeys.add(arriveKey);
                        speakTextLive(`Has llegado a {{ addslashes($placeName) }}.`);
                    }
                }
            }

            const remainingMeters = getRemainingRouteDistance();
            if (remainingMeters > 0) {
                routeSummaryText.textContent = `Ruta activa hacia {{ addslashes($placeName) }}. Restan aproximadamente ${formatDistance(remainingMeters)} para llegar.`;
            }
        }

        function startLiveTracking() {
            stopLiveTracking();

            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización.');
                return;
            }

            navigationMode = true;

            watchId = navigator.geolocation.watchPosition(
                async (position) => {
                    const user = [position.coords.longitude, position.coords.latitude];
                    const heading = typeof position.coords.heading === 'number' && !Number.isNaN(position.coords.heading)
                        ? position.coords.heading
                        : null;

                    moveUserMarker(user, heading);
                    advanceStepIfNeeded();
                    await maybeReroute(user);
                },
                () => {
                    console.error('No se pudo actualizar la ubicación en tiempo real.');
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 1000,
                    timeout: 10000
                }
            );
        }

        async function waitForRouteMapReady() {
            if (!routeMap) return;
            if (routeMap.loaded()) return;

            await new Promise(resolve => routeMap.once('load', resolve));
        }

        async function drawRouteLarge(fromLngLat, toLngLat, isReroute = false) {
            if (!routeMap || !MAPBOX_TOKEN) return;

            await waitForRouteMapReady();

            const url =
                `https://api.mapbox.com/directions/v5/mapbox/driving/` +
                `${fromLngLat[0]},${fromLngLat[1]};${toLngLat[0]},${toLngLat[1]}` +
                `?geometries=geojson&overview=full&steps=true&language=es&banner_instructions=true&voice_instructions=true&access_token=${MAPBOX_TOKEN}`;

            const res = await fetch(url);
            const data = await res.json();

            const route = data?.routes?.[0];
            if (!route?.geometry) {
                routeSummary.textContent = 'No se pudo calcular la ruta.';
                routeSummaryText.textContent = 'No fue posible obtener indicaciones para este trayecto.';
                routeDistance.textContent = '—';
                routeDuration.textContent = '—';
                routeStepsCount.textContent = '0 pasos';
                routeSteps.innerHTML = `<p class="text-sm text-red-600 dark:text-red-400">No fue posible obtener indicaciones.</p>`;
                liveSteps = [];
                currentStepIndex = 0;
                activeRouteGeometry = null;
                updateInstructionCards();
                return;
            }

            const geojson = {
                type: 'Feature',
                properties: {},
                geometry: route.geometry
            };

            activeRouteGeometry = route.geometry;

            if (routeMap.getSource('route')) {
                routeMap.getSource('route').setData(geojson);
            } else {
                routeMap.addSource('route', { type: 'geojson', data: geojson });
                routeMap.addLayer({
                    id: 'route-line',
                    type: 'line',
                    source: 'route',
                    layout: { 'line-join': 'round', 'line-cap': 'round' },
                    paint: {
                        'line-color': '#2563eb',
                        'line-width': 6,
                        'line-opacity': 0.9
                    }
                });
            }

            if (routeDestMarker) routeDestMarker.remove();

            routeDestMarker = new mapboxgl.Marker({ color: '#2563eb' })
                .setLngLat(toLngLat)
                .addTo(routeMap);

            if (!navigationMode || !currentUserLngLat) {
                const bounds = new mapboxgl.LngLatBounds();
                bounds.extend(fromLngLat);
                bounds.extend(toLngLat);
                route.geometry.coordinates.forEach(coord => bounds.extend(coord));
                routeMap.fitBounds(bounds, { padding: 60 });
            }

            const distanceText = formatDistance(route.distance);
            const durationText = formatDuration(route.duration);

            routeSummary.textContent = `${distanceText} · ${durationText}`;
            routeDistance.textContent = distanceText;
            routeDuration.textContent = durationText;
            routeSummaryText.textContent = isReroute
                ? `Ruta actualizada hacia {{ addslashes($placeName) }}. Distancia aproximada: ${distanceText}. Tiempo estimado: ${durationText}.`
                : `La ruta estimada hacia {{ addslashes($placeName) }} tiene una distancia aproximada de ${distanceText} y un tiempo estimado de ${durationText}.`;

            const steps = route.legs?.[0]?.steps || [];
            routeStepsCount.textContent = `${steps.length} paso${steps.length === 1 ? '' : 's'}`;

            if (!steps.length) {
                liveSteps = [];
                currentStepIndex = 0;
                routeSteps.innerHTML = `<p class="text-sm text-gray-500 dark:text-slate-400">No hay pasos disponibles para esta ruta.</p>`;
                updateInstructionCards();
                return;
            }

            liveSteps = steps.map((step) => {
                const instruction = stripHtml(step.maneuver?.instruction || 'Continúa');
                const distance = formatDistance(step.distance || 0);

                let target = null;
                if (step.maneuver?.location && step.maneuver.location.length === 2) {
                    target = step.maneuver.location;
                }

                return {
                    instruction,
                    distance,
                    rawDistance: step.distance || 0,
                    target
                };
            });

            currentStepIndex = 0;
            spokenStepKeys.clear();

            routeSteps.innerHTML = liveSteps.map((step, index) => {
                return `
                    <div data-step-card class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm transition">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold shadow-sm">
                                ${index + 1}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-100 leading-relaxed">${step.instruction}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">${step.distance}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            highlightCurrentStep();
            updateInstructionCards();
        }

        function playRouteVoice() {
            const textToSpeak = currentInstruction?.textContent?.trim();

            if (!textToSpeak || textToSpeak === 'Aún no hay una ruta activa.' || textToSpeak === 'Calculando ruta...') {
                alert('Primero calcula una ruta para poder escuchar la indicación.');
                return;
            }

            speakTextLive(textToSpeak);
        }

        const btnRoute = document.getElementById('btnRoute');
        btnRoute?.addEventListener('click', () => {
            if (!DEST) return;
            if (!MAPBOX_TOKEN || typeof mapboxgl === 'undefined') {
                alert('No se pudo cargar el mapa de ruta.');
                return;
            }

            stopSpeechSynthesis();

            openRouteModal();
            routeSummary.textContent = 'Obteniendo tu ubicación…';
            routeSummaryText.textContent = 'Espera un momento mientras se calcula la ruta.';
            routeDistance.textContent = '—';
            routeDuration.textContent = '—';
            routeStepsCount.textContent = '0 pasos';
            routeSteps.innerHTML = `<p class="text-sm text-gray-500 dark:text-slate-400">Espera un momento mientras se calcula la ruta.</p>`;
            currentInstruction.textContent = 'Calculando ruta...';
            nextInstruction.textContent = 'Esperando ruta...';
            currentDistanceToStep.textContent = '—';
            spokenStepKeys.clear();
            isInitialRouteLoaded = false;
            lastUserLngLat = null;
            currentUserLngLat = null;

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const user = [position.coords.longitude, position.coords.latitude];
                    const heading = typeof position.coords.heading === 'number' && !Number.isNaN(position.coords.heading)
                        ? position.coords.heading
                        : null;

                    navigationMode = true;
                    await drawRouteLarge(user, DEST);
                    moveUserMarker(user, heading);
                    startLiveTracking();
                },
                () => {
                    routeSummary.textContent = 'No se pudo obtener tu ubicación.';
                    routeSummaryText.textContent = 'Activa la ubicación del navegador para calcular la ruta.';
                    routeSteps.innerHTML = `<p class="text-sm text-red-600 dark:text-red-400">Activa la ubicación del navegador para calcular la ruta.</p>`;
                    currentInstruction.textContent = 'No se pudo obtener tu ubicación.';
                    nextInstruction.textContent = 'Activa la ubicación del navegador.';
                    currentDistanceToStep.textContent = '—';
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        });

        btnPlayRouteVoice?.addEventListener('click', playRouteVoice);

        document.getElementById('closeRouteModal')?.addEventListener('click', closeRouteModal);
        routeModal?.addEventListener('click', (e) => {
            if (e.target === routeModal) closeRouteModal();
        });

        (function () {
            const dd = document.querySelector('.photo-dd');
            if (!dd) return;

            const toggle = dd.querySelector('[data-toggle]');
            const menu = dd.querySelector('[data-menu]');
            const mainPhoto = document.getElementById('mainPhoto');
            const modalPhoto = document.getElementById('modalPhoto');
            const counter = dd.querySelector('[data-counter]');

            function closeMenu() { menu.classList.add('hidden'); }
            function openMenu() { menu.classList.remove('hidden'); }

            toggle?.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = !menu.classList.contains('hidden');
                if (isOpen) closeMenu(); else openMenu();
            });

            menu?.querySelectorAll('[data-photo]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const url = btn.dataset.photo;
                    const idx = btn.dataset.index;

                    if (url) {
                        mainPhoto.src = url;
                        modalPhoto.src = url;
                    }

                    if (counter) {
                        const total = (counter.textContent.match(/\/(\d+)/) || [])[1] || '';
                        counter.textContent = `(${idx}/${total})`;
                    }

                    closeMenu();
                });
            });

            document.addEventListener('click', () => closeMenu());
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });
        })();

        (function () {
            const modal = document.getElementById('photoModal');
            const openBtn = document.getElementById('openPhotoModal');
            const closeBtn = document.getElementById('closePhotoModal');
            const modalPhoto = document.getElementById('modalPhoto');

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }

            openBtn?.addEventListener('click', openModal);
            closeBtn?.addEventListener('click', closeModal);

            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    closeRouteModal();
                }
            });

            document.querySelectorAll('[data-modal-thumb]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.dataset.modalThumb;
                    if (url) modalPhoto.src = url;
                });
            });
        })();
    </script>

    <div class="h-20 sm:h-24"></div>

</x-app-layout>