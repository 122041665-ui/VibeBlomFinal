<x-app-layout>

    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white dark:bg-slate-900 shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800";

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

        $defaultPhoto = asset('images/default.jpg');
        $favoritePlaceIds = $favoritePlaceIds ?? [];
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.10),transparent_55%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.10),transparent_55%)]"></div>

        <div class="{{ $container }}">

            <div class="mb-6">
                <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur shadow-sm p-5 sm:p-6 lg:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                Colección personal
                            </div>

                            <h1 class="{{ $title }} mt-4">Mis lugares</h1>
                            <p class="{{ $hint }}">Administra tus lugares, fotos y detalles.</p>
                        </div>

                        <a href="{{ route('places.create') }}" class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2 shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Agregar un lugar
                        </a>
                    </div>
                </div>
            </div>

            @if ($places->isEmpty())
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
                        Crea tu primer lugar y empieza a organizar tu colección con fotos, detalles y accesos rápidos.
                    </p>

                    <div class="mt-6 flex items-center justify-center">
                        <a href="{{ route('places.create') }}" class="{{ $btnPrimary }}">
                            Agregar un lugar
                        </a>
                    </div>
                </div>
            @else
                <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300 w-fit">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                        {{ $places->count() }} {{ $places->count() === 1 ? 'lugar registrado' : 'lugares registrados' }}
                    </div>

                    <div class="text-xs text-gray-500 dark:text-slate-400">
                        Gestiona tus lugares desde cada tarjeta
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($places as $place)
                        @php
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

                            $allPhotos = [];

                            $main = !empty($placePhotoUrl)
                                ? $placePhotoUrl
                                : (!empty($placePhoto) ? asset('storage/' . ltrim($placePhoto, '/')) : null);

                            if ($main) {
                                $allPhotos[] = $main;
                            }

                            $extras = [];
                            if (!empty($placePhotosUrls) && is_array($placePhotosUrls)) {
                                $extras = $placePhotosUrls;
                            } elseif (is_array($placePhotos)) {
                                $extras = array_map(fn ($p) => asset('storage/' . ltrim($p, '/')), $placePhotos);
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
                        @endphp

                        @if($placeId)
                            <article class="group relative">
                                <span class="pointer-events-none absolute inset-0 z-10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <span class="absolute inset-0 bg-gradient-to-t from-blue-600/10 via-transparent to-transparent rounded-2xl"></span>
                                </span>

                                <div class="absolute top-3 right-3 z-20">
                                    <form action="{{ route('favorite.toggle', $placeId) }}"
                                          method="POST"
                                          onclick="event.stopPropagation();">
                                        @csrf

                                        <button type="submit"
                                                class="group/heart relative p-2.5 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur border border-gray-200 dark:border-slate-700 shadow-md hover:shadow-lg hover:scale-[1.04] active:scale-[0.96] transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                                                aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">

                                            <span class="pointer-events-none absolute -inset-1 rounded-full {{ $isFavorite ? 'bg-blue-500/15' : 'bg-blue-600/0' }} opacity-0 group-hover/heart:opacity-100 transition"></span>

                                            <svg class="relative w-7 h-7 transition-colors duration-200 {{ $isFavorite ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-slate-400 group-hover/heart:text-blue-500 dark:group-hover/heart:text-blue-400' }}"
                                                 viewBox="0 0 24 24"
                                                 aria-hidden="true"
                                                 @if($isFavorite) fill="currentColor" @else fill="none" @endif
                                                 stroke="currentColor"
                                                 stroke-width="1.9"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path d="M12 20s-7-4.4-9.3-8.5C.7 8.2 2.2 5.3 6 4.8c2-.3 3.7.7 4.7 2 1-1.3 2.7-2.3 4.7-2c3.8.5 5.3 3.4 3.3 6.7C19 15.6 12 20 12 20Z"/>
                                            </svg>

                                            <span class="pointer-events-none absolute inset-0 rounded-full opacity-0 group-active/heart:opacity-100 group-active/heart:animate-ping bg-red-500/20"></span>
                                        </button>
                                    </form>
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

                                    <a href="{{ route('places.show', $placeId) }}" class="block">
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
                                            <a href="{{ route('places.edit', $placeId) }}" class="{{ $btnGhost }}">
                                                Editar
                                            </a>

                                            <form action="{{ route('places.destroy', $placeId) }}"
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