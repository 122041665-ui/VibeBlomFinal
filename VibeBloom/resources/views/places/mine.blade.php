<x-app-layout>

    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800 backdrop-blur";

        $btnPrimary = "px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnGhost = "px-6 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition
                     active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnDanger = "px-4 py-2.5 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-700 dark:text-red-300 font-semibold rounded-xl shadow-sm
                      transition active:scale-[0.99] border border-red-100 dark:border-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";

        $hint = "text-sm text-gray-600 dark:text-slate-400 mt-1";
        $title = "text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight";

        $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold
                 border border-blue-100 dark:border-blue-500/20";

        $typeIcons = [
            'RESTAURANTE' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 3v9M10 3v9M7 7h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M14 3v8.5a3 3 0 0 0 6 0V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>',
            'CAFETERIA' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4.5 9h10.5v6a4 4 0 0 1-4 4H8.5a4 4 0 0 1-4-4V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M15 10h2.25a2.75 2.75 0 1 1 0 5.5H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M7.5 5.5c0 1 .8 1.5.8 2.5M10.5 5.5c0 1 .8 1.5.8 2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".7"/>
            </svg>',
            'BAR' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 3h10l-1 7a4 4 0 0 1-4 3H12a4 4 0 0 1-4-3L7 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 13v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M9 20h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>',
            'ANTRO' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M7 12h10l-1 9H8l-1-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M9.25 8.5l5.5-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".75"/>
            </svg>',
            'PARQUE' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 2l4.5 7H7.5L12 2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M8 9l4 6 4-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 15v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M9 22h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>',
            'PLAZA' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 10h16v10H4V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M7 10V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M9 14h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".7"/>
            </svg>',
            'MIRADOR' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 20l6-6 4 4 7-7 1 1-8 8-4-4-5 5H3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>',
            'MUSEO' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3l9 6H3l9-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M5 10v9M9 10v9M15 10v9M19 10v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M4 19h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>',
            'OTRO' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 21a9 9 0 1 1 0-18a9 9 0 0 1 0 18Z" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 8.25v4.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 16.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>',
        ];

        $defaultPhoto = asset('images/vibebloom.png');
        $resolvePhotoUrl = function ($value) use ($defaultPhoto) {
            $value = trim((string) $value);

            if ($value === '') return $defaultPhoto;
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//') || str_starts_with($value, 'data:')) return $value;
            if (str_starts_with($value, '/storage/')) return asset(ltrim($value, '/'));
            if (str_starts_with($value, 'storage/')) return asset($value);
            if (str_starts_with($value, '/')) return $value;

            return asset('storage/' . ltrim($value, '/'));
        };
        $favoritePlaceIds = $favoritePlaceIds ?? [];
        $favoritesCount = is_array($favoritePlaceIds) ? count($favoritePlaceIds) : 0;
        $profileUser = auth()->user();
        $profileApiPlaces = collect($places ?? []);
        $profileCreatedPlaces = collect($profileCreatedPlaces ?? []);
        $profilePlaces = $profileApiPlaces->concat($profileCreatedPlaces)->values();
        $placesCount = $profilePlaces->count();
        $profileFavoritePlaces = collect($profileFavoritePlaces ?? []);

        if ($profileFavoritePlaces->isEmpty()) {
            $profileFavoritePlaces = $profilePlaces
            ->filter(function ($place) use ($favoritePlaceIds) {
                $placeId = is_array($place) ? ($place['id'] ?? null) : ($place->id ?? null);
                return $placeId && is_array($favoritePlaceIds) && in_array((int) $placeId, $favoritePlaceIds, true);
            })
            ->values();
        }
        $profileSubmissions = collect($profileSubmissions ?? []);
        $profileMemories = collect($profileMemories ?? []);
        $profileFollowers = collect($profileFollowers ?? []);
        $profileFollowing = collect($profileFollowing ?? []);
        $submissionsCount = $profileSubmissions->count();
        $memoriesCount = $profileMemories->count();
        $followersCount = $profileUser?->followers()->count() ?? $profileFollowers->count();
        $followingCount = $profileUser?->following()->count() ?? $profileFollowing->count();
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        .profile-tab-button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            border-radius: 1rem;
            border: 1px solid rgb(243 244 246);
            background: rgb(255 255 255);
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 700;
            color: rgb(55 65 81);
            transition: border-color 160ms ease, background-color 160ms ease, color 160ms ease;
        }

        .profile-tab-button:hover {
            border-color: rgb(191 219 254);
        }

        .profile-tab-button.is-active {
            border-color: rgb(219 234 254);
            background: rgb(239 246 255);
            color: rgb(29 78 216);
        }

        .dark .profile-tab-button {
            border-color: rgb(30 41 59);
            background: rgba(2, 6, 23, 0.3);
            color: rgb(226 232 240);
        }

        .dark .profile-tab-button:hover {
            border-color: rgba(59, 130, 246, 0.3);
        }

        .dark .profile-tab-button.is-active {
            border-color: rgba(59, 130, 246, 0.2);
            background: rgba(59, 130, 246, 0.1);
            color: rgb(147 197 253);
        }
    </style>

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }}" data-profile-tabs>
            <div class="grid grid-cols-1 lg:grid-cols-[300px_minmax(0,1fr)] gap-6 items-start">
                <aside class="space-y-4">
                    <section class="{{ $card }} overflow-hidden p-0">
                        <div class="h-20 bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500"></div>
                        <div class="px-5 pb-5">
                            <img
                                src="{{ $profileUser?->display_photo_url }}"
                                alt="{{ $profileUser?->name ?? 'Usuario' }}"
                                class="-mt-8 h-16 w-16 rounded-2xl object-cover border-4 border-white dark:border-slate-900 shadow-md bg-white"
                            >

                            <h1 class="mt-3 text-xl font-extrabold text-gray-900 dark:text-slate-100 leading-tight break-words">
                                {{ $profileUser?->name ?? 'Usuario' }}
                            </h1>

                            <p class="mt-1 text-sm text-gray-600 dark:text-slate-400 break-words">
                                {{ $profileUser?->email ?? 'Sin correo registrado' }}
                            </p>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-gray-100 dark:border-slate-800 p-3 min-w-0">
                                    <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $placesCount }}</p>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Lugares</p>
                                </div>

                                <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-gray-100 dark:border-slate-800 p-3 min-w-0">
                                    <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $favoritesCount }}</p>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Favoritos</p>
                                </div>

                                <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-gray-100 dark:border-slate-800 p-3 min-w-0">
                                    <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $followersCount }}</p>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Seguidores</p>
                                </div>

                                <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-gray-100 dark:border-slate-800 p-3 min-w-0">
                                    <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $followingCount }}</p>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Siguiendo</p>
                                </div>
                            </div>

                            <a href="{{ route('profile.show') }}" class="mt-4 box-border w-full inline-flex items-center justify-center rounded-xl border border-blue-100 dark:border-slate-700 bg-blue-50 dark:bg-slate-800 px-4 py-2.5 text-center text-sm font-semibold leading-tight text-blue-700 dark:text-blue-400 shadow-sm transition hover:bg-blue-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">
                                Configuración de perfil
                            </a>
                        </div>
                    </section>

                    <section class="{{ $card }} space-y-3">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-gray-500 dark:text-slate-400">Secciones</p>

                        <button
                            type="button"
                            data-profile-tab-button="places"
                            aria-selected="true"
                            class="profile-tab-button is-active"
                            role="tab">
                            Mis lugares
                            <span class="shrink-0">{{ $placesCount }}</span>
                        </button>

                        <button
                            type="button"
                            data-profile-tab-button="favorites"
                            aria-selected="false"
                            class="profile-tab-button"
                            role="tab">
                            Mis favoritos
                            <span class="shrink-0">{{ $favoritesCount }}</span>
                        </button>

                        <button
                            type="button"
                            data-profile-tab-button="approvals"
                            aria-selected="false"
                            class="profile-tab-button"
                            role="tab">
                            Mis aprobaciones
                            <span class="shrink-0">{{ $submissionsCount }}</span>
                        </button>

                        <button
                            type="button"
                            data-profile-tab-button="memories"
                            aria-selected="false"
                            class="profile-tab-button"
                            role="tab">
                            Recuerdos
                            <span class="shrink-0">{{ $memoriesCount }}</span>
                        </button>

                        <button
                            type="button"
                            data-profile-tab-button="network"
                            aria-selected="false"
                            class="profile-tab-button"
                            role="tab">
                            Comunidad
                            <span class="shrink-0">{{ $followersCount + $followingCount }}</span>
                        </button>
                    </section>
                </aside>

                <main class="min-w-0">
                    <section id="mis-lugares" class="space-y-5" data-profile-panel="places" role="tabpanel">
                        <div class="{{ $card }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                    Publicaciones
                                </div>
                                <h2 class="mt-3 text-2xl font-extrabold text-gray-900 dark:text-slate-100">Mis lugares</h2>
                                <p class="{{ $hint }}">Lugares que has creado desde el formulario de VibeBloom.</p>
                            </div>

                            <a href="{{ route('places.create') }}" class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2 shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Agregar un lugar
                            </a>
                        </div>

                        @if ($profilePlaces->isEmpty())
                            <div class="{{ $card }} text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" opacity=".5"/>
                                    </svg>
                                </div>

                                <p class="text-gray-900 dark:text-slate-100 text-2xl font-extrabold">
                                    Aún no has subido ningún lugar
                                </p>
                                <p class="mt-2 max-w-xl mx-auto text-sm leading-6 text-gray-600 dark:text-slate-400">
                                    Crea tu primer lugar y aquí aparecerá con sus fotos, datos y estado de revisión.
                                </p>

                                <div class="mt-6 flex items-center justify-center">
                                    <a href="{{ route('places.create') }}" class="{{ $btnPrimary }}">
                                        Agregar un lugar
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                @foreach ($profilePlaces as $place)
                                    @php
                                        $isSubmission = is_object($place) && $place instanceof \App\Models\PlaceSubmission;
                                        $placeId = is_array($place) ? ($place['id'] ?? null) : ($place->id ?? null);
                                        $placeName = is_array($place) ? ($place['name'] ?? 'Sin nombre') : ($place->name ?? 'Sin nombre');
                                        $placeCity = is_array($place) ? ($place['city'] ?? 'Sin ciudad') : ($place->city ?? 'Sin ciudad');
                                        $placeType = is_array($place) ? ($place['type'] ?? 'OTRO') : ($place->type ?? 'OTRO');
                                        $placeRating = is_array($place) ? ($place['rating'] ?? 0) : ($place->rating ?? 0);
                                        $placePrice = is_array($place) ? ($place['price'] ?? 0) : ($place->price ?? 0);

                                        $placePhoto = is_array($place) ? ($place['photo'] ?? null) : ($place->photo ?? null);
                                        $placePhotoUrl = is_array($place) ? ($place['photo_url'] ?? null) : ($place->photo_url ?? null);
                                        $placePhotosUrls = is_array($place) ? ($place['photos_urls'] ?? null) : ($place->photos_urls ?? null);
                                        $placePhotos = is_array($place) ? ($place['photos'] ?? null) : ($place->photos ?? null);
                                        $placeStatus = $isSubmission
                                            ? ($place->status ?? 'pending')
                                            : (is_array($place) ? ($place['status'] ?? 'published') : ($place->status ?? 'published'));

                                        $allPhotos = [];

                                        $main = $resolvePhotoUrl($placePhotoUrl ?: $placePhoto);

                                        if ($main) {
                                            $allPhotos[] = $main;
                                        }

                                        $extras = [];
                                        if (!empty($placePhotosUrls) && is_array($placePhotosUrls)) {
                                            $extras = $placePhotosUrls;
                                        } elseif ($placePhotos instanceof \Illuminate\Support\Collection) {
                                            $extras = $placePhotos
                                                ->pluck('path')
                                                ->filter()
                                                ->map(fn ($p) => $resolvePhotoUrl($p))
                                                ->all();
                                        } elseif (is_array($placePhotos)) {
                                            $extras = array_map(fn ($p) => $resolvePhotoUrl($p), $placePhotos);
                                        }

                                        if (is_array($extras)) {
                                            foreach ($extras as $url) {
                                                if (!empty($url)) {
                                                    $allPhotos[] = $url;
                                                }
                                            }
                                        }

                                        $allPhotos = array_values(array_unique($allPhotos));
                                        $allPhotos = array_slice($allPhotos, 0, 3);

                                        $countPhotos = count($allPhotos);
                                        $initialPhoto = $countPhotos ? $allPhotos[0] : $defaultPhoto;

                                        $typeRaw = trim((string) ($placeType ?? 'OTRO'));
                                        if ($typeRaw === '') {
                                            $typeRaw = 'OTRO';
                                        }

                                        $typeKey = mb_strtoupper($typeRaw, 'UTF-8');
                                        $typeKey = strtr($typeKey, [
                                            'Á' => 'A',
                                            'É' => 'E',
                                            'Í' => 'I',
                                            'Ó' => 'O',
                                            'Ú' => 'U',
                                            'Ü' => 'U',
                                            'Ñ' => 'N',
                                        ]);
                                        $typeKey = preg_replace('/\s+/', ' ', $typeKey);

                                        $aliases = [
                                            'RESTAURANT' => 'RESTAURANTE',
                                            'RESTAURANTES' => 'RESTAURANTE',
                                            'CAFE' => 'CAFETERIA',
                                            'CAFÉ' => 'CAFETERIA',
                                            'CAFETERIAS' => 'CAFETERIA',
                                            'DISCOTECA' => 'ANTRO',
                                            'CLUB' => 'ANTRO',
                                            'MUSEOS' => 'MUSEO',
                                            'MIRADORES' => 'MIRADOR',
                                            'PLAZAS' => 'PLAZA',
                                            'PARQUES' => 'PARQUE',
                                        ];

                                        if (isset($aliases[$typeKey])) {
                                            $typeKey = $aliases[$typeKey];
                                        }

                                        $typeIcon = $typeIcons[$typeKey] ?? $typeIcons['OTRO'];
                                        $typeLabel = $typeRaw ?: 'Sin tipo';

                                        $isFavorite = false;
                                        if (auth()->check() && is_array($favoritePlaceIds) && $placeId) {
                                            $isFavorite = in_array((int) $placeId, $favoritePlaceIds, true);
                                        }

                                        $rating = (int) ($placeRating ?? 0);
                                        $rating = max(0, min(5, $rating));

                                        $statusKey = strtolower((string) $placeStatus);
                                        $statusLabel = match ($statusKey) {
                                            'approved', 'aprobado' => 'Aprobado',
                                            'rejected', 'rechazado' => 'Rechazado',
                                            'published', 'publicado' => 'Publicado',
                                            default => 'Pendiente',
                                        };
                                        $statusClass = match ($statusKey) {
                                            'approved', 'aprobado' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
                                            'published', 'publicado' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20',
                                            'rejected', 'rechazado' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-500/10 dark:text-red-300 dark:border-red-500/20',
                                            default => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20',
                                        };
                                    @endphp

                                    @if($placeId)
                                        <article class="group relative">
                                            <span class="pointer-events-none absolute inset-0 z-10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                                <span class="absolute inset-0 bg-gradient-to-t from-blue-600/10 via-transparent to-transparent rounded-2xl"></span>
                                            </span>

                                            <div class="absolute top-3 right-3 z-20">
                                                <span class="inline-flex items-center justify-center rounded-full border px-3 py-1 text-xs font-bold shadow-sm {{ $statusClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>

                                            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm transition-all duration-300 ease-out overflow-hidden border border-gray-100 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30">
                                                @if ($countPhotos > 1)
                                                    <div class="absolute top-3 left-3 z-20">
                                                        <div class="relative photo-dd" data-place="{{ $placeId }}">
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-slate-900/90 backdrop-blur border border-gray-200 dark:border-slate-700 shadow-sm text-xs font-semibold text-gray-800 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-800 transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                                data-toggle
                                                                onclick="event.preventDefault(); event.stopPropagation();"
                                                            >
                                                                <svg class="w-4 h-4 text-gray-700 dark:text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M8 7l1.2-2h5.6L16 7h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h3Z"
                                                                        stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                    <path d="M12 17a4 4 0 1 0 0-8a4 4 0 0 0 0 8Z"
                                                                        stroke="currentColor" stroke-width="1.8"/>
                                                                </svg>
                                                                Fotos
                                                                <span class="text-gray-500 dark:text-slate-400 font-medium" data-counter>(1/{{ $countPhotos }})</span>
                                                                <svg class="w-4 h-4 text-gray-600 dark:text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>

                                                            <div class="hidden absolute mt-2 w-56 rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg overflow-hidden"
                                                                data-menu>
                                                                <div class="p-3 grid grid-cols-3 gap-2">
                                                                    @foreach ($allPhotos as $idx => $url)
                                                                        <button type="button"
                                                                                class="group rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                                                data-photo="{{ $url }}"
                                                                                data-index="{{ $idx + 1 }}"
                                                                                onclick="event.preventDefault(); event.stopPropagation();">
                                                                            <img src="{{ $url }}"
                                                                                class="w-full h-14 object-cover group-hover:scale-[1.03] transition"
                                                                                alt="Miniatura {{ $idx + 1 }} de {{ $placeName }}"
                                                                                onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';">
                                                                        </button>
                                                                    @endforeach
                                                                </div>

                                                                <div class="px-3 pb-3">
                                                                    <p class="text-[11px] text-gray-500 dark:text-slate-400">Selecciona una miniatura para cambiar la portada.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <a href="{{ $isSubmission ? route('place-submissions.show', $placeId) : route('places.show', $placeId) }}" class="block">
                                                    <div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-slate-800">
                                                        <img src="{{ $initialPhoto }}"
                                                            class="h-48 w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                                            data-main-photo="{{ $placeId }}"
                                                            alt="Foto de {{ $placeName }}"
                                                            onerror="this.onerror=null; this.src='{{ $defaultPhoto }}';">
                                                    </div>
                                                </a>

                                                <div class="p-5 space-y-3">
                                                    <div>
                                                        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-slate-100 transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                                            {{ $placeName }}
                                                        </h2>
                                                        <p class="text-gray-600 dark:text-slate-400 text-sm mt-1">{{ $placeCity }}</p>
                                                    </div>

                                                    <div class="flex items-center justify-between gap-3">
                                                        <span class="{{ $pill }}">
                                                            {!! $typeIcon !!}
                                                            {{ $typeLabel }}
                                                        </span>

                                                        <div class="flex items-center gap-1" title="{{ $rating }}/5">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <svg class="w-5 h-5 {{ $i <= $rating ? 'text-yellow-500' : 'text-gray-300 dark:text-slate-600' }}"
                                                                    viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                                </svg>
                                                            @endfor
                                                            <span class="text-xs text-gray-500 dark:text-slate-400 ml-1">{{ $rating }}/5</span>
                                                        </div>
                                                    </div>

                                                    <div class="pt-1">
                                                        <p class="{{ $hint }}">Precio aprox. por persona</p>
                                                        <p class="text-gray-900 dark:text-slate-100 font-bold text-lg">
                                                            MXN ${{ number_format((float) $placePrice, 2) }}
                                                        </p>
                                                    </div>

                                                    <div class="flex justify-end gap-3 pt-2">
                                                        @if ($isSubmission)
                                                            <a href="{{ route('place-submissions.show', $placeId) }}" class="{{ $btnGhost }}">
                                                                Ver detalle
                                                            </a>
                                                        @else
                                                            <a href="{{ route('places.edit', $placeId) }}" class="{{ $btnGhost }}">
                                                                Editar
                                                            </a>
                                                        @endif

                                                        <form action="{{ $isSubmission ? route('place-submissions.destroy', $placeId) : route('places.destroy', $placeId) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('¿Seguro que deseas eliminar este lugar?');">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit" class="{{ $btnDanger }}">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </section>

                    <section class="space-y-5 hidden" data-profile-panel="favorites" role="tabpanel">
                        <div class="{{ $card }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                    Colección guardada
                                </div>
                                <h2 class="mt-3 text-2xl font-extrabold text-gray-900 dark:text-slate-100">Mis favoritos</h2>
                                <p class="{{ $hint }}">Lugares que guardaste para consultar después.</p>
                            </div>

                            <a href="{{ route('favorites.mine') }}" class="{{ $btnGhost }} inline-flex items-center justify-center gap-2 shrink-0">
                                Abrir vista completa
                            </a>
                        </div>

                        @if ($profileFavoritePlaces->isEmpty())
                            <div class="{{ $card }} text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 20s-7-4.4-9.3-8.5C.7 8.2 2.2 5.3 6 4.8c2-.3 3.7.7 4.7 2 1-1.3 2.7-2.3 4.7-2c3.8.5 5.3 3.4 3.3 6.7C19 15.6 12 20 12 20Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>

                                <p class="text-gray-900 dark:text-slate-100 text-2xl font-extrabold">
                                    Aún no tienes favoritos
                                </p>
                                <p class="mt-2 max-w-xl mx-auto text-sm leading-6 text-gray-600 dark:text-slate-400">
                                    Guarda lugares desde el dashboard o el mapa y aparecerán aquí.
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($profileFavoritePlaces as $place)
                                    @php
                                        $placeId = is_array($place) ? ($place['id'] ?? null) : ($place->id ?? null);
                                        $placeName = is_array($place) ? ($place['name'] ?? 'Sin nombre') : ($place->name ?? 'Sin nombre');
                                        $placeCity = is_array($place) ? ($place['city'] ?? 'Sin ciudad') : ($place->city ?? 'Sin ciudad');
                                        $placeType = is_array($place) ? ($place['type'] ?? 'OTRO') : ($place->type ?? 'OTRO');
                                        $placePhoto = is_array($place) ? ($place['photo'] ?? null) : ($place->photo ?? null);
                                        $placePhotoUrl = is_array($place) ? ($place['photo_url'] ?? null) : ($place->photo_url ?? null);
                                        $photo = $resolvePhotoUrl($placePhotoUrl ?: $placePhoto);
                                    @endphp

                                    @if ($placeId)
                                        <a href="{{ route('places.show', $placeId) }}" class="group flex gap-4 rounded-[24px] border border-gray-100 dark:border-slate-800 bg-white/92 dark:bg-slate-900/92 p-4 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:border-blue-100 dark:hover:border-blue-500/30 hover:shadow-lg">
                                            <img
                                                src="{{ $photo }}"
                                                class="h-24 w-28 shrink-0 rounded-2xl object-cover bg-slate-100 dark:bg-slate-800"
                                                alt="Foto de {{ $placeName }}"
                                                loading="lazy"
                                                onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';"
                                            >

                                            <div class="min-w-0 flex-1">
                                                <p class="text-lg font-extrabold text-gray-900 dark:text-slate-100 transition group-hover:text-blue-700 dark:group-hover:text-blue-300 truncate">
                                                    {{ $placeName }}
                                                </p>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-slate-400 truncate">
                                                    {{ $placeCity }}
                                                </p>
                                                <span class="mt-3 inline-flex items-center rounded-full border border-blue-100 dark:border-blue-500/20 bg-blue-50/80 dark:bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300">
                                                    {{ $placeType ?: 'Sin tipo' }}
                                                </span>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </section>

                    <section class="space-y-5 hidden" data-profile-panel="approvals" role="tabpanel">
                        <div class="{{ $card }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                    Solicitudes
                                </div>
                                <h2 class="mt-3 text-2xl font-extrabold text-gray-900 dark:text-slate-100">Mis aprobaciones</h2>
                                <p class="{{ $hint }}">Solicitudes pendientes de revisión enviadas por ti.</p>
                            </div>

                            <a href="{{ route('places.create') }}" class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2 shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Nueva solicitud
                            </a>
                        </div>

                        @if ($profileSubmissions->isEmpty())
                            <div class="{{ $card }} text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 12h6M12 9v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" opacity=".5"/>
                                    </svg>
                                </div>

                                <p class="text-gray-900 dark:text-slate-100 text-2xl font-extrabold">
                                    Aún no tienes solicitudes
                                </p>
                                <p class="mt-2 max-w-xl mx-auto text-sm leading-6 text-gray-600 dark:text-slate-400">
                                    Cuando propongas un lugar y siga pendiente de revisión, aparecerá aquí sin salir de tu perfil.
                                </p>

                                <div class="mt-6 flex items-center justify-center">
                                    <a href="{{ route('places.create') }}" class="{{ $btnPrimary }}">
                                        Crear primera solicitud
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                                @foreach ($profileSubmissions as $submission)
                                    @php
                                        $submissionPhoto = $submission->photos->first();
                                        $submissionPhotoUrl = $submissionPhoto?->url ?: $defaultPhoto;
                                        $submissionStatus = strtolower((string) ($submission->status ?? 'pending'));
                                        $statusLabel = match ($submissionStatus) {
                                            'approved', 'aprobado' => 'Aprobado',
                                            'rejected', 'rechazado' => 'Rechazado',
                                            default => 'Pendiente',
                                        };
                                        $statusClass = match ($submissionStatus) {
                                            'approved', 'aprobado' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
                                            'rejected', 'rechazado' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-500/10 dark:text-red-300 dark:border-red-500/20',
                                            default => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20',
                                        };
                                    @endphp

                                    <article class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                                        <div class="grid grid-cols-1 sm:grid-cols-[160px_minmax(0,1fr)]">
                                            <div class="h-44 sm:h-full min-h-[170px] bg-slate-100 dark:bg-slate-800">
                                                <img src="{{ $submissionPhotoUrl }}"
                                                    alt="Foto de {{ $submission->name }}"
                                                    class="h-full w-full object-cover"
                                                    onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';">
                                            </div>

                                            <div class="p-5 min-w-0">
                                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <h3 class="text-lg font-extrabold text-gray-900 dark:text-slate-100 truncate">
                                                            {{ $submission->name }}
                                                        </h3>
                                                        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
                                                            {{ $submission->city ?? 'Sin ciudad' }} · {{ $submission->type ?? 'Sin tipo' }}
                                                        </p>
                                                    </div>

                                                    <span class="inline-flex shrink-0 items-center justify-center rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                                        {{ $statusLabel }}
                                                    </span>
                                                </div>

                                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                                    <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-gray-100 dark:border-slate-800 p-3">
                                                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Precio</p>
                                                        <p class="mt-1 font-extrabold text-gray-900 dark:text-slate-100">MXN ${{ number_format((float) $submission->price, 2) }}</p>
                                                    </div>

                                                    <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-gray-100 dark:border-slate-800 p-3">
                                                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Fotos</p>
                                                        <p class="mt-1 font-extrabold text-gray-900 dark:text-slate-100">{{ $submission->photos->count() }}</p>
                                                    </div>
                                                </div>

                                                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <a href="{{ route('place-submissions.show', $submission) }}" class="{{ $btnGhost }} inline-flex items-center justify-center">
                                                        Ver detalle
                                                    </a>

                                                    <form action="{{ route('place-submissions.destroy', $submission) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('¿Seguro que quieres eliminar esta solicitud?');">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="{{ $btnDanger }} w-full">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="flex justify-end">
                                <a href="{{ route('place-submissions.index') }}" class="{{ $btnGhost }} inline-flex items-center justify-center">
                                    Ver mis aprobaciones
                                </a>
                            </div>
                        @endif
                    </section>

                    <section class="space-y-5 hidden" data-profile-panel="memories" role="tabpanel">
                        <div class="{{ $card }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                    Momentos
                                </div>
                                <h2 class="mt-3 text-2xl font-extrabold text-gray-900 dark:text-slate-100">Recuerdos</h2>
                                <p class="{{ $hint }}">Tus experiencias guardadas con fotos, fechas y lugares.</p>
                            </div>

                            <a href="{{ route('memories.create') }}" class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2 shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Nuevo recuerdo
                            </a>
                        </div>

                        @if ($profileMemories->isEmpty())
                            <div class="{{ $card }} text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 7h16v12H4V7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M8 7l1.2-2h5.6L16 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M12 16a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                </div>

                                <p class="text-gray-900 dark:text-slate-100 text-2xl font-extrabold">
                                    Aún no tienes recuerdos
                                </p>
                                <p class="mt-2 max-w-xl mx-auto text-sm leading-6 text-gray-600 dark:text-slate-400">
                                    Guarda tus mejores momentos para tenerlos organizados dentro de tu perfil.
                                </p>

                                <div class="mt-6 flex items-center justify-center">
                                    <a href="{{ route('memories.create') }}" class="{{ $btnPrimary }}">
                                        Crear primer recuerdo
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach ($profileMemories as $memory)
                                    @php
                                        $memoryPhoto = $memory->photos->first();
                                        $memoryPhotoUrl = $memoryPhoto?->url ?: $resolvePhotoUrl($memoryPhoto?->path);
                                        $memoryDate = $memory->memory_date ? \Carbon\Carbon::parse($memory->memory_date)->format('d M Y') : null;
                                    @endphp

                                    <article class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                                        <div class="h-44 bg-slate-100 dark:bg-slate-800">
                                            <img src="{{ $memoryPhotoUrl }}"
                                                alt="Foto de {{ $memory->title ?: 'recuerdo' }}"
                                                class="h-full w-full object-cover"
                                                onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';">
                                        </div>

                                        <div class="p-5">
                                            <h3 class="text-lg font-extrabold text-gray-900 dark:text-slate-100 line-clamp-1">
                                                {{ $memory->title ?: 'Recuerdo sin titulo' }}
                                            </h3>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @if ($memoryDate)
                                                    <span class="{{ $pill }}">{{ $memoryDate }}</span>
                                                @endif

                                                @if ($memory->location)
                                                    <span class="{{ $pill }}">{{ $memory->location }}</span>
                                                @endif
                                            </div>

                                            @if ($memory->description)
                                                <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-slate-400 line-clamp-2">
                                                    {{ $memory->description }}
                                                </p>
                                            @endif

                                            <div class="mt-5">
                                                @if ($memory->id)
                                                    <a href="{{ route('memories.edit', $memory->id) }}" class="{{ $btnGhost }} inline-flex items-center justify-center w-full">
                                                        Editar
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="flex justify-end">
                                <a href="{{ route('memories.index') }}" class="{{ $btnGhost }} inline-flex items-center justify-center">
                                    Ver todos los recuerdos
                                </a>
                            </div>
                        @endif
                    </section>

                    <section class="space-y-5 hidden" data-profile-panel="network" role="tabpanel">
                        <div class="{{ $card }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="min-w-0">
                                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M10 11a4 4 0 1 0 0-8a4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M3 21a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M19 8v6M16 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    Red VibeBloom
                                </div>
                                <h2 class="mt-3 text-2xl font-extrabold text-gray-900 dark:text-slate-100">Comunidad</h2>
                                <p class="{{ $hint }}">Encuentra personas, revisa perfiles y descubre lugares publicados por otros usuarios.</p>
                            </div>

                            <a href="{{ Route::has('users.index') ? route('users.index') : url('/usuarios') }}" class="{{ $btnPrimary }} inline-flex w-full sm:w-auto items-center justify-center gap-2 shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10 11a4 4 0 1 0 0-8a4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M3 21a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M19 8v6M16 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                Buscar usuarios
                            </a>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                            <div class="{{ $card }} space-y-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="flex items-center gap-2 text-xl font-extrabold text-gray-900 dark:text-slate-100">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M16 11a4 4 0 1 0-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    <path d="M5 21a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </span>
                                            Seguidores
                                        </h3>
                                        <p class="{{ $hint }}">{{ $followersCount }} personas siguen tu perfil.</p>
                                    </div>
                                </div>

                                @forelse ($profileFollowers as $person)
                                    <a href="{{ route('users.show', $person) }}" class="group flex items-center gap-3 rounded-2xl border border-gray-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/30 p-3 transition hover:border-blue-100 dark:hover:border-blue-500/30">
                                        <img src="{{ $person->display_photo_url }}" alt="{{ $person->name }}" class="h-11 w-11 rounded-2xl object-cover bg-white dark:bg-slate-800">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-gray-900 dark:text-slate-100 truncate transition group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ $person->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $person->email }}</p>
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-hover:text-blue-600 dark:text-slate-500 dark:group-hover:text-blue-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 18l6-6l-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 p-5 text-sm text-gray-600 dark:text-slate-400">
                                        Todavía no tienes seguidores. Comparte tus lugares para que más personas descubran tu perfil.
                                    </div>
                                @endforelse
                            </div>

                            <div class="{{ $card }} space-y-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="flex items-center gap-2 text-xl font-extrabold text-gray-900 dark:text-slate-100">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M7 12l3 3l7-7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 21a9 9 0 1 0-8.2-5.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </span>
                                            Seguidos
                                        </h3>
                                        <p class="{{ $hint }}">Sigues a {{ $followingCount }} personas.</p>
                                    </div>
                                </div>

                                @forelse ($profileFollowing as $person)
                                    <a href="{{ route('users.show', $person) }}" class="group flex items-center gap-3 rounded-2xl border border-gray-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/30 p-3 transition hover:border-blue-100 dark:hover:border-blue-500/30">
                                        <img src="{{ $person->display_photo_url }}" alt="{{ $person->name }}" class="h-11 w-11 rounded-2xl object-cover bg-white dark:bg-slate-800">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-gray-900 dark:text-slate-100 truncate transition group-hover:text-blue-700 dark:group-hover:text-blue-300">{{ $person->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $person->email }}</p>
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-hover:text-blue-600 dark:text-slate-500 dark:group-hover:text-blue-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 18l6-6l-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 p-5 text-sm text-gray-600 dark:text-slate-400">
                                        Aún no sigues a nadie. Empieza buscando usuarios con gustos parecidos.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>
                </main>
            </div>

        </div>
    </div>

    <a href="{{ url('/ai/voz') }}"
       class="fixed bottom-6 right-6 z-50 group"
       aria-label="Abrir Vibe IA"
       title="Vibe IA">
        <span class="absolute -inset-1 rounded-2xl bg-blue-600/20 blur-lg opacity-0 group-hover:opacity-100 transition"></span>
        <span class="relative inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-blue-600 text-white shadow-lg hover:bg-blue-700 active:scale-[0.98] transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 13.25a2.25 2.25 0 1 0 0-4.5a2.25 2.25 0 0 0 0 4.5Z" fill="currentColor"/>
            </svg>
            <span class="font-semibold">Vibe IA</span>
        </span>
    </a>

    <script>
        function initProfileTabs() {
            const root = document.querySelector('[data-profile-tabs]');
            if (!root) return;
            if (root.dataset.profileTabsReady === 'true') return;
            root.dataset.profileTabsReady = 'true';

            const buttons = Array.from(root.querySelectorAll('[data-profile-tab-button]'));
            const panels = Array.from(root.querySelectorAll('[data-profile-panel]'));

            function showProfileTab(tab) {
                buttons.forEach(button => {
                    const isActive = button.dataset.profileTabButton === tab;
                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach(panel => {
                    panel.classList.toggle('hidden', panel.dataset.profilePanel !== tab);
                });
            }

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    showProfileTab(button.dataset.profileTabButton);
                });
            });

            showProfileTab('places');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProfileTabs);
        } else {
            initProfileTabs();
        }

        document.addEventListener('livewire:navigated', initProfileTabs);

        (function () {
            const dropdowns = document.querySelectorAll('.photo-dd');

            function closeAll(except = null) {
                dropdowns.forEach(dd => {
                    const menu = dd.querySelector('[data-menu]');
                    if (!menu) return;
                    if (except && dd === except) return;
                    menu.classList.add('hidden');
                });
            }

            dropdowns.forEach(dd => {
                const toggle = dd.querySelector('[data-toggle]');
                const menu = dd.querySelector('[data-menu]');
                const placeId = dd.dataset.place;
                const mainImg = document.querySelector(`[data-main-photo="${placeId}"]`);
                const counter = dd.querySelector('[data-counter]');

                if (!toggle || !menu || !mainImg) return;

                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const isHidden = menu.classList.contains('hidden');
                    closeAll(dd);

                    if (isHidden) {
                        menu.classList.remove('hidden');
                    } else {
                        menu.classList.add('hidden');
                    }
                });

                menu.querySelectorAll('[data-photo]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const url = btn.dataset.photo;
                        const idx = btn.dataset.index;

                        if (url) {
                            mainImg.src = url;
                        }

                        if (counter) {
                            const total = (counter.textContent.match(/\/(\d+)/) || [])[1] || '';
                            counter.textContent = `(${idx}/${total})`;
                        }

                        menu.classList.add('hidden');
                    });
                });
            });

            document.addEventListener('click', () => closeAll());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeAll();
                }
            });
        })();
    </script>

    <div class="h-20 sm:h-24"></div>

</x-app-layout>
