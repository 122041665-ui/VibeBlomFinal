<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6";
        $card = "bg-white dark:bg-slate-900 shadow rounded-2xl p-6 border border-gray-100 dark:border-slate-800";
        $hint = "text-xs text-gray-500 dark:text-slate-400";

        $btnPrimary = "px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-5 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20";

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
        $defaultPhoto = asset('images/default.jpg');

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

    @guest
        <style>
            nav {
                display: none !important;
            }
        </style>
    @endguest

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
        @guest
            <section class="relative overflow-hidden border-b border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-950/70 backdrop-blur">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-20 left-[-4rem] h-56 w-56 rounded-full bg-blue-100/60 blur-3xl dark:bg-blue-500/10"></div>
                    <div class="absolute top-10 right-[-3rem] h-64 w-64 rounded-full bg-sky-100/60 blur-3xl dark:bg-sky-500/10"></div>
                </div>

                <div class="relative max-w-7xl mx-auto px-6 py-6 flex items-center justify-between gap-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/vibebloom.png') }}"
                             alt="VibeBloom"
                             class="h-11 w-auto object-contain"
                             onerror="this.style.display='none'">
                        <div>
                            <p class="text-lg font-extrabold text-slate-900 dark:text-slate-100 leading-none">VibeBloom</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Descubre lugares y experiencias</p>
                        </div>
                    </a>

                    <div class="hidden sm:flex items-center gap-3">
                        <a href="{{ route('login') }}" class="{{ $btnGhost }}">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="{{ $btnPrimary }}">Crear cuenta</a>
                    </div>
                </div>
            </section>
        @endguest

        <div class="{{ $container }}">
            @guest
                <section class="relative mb-10 mt-2">
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-center">
                        <div class="xl:col-span-7">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 text-blue-700 dark:text-blue-300 text-sm font-semibold">
                                Explora antes de registrarte
                            </div>

                            <h1 class="mt-5 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 leading-tight">
                                Explora lugares con
                                <span class="text-blue-600 dark:text-blue-400">VibeBloom</span>
                            </h1>

                            <p class="mt-4 text-lg text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">
                                Conoce opciones, revisa lugares destacados y descubre nuevas experiencias.
                                Al iniciar sesión puedes acceder a más funciones y mantener una experiencia personalizada.
                            </p>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="{{ $btnPrimary }}">Iniciar sesión</a>
                                <a href="{{ route('register') }}" class="{{ $btnGhost }}">Crear cuenta</a>
                            </div>

                            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl">
                                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-4 shadow-sm">
                                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Exploración</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Explora lugares de forma única, rápida y ordenada.</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-4 shadow-sm">
                                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Más detalle con una cuenta</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Accede a información completa y seguimiento personal.</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-4 shadow-sm">
                                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Experiencia continua</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Guarda lo que más te gusta y regresa cuando quieras.</p>
                                </div>
                            </div>
                        </div>

                        <div class="xl:col-span-5">
                            <div class="relative rounded-[28px] border border-slate-200/70 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-2xl overflow-hidden">
                                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Acceso completo</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lo que obtienes al iniciar sesión</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20">
                                        Cuenta
                                    </span>
                                </div>

                                <div class="p-5 space-y-4">
                                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50/80 dark:bg-slate-800/60">
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">Ver detalles completos</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Consulta más información de cada lugar y su experiencia.</p>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50/80 dark:bg-slate-800/60">
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">Guardar favoritos</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Organiza tus opciones preferidas y vuelve a ellas fácilmente.</p>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50/80 dark:bg-slate-800/60">
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">Experiencia personalizada</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Accede a herramientas y recomendaciones dentro de tu cuenta.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <div class="mb-6 mt-2">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                        Explora lugares con <span class="text-blue-600 dark:text-blue-400">VibeBloom</span>
                    </h1>
                    <p class="text-gray-600 dark:text-slate-400 mt-1 text-lg">
                        Descubre lugares y experiencias increíbles cerca de ti.
                    </p>
                </div>
            @endguest

            @if ($places->count() > 0)
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-slate-100">
                            {{ $isAuth ? 'Lugares disponibles' : 'Explora algunos lugares' }}
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            {{ $isAuth ? 'Explora y entra al detalle de cada opción.' : 'Puedes navegar la vista general. Para abrir el detalle de un lugar, inicia sesión.' }}
                        </p>
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

                            $fastapiBase = rtrim(env('FASTAPI_URL', 'http://127.0.0.1:8010'), '/');
                            $initialPhoto = !empty($placePhotoUrl)
                                ? $placePhotoUrl
                                : (!empty($placePhoto) ? $fastapiBase . '/storage/' . ltrim($placePhoto, '/') : $defaultPhoto);

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
                               class="group relative block bg-white dark:bg-slate-900 rounded-2xl shadow transition-all duration-300 ease-out overflow-hidden border border-gray-100 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30">
                        @else
                            <a href="#"
                               data-requires-auth
                               data-href="{{ $href }}"
                               class="group relative block bg-white dark:bg-slate-900 rounded-2xl shadow transition-all duration-300 ease-out overflow-hidden border border-gray-100 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30">
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

                            <div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-slate-800">
                                <img src="{{ $initialPhoto }}"
                                     class="h-48 w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                     alt="Foto de {{ $placeName }}"
                                     data-fallback="{{ $defaultPhoto }}"
                                     onerror="this.onerror=null; this.src=this.dataset.fallback;" />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 via-transparent to-transparent"></div>
                            </div>

                            <div class="p-5 space-y-3">
                                <div>
                                    <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-slate-100 transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                        {{ $placeName }}
                                    </h2>

                                    <div class="mt-1 flex items-center gap-2 text-gray-600 dark:text-slate-400 text-sm">
                                        {!! $locIcon !!}
                                        <span>{{ $placeCity }}</span>
                                    </div>
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
                                        MXN ${{ number_format((float)$placePrice, 2) }}
                                    </p>
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
                        No se encontraron lugares
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                        Aún no hay lugares disponibles para mostrar.
                    </p>
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