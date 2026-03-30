<x-app-layout>

    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";

        $card = "bg-white dark:bg-slate-900 shadow rounded-2xl p-6 border border-gray-100 dark:border-slate-800";
        $hint = "text-xs text-gray-500 dark:text-slate-400";

        $btnGhost = "px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm
                     transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold
                 border border-blue-100 dark:border-blue-500/20";

        $types = [
            'RESTAURANTE' => 'Restaurante',
            'CAFETERIA'   => 'Cafetería',
            'BAR'         => 'Bar',
            'ANTRO'       => 'Antro',
            'PARQUE'      => 'Parque',
            'PLAZA'       => 'Plaza',
            'MIRADOR'     => 'Mirador',
            'MUSEO'       => 'Museo',
            'OTRO'        => 'Otro',
        ];

        $hasAnyFilter = request()->filled('buscar') || request()->filled('city') || request()->filled('type') || request()->filled('max_price');
        $defaultPhoto = asset('images/default.jpg');

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
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
        <div class="{{ $container }}">

            <div class="mb-6 mt-2">
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                    Explora lugares con <span class="text-blue-600 dark:text-blue-400">VibeBloom</span>
                </h1>
                <p class="text-gray-600 dark:text-slate-400 mt-1 text-lg">
                    Descubre lugares y experiencias increíbles cerca de ti.
                </p>
            </div>

            @if (!empty($error))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300">
                    {{ $error }}
                </div>
            @endif

            @if ($places->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
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

                            $rating = (int) $placeRating;
                            $rating = max(0, min(5, $rating));

                            $photos = [];

                            $main = !empty($placePhotoUrl)
                                ? $placePhotoUrl
                                : (!empty($placePhoto) ? asset('storage/' . ltrim($placePhoto, '/')) : null);

                            if ($main) {
                                $photos[] = $main;
                            }

                            $extras = [];
                            if (!empty($placePhotosUrls) && is_array($placePhotosUrls)) {
                                $extras = $placePhotosUrls;
                            } elseif (is_array($placePhotos)) {
                                $extras = array_map(fn ($p) => asset('storage/' . ltrim($p, '/')), $placePhotos);
                            }

                            if (is_array($extras)) {
                                foreach ($extras as $u) {
                                    if (!empty($u)) {
                                        $photos[] = $u;
                                    }
                                }
                            }

                            $photos = array_values(array_unique($photos));
                            $photos = array_slice($photos, 0, 3);
                            $countPhotos = count($photos);
                            $initialPhoto = $countPhotos ? $photos[0] : $defaultPhoto;

                            $typeRaw = trim((string) $placeType);
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
                                'RESTAURANT'   => 'RESTAURANTE',
                                'RESTAURANTES' => 'RESTAURANTE',
                                'CAFE'         => 'CAFETERIA',
                                'CAFÉ'         => 'CAFETERIA',
                                'CAFETERIAS'   => 'CAFETERIA',
                                'DISCOTECA'    => 'ANTRO',
                                'CLUB'         => 'ANTRO',
                                'MUSEOS'       => 'MUSEO',
                                'MIRADORES'    => 'MIRADOR',
                                'PLAZAS'       => 'PLAZA',
                                'PARQUES'      => 'PARQUE',
                            ];

                            if (isset($aliases[$typeKey])) {
                                $typeKey = $aliases[$typeKey];
                            }

                            $typeLabelCard = $types[$typeKey] ?? 'Otro';
                            $typeIcon = $typeIcons[$typeKey] ?? $typeIcons['OTRO'];

                            $isFavorite = false;
                            if (auth()->check() && isset($favoritePlaceIds) && is_array($favoritePlaceIds) && $placeId) {
                                $isFavorite = in_array((int) $placeId, $favoritePlaceIds, true);
                            }
                        @endphp

                        @if($placeId)
                            <article class="group relative">
                                @auth
                                    <div class="absolute top-3 right-3 z-30">
                                        <form action="{{ route('favorite.toggle', ['place' => $placeId]) }}"
                                              method="POST"
                                              onsubmit="event.stopPropagation();">
                                            @csrf

                                            <button type="submit"
                                                    onclick="event.stopPropagation();"
                                                    class="group/heart relative p-2.5 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur
                                                           border border-gray-200 dark:border-slate-700 shadow-md
                                                           hover:shadow-lg hover:scale-[1.04] active:scale-[0.96] transition
                                                           focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                    title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                                                    aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">

                                                <span class="pointer-events-none absolute -inset-1 rounded-full
                                                             {{ $isFavorite ? 'bg-blue-500/15' : 'bg-blue-600/0' }}
                                                             opacity-0 group-hover/heart:opacity-100 transition"></span>

                                                <svg class="relative w-7 h-7 transition-colors duration-200
                                                            {{ $isFavorite ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-slate-400 group-hover/heart:text-blue-500 dark:group-hover/heart:text-blue-400' }}"
                                                     viewBox="0 0 24 24"
                                                     aria-hidden="true"
                                                     @if($isFavorite) fill="currentColor" @else fill="none" @endif
                                                     stroke="currentColor"
                                                     stroke-width="1.9"
                                                     stroke-linecap="round"
                                                     stroke-linejoin="round">
                                                    <path d="M12 20s-7-4.4-9.3-8.5C.7 8.2 2.2 5.3 6 4.8c2-.3 3.7.7 4.7 2
                                                             1-1.3 2.7-2.3 4.7-2c3.8.5 5.3 3.4 3.3 6.7C19 15.6 12 20 12 20Z"/>
                                                </svg>

                                                <span class="pointer-events-none absolute inset-0 rounded-full
                                                             opacity-0 group-active/heart:opacity-100
                                                             group-active/heart:animate-ping bg-red-500/20"></span>
                                            </button>
                                        </form>
                                    </div>
                                @endauth

                                <a href="{{ route('places.show', ['place' => $placeId]) }}"
                                   class="block relative bg-white dark:bg-slate-900 rounded-2xl shadow transition-all duration-300 ease-out
                                          overflow-hidden border border-gray-100 dark:border-slate-800
                                          hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30">

                                    @if ($countPhotos > 1)
                                        <div class="absolute top-3 left-3 z-20">
                                            <div class="relative photo-dd" data-dd="{{ $placeId }}">
                                                <button type="button"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl
                                                               bg-white/90 dark:bg-slate-900/90 backdrop-blur border border-gray-200 dark:border-slate-700 shadow-sm
                                                               text-xs font-semibold text-gray-800 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-800 transition
                                                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                        data-toggle
                                                        onclick="event.preventDefault(); event.stopPropagation();">
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
                                                        @foreach ($photos as $idx => $url)
                                                            <button type="button"
                                                                    class="group rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 transition
                                                                           focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                                                    data-photo="{{ $url }}"
                                                                    data-index="{{ $idx + 1 }}"
                                                                    onclick="event.preventDefault(); event.stopPropagation();">
                                                                <img src="{{ $url }}"
                                                                     class="w-full h-14 object-cover group-hover:scale-[1.03] transition"
                                                                     alt="Miniatura {{ $idx + 1 }} de {{ $placeName }}"
                                                                     onerror="this.src='{{ $defaultPhoto }}'">
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

                                    <div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-slate-800">
                                        <img src="{{ $initialPhoto }}"
                                             class="h-48 w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                             alt="Foto de {{ $placeName }}"
                                             data-main-photo="{{ $placeId }}"
                                             data-fallback="{{ $defaultPhoto }}"
                                             onerror="this.onerror=null; this.src=this.dataset.fallback;" />
                                    </div>

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
                                                {{ $typeLabelCard }}
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
                                    </div>
                                </a>
                            </article>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="{{ $card }} mt-6 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10 18a8 8 0 1 1 5.3-14l4.7 4.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 20l-4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 dark:text-slate-100">
                        No se encontraron lugares
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                        {{ $hasAnyFilter ? 'Prueba con otros filtros o limpia tu búsqueda.' : 'Aún no hay lugares disponibles para mostrar.' }}
                    </p>

                    @if ($hasAnyFilter)
                        <div class="mt-5">
                            <a href="{{ route('dashboard') }}" class="{{ $btnGhost }}">
                                Limpiar filtros
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-6">
                @if (method_exists($places, 'links'))
                    {{ $places->links() }}
                @endif
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
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.photo-dd').forEach(dd => {
                const toggle = dd.querySelector('[data-toggle]');
                const menu = dd.querySelector('[data-menu]');
                const placeId = dd.getAttribute('data-dd');

                const mainImg = document.querySelector(`[data-main-photo="${placeId}"]`);
                const counter = dd.querySelector('[data-counter]');

                function closeMenu() {
                    menu.classList.add('hidden');
                }

                function openMenu() {
                    menu.classList.remove('hidden');
                }

                toggle?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    document.querySelectorAll('.photo-dd [data-menu]').forEach(m => {
                        if (m !== menu) m.classList.add('hidden');
                    });

                    const isOpen = !menu.classList.contains('hidden');
                    if (isOpen) closeMenu();
                    else openMenu();
                });

                menu?.querySelectorAll('[data-photo]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const url = btn.dataset.photo;
                        const idx = btn.dataset.index;

                        if (mainImg && url) {
                            mainImg.src = url;
                        }

                        if (counter) {
                            const total = (counter.textContent.match(/\/(\d+)/) || [])[1] || '';
                            counter.textContent = `(${idx}/${total})`;
                        }

                        closeMenu();
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!dd.contains(e.target)) closeMenu();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeMenu();
                });
            });
        });
    </script>

    <div class="h-20 sm:h-24"></div>

</x-app-layout>