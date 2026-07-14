<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-36 sm:pb-44";
        $card = "bg-white/95 dark:bg-slate-900/95 shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800";
        $hint = "text-xs text-gray-500 dark:text-slate-400";

        $btnPrimary = "px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-5 py-2.5 bg-white/90 dark:bg-slate-900 hover:bg-blue-50 dark:hover:bg-slate-800 text-blue-700 dark:text-blue-400 font-semibold rounded-2xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20 shadow-sm";

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

        $isAuth = auth()->check();
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
        $hasAnyFilter = request()->filled('buscar') || request()->filled('city') || request()->filled('type') || request()->filled('max_price');

        $typeIcons = [
            'RESTAURANTE' => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v9M10 3v9M7 7h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M14 3v8.5a3 3 0 0 0 6 0V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'CAFETERIA'   => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 9h10.5v6a4 4 0 0 1-4 4H8.5a4 4 0 0 1-4-4V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M15 10h2.25a2.75 2.75 0 1 1 0 5.5H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 5.5c0 1 .8 1.5.8 2.5M10.5 5.5c0 1 .8 1.5.8 2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".7"/></svg>',
            'BAR'         => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h10l-1 7a4 4 0 0 1-4 3H12a4 4 0 0 1-4-3L7 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 13v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 20h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'ANTRO'       => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 12h10l-1 9H8l-1-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9.25 8.5l5.5-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".75"/></svg>',
            'PARQUE'      => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2l4.5 7H7.5L12 2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9l4 6 4-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 15v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 22h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'PLAZA'       => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 10h16v10H4V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 10V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 14h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".7"/></svg>',
            'MIRADOR'     => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 20l6-6 4 4 7-7 1 1-8 8-4-4-5 5H3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'MUSEO'       => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l9 6H3l9-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M5 10v9M9 10v9M15 10v9M19 10v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 19h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'OTRO'        => '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21a9 9 0 1 1 0-18a9 9 0 0 1 0 18Z" stroke="currentColor" stroke-width="1.8"/><path d="M12 8.25v4.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 16.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>',
        ];

        $locIcon = '<svg class="w-4 h-4 text-blue-700 dark:text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-4.6 7-11a7 7 0 1 0-14 0c0 6.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 11a2 2 0 1 0 0-4a2 2 0 0 0 0 4Z" stroke="currentColor" stroke-width="1.8"/></svg>';
    @endphp

    <div class="min-h-screen vb-soft-page relative overflow-hidden">
        <div class="{{ $container }}">
            @guest
                <section class="relative mb-6 mt-2 rounded-[24px] border border-slate-200/80 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-sm overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-600 via-sky-500 to-blue-600"></div>
                    <div class="p-4 sm:p-5 lg:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-300">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                Lugares seleccionados
                            </div>

                            <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 leading-tight">
                                Encuentra tu próximo lugar en <span class="text-blue-600 dark:text-blue-400">VibeBloom</span>
                            </h1>

                            <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">
                                Explora tarjetas, compara precio, ubicación y calificación. Inicia sesión solo cuando quieras abrir el detalle completo.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 shrink-0">
                            <a href="{{ route('login') }}" class="{{ $btnPrimary }} text-center">Iniciar sesión</a>
                            <a href="{{ route('register') }}" class="{{ $btnGhost }} text-center">Crear cuenta</a>
                        </div>
                    </div>
                </section>
            @else
                <div class="mb-6 mt-2 rounded-[24px] border border-gray-100 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-sm p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                Exploración
                            </div>
                            <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                                Lugares para descubrir
                            </h1>
                        </div>

                        <p class="text-gray-600 dark:text-slate-400 text-sm sm:text-base max-w-xl leading-relaxed">
                            Compara lugares por foto, ubicación, categoría, precio y calificación.
                        </p>
                    </div>
                </div>
            @endguest

            @if ($places->count() > 0)
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-slate-100">
                            {{ $hasAnyFilter ? 'Resultados de búsqueda' : ($isAuth ? 'Lugares disponibles' : 'Explora algunos lugares') }}
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            {{ $hasAnyFilter ? 'Filtros aplicados desde la barra de búsqueda.' : ($isAuth ? 'Explora y entra al detalle de cada opción.' : 'Puedes navegar la vista general. Para abrir el detalle de un lugar, inicia sesión.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300 w-fit">
                            <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                            {{ $places->count() }} {{ $places->count() === 1 ? 'lugar' : 'lugares' }}
                        </div>

                        @if ($hasAnyFilter)
                            <a href="{{ route('places.index') }}" class="inline-flex items-center justify-center rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-slate-300 hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-400 transition">
                                Limpiar filtros
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
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

                            $rating = (int) $placeRating;
                            $rating = max(0, min(5, $rating));

                            $initialPhoto = $resolvePhotoUrl($placePhotoUrl ?: $placePhoto);

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
                                'Ñ' => 'N'
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
                            $href = $placeId ? route('places.show', ['place' => $placeId]) : '#';
                        @endphp

                        @if($isAuth)
                            <a href="{{ $href }}"
                               class="group relative flex h-full flex-col bg-white dark:bg-slate-900 rounded-[28px] shadow-sm transition-all duration-300 ease-out overflow-hidden border border-gray-100 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">
                        @else
                            <a href="#"
                               data-requires-auth
                               data-href="{{ $href }}"
                               class="group relative flex h-full flex-col bg-white dark:bg-slate-900 rounded-[28px] shadow-sm transition-all duration-300 ease-out overflow-hidden border border-gray-100 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">
                        @endif

                            @guest
                                <div class="absolute top-3 right-3 z-20">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur border border-blue-100 dark:border-blue-500/20 px-3 py-1.5 text-[11px] font-bold text-blue-700 dark:text-blue-300 shadow-sm">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 11V8a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        Acceso completo con cuenta
                                    </span>
                                </div>
                            @endguest

                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-slate-800">
                                <img src="{{ $initialPhoto }}"
                                     class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                     alt="Foto de {{ $placeName }}"
                                     data-fallback="{{ $defaultPhoto }}"
                                     onerror="this.onerror=null; this.src=this.dataset.fallback;" />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/45 via-slate-950/5 to-transparent"></div>
                            </div>

                            <div class="flex flex-1 flex-col p-5 space-y-4">
                                <div>
                                    <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-slate-100 transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                        {{ $placeName }}
                                    </h2>

                                    <div class="mt-2 flex items-center gap-2 text-gray-600 dark:text-slate-400 text-sm min-w-0">
                                        {!! $locIcon !!}
                                        <span class="truncate">{{ $placeCity }}</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3">
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

                                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-slate-800 flex items-end justify-between gap-3">
                                    <div>
                                        <p class="{{ $hint }}">Precio aprox. por persona</p>
                                        <p class="text-gray-900 dark:text-slate-100 font-extrabold text-lg">
                                            MXN ${{ number_format((float)$placePrice, 2) }}
                                        </p>
                                    </div>

                                    <span class="text-xs font-bold text-blue-700 dark:text-blue-400 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                        {{ $isAuth ? 'Ver detalle' : 'Iniciar sesión' }}
                                    </span>
                                </div>
                            </div>
                        </a>
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
                        {{ $hasAnyFilter ? 'No hay coincidencias con esos filtros' : 'No se encontraron lugares' }}
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                        {{ $hasAnyFilter ? 'Prueba con otro nombre, ciudad, tipo o precio máximo.' : 'Aún no hay lugares disponibles para mostrar.' }}
                    </p>

                    @if ($hasAnyFilter)
                        <div class="mt-5">
                            <a href="{{ route('places.index') }}" class="{{ $btnPrimary }}">
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

            <div class="h-28 sm:h-32 lg:h-40"></div>
        </div>
    </div>

    <div id="authModal" class="fixed inset-0 z-[60] hidden items-center justify-center px-6">
        <div class="absolute inset-0 bg-black/55"></div>

        <div class="relative w-full max-w-md">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900 dark:text-slate-100">Inicia sesión para continuar</h3>
                            <p class="text-gray-600 dark:text-slate-400 mt-1 text-sm leading-relaxed">
                                Al iniciar sesión podrás acceder al detalle del lugar, guardar tus preferidos y continuar con una experiencia más completa.
                            </p>
                        </div>

                        <button type="button" id="closeAuthModal"
                                class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                aria-label="Cerrar">
                            <svg class="w-5 h-5 text-gray-700 dark:text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-3">
                        <a href="{{ route('login') }}"
                           class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2">
                            Iniciar sesión
                        </a>

                        <a href="{{ route('register') }}"
                           class="{{ $btnGhost }} inline-flex items-center justify-center gap-2">
                            Crear cuenta
                        </a>

                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                            También puedes cerrar este mensaje y seguir explorando la vista general.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-center">
                <button type="button" id="continueExploring"
                        class="text-sm font-semibold text-white/90 hover:text-white underline underline-offset-4">
                    Seguir explorando
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('authModal');
            const closeBtn = document.getElementById('closeAuthModal');
            const continueBtn = document.getElementById('continueExploring');
            const isAuth = {{ $isAuth ? 'true' : 'false' }};

            function openModal() {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                if (!modal) return;
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            closeBtn?.addEventListener('click', closeModal);
            continueBtn?.addEventListener('click', closeModal);

            modal?.addEventListener('click', (e) => {
                if (e.target === modal.firstElementChild) closeModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

            document.querySelectorAll('[data-requires-auth]').forEach(el => {
                el.addEventListener('click', (e) => {
                    if (isAuth) return;
                    e.preventDefault();
                    e.stopPropagation();

                    const href = el.getAttribute('data-href') || '';
                    if (href) {
                        sessionStorage.setItem('vb_intent_href', href);
                    }

                    openModal();
                });
            });
        });
    </script>
</x-app-layout>
