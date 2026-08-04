<x-app-layout>
    @php
        // env() puede devolver null cuando producción usa config:cache.
        $mapboxToken = (string) config('services.mapbox.token');

        $typeIcons = [
            'RESTAURANTE' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v9M10 3v9M7 7h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M14 3v8.5a3 3 0 0 0 6 0V3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'CAFETERIA' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 9h10.5v6a4 4 0 0 1-4 4H8.5a4 4 0 0 1-4-4V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M15 10h2.25a2.75 2.75 0 1 1 0 5.5H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7.5 5.5c0 1 .8 1.5.8 2.5M10.5 5.5c0 1 .8 1.5.8 2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".7"/></svg>',
            'BAR' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h10l-1 7a4 4 0 0 1-4 3H12a4 4 0 0 1-4-3L7 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 13v7M9 20h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'ANTRO' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 12h10l-1 9H8l-1-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9.25 8.5l5.5-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".75"/></svg>',
            'PARQUE' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2l4.5 7H7.5L12 2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9l4 6 4-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 15v7M9 22h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'PLAZA' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 10h16v10H4V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 10V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 14h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".7"/></svg>',
            'MIRADOR' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 20l6-6 4 4 7-7 1 1-8 8-4-4-5 5H3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'MUSEO' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l9 6H3l9-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M5 10v9M9 10v9M15 10v9M19 10v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 19h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'OTRO' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21a9 9 0 1 1 0-18a9 9 0 0 1 0 18Z" stroke="currentColor" stroke-width="1.8"/><path d="M12 8.25v4.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 16.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>',
        ];

        $labels = [
            'TODOS' => 'Todos',
            'RESTAURANTE' => 'Restaurantes',
            'CAFETERIA' => 'Cafeterías',
            'BAR' => 'Bares',
            'ANTRO' => 'Antros',
            'PARQUE' => 'Parques',
            'PLAZA' => 'Plazas',
            'MIRADOR' => 'Miradores',
            'MUSEO' => 'Museos',
            'OTRO' => 'Otros',
        ];

        $btnPrimary = "inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white/90 px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm backdrop-blur transition hover:border-blue-200 hover:text-blue-700 active:scale-[0.99] dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-200 dark:hover:border-blue-500/30 dark:hover:text-blue-300";
    @endphp

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

    <div class="relative overflow-hidden bg-white dark:bg-slate-950" style="height: calc(100vh - 4rem); min-height: 680px;">
        <div id="vbMap" class="absolute inset-0 h-full w-full bg-slate-50 dark:bg-slate-950" style="height: 100%; min-height: 680px;"></div>

        <div class="pointer-events-none absolute inset-x-0 top-0 z-10 h-56 bg-gradient-to-b from-white/90 via-white/45 to-transparent dark:from-slate-950/92 dark:via-slate-950/42 dark:to-transparent"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-44 bg-gradient-to-t from-white/88 via-white/35 to-transparent dark:from-slate-950/90 dark:via-slate-950/35 dark:to-transparent"></div>

        <section class="pointer-events-none absolute inset-x-0 top-0 z-20 px-3 py-4 sm:px-5 lg:px-6">
            <div class="mx-auto flex max-w-7xl flex-col gap-3">
                <div class="pointer-events-auto rounded-[26px] border border-white/20 bg-white/92 p-3 shadow-[0_24px_70px_rgba(15,23,42,0.22)] backdrop-blur-xl dark:border-slate-700/80 dark:bg-slate-950/92 dark:shadow-[0_24px_70px_rgba(0,0,0,0.48)]">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                Explorador VibeBloom
                            </div>
                            <h1 class="mt-2 text-xl font-extrabold leading-tight text-gray-950 sm:text-2xl dark:text-slate-100">Mapa de lugares</h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Filtra por nombre o tipo para encontrar ubicaciones creadas por la comunidad.</p>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-[minmax(220px,1fr)_auto_auto] xl:min-w-[620px]">
                            <label class="relative block">
                                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m21 21-4.35-4.35M10.75 18.5a7.75 7.75 0 1 1 0-15.5a7.75 7.75 0 0 1 0 15.5Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <input id="vbSearchInput"
                                       type="search"
                                       autocomplete="off"
                                       class="h-11 w-full rounded-2xl border border-gray-200 bg-slate-50/90 pl-11 pr-4 text-sm font-semibold text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-300 focus:bg-white focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-500/50 dark:focus:bg-slate-900 dark:focus:ring-blue-500/20"
                                       placeholder="Buscar por nombre, ciudad o tipo">
                            </label>

                            <a href="{{ route('places.create') }}" class="{{ $btnPrimary }}">
                                Crear lugar
                            </a>

                            <a href="{{ route('dashboard') }}" class="{{ $btnGhost }}">
                                Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="pointer-events-auto grid grid-cols-2 gap-2 rounded-[22px] border border-white/20 bg-white/86 p-2 shadow-lg backdrop-blur-xl dark:border-slate-700/80 dark:bg-slate-950/88 dark:shadow-[0_18px_45px_rgba(0,0,0,0.35)] sm:grid-cols-3 md:grid-cols-5 xl:grid-cols-10">
                    @foreach ($labels as $key => $label)
                        <button type="button"
                                class="vb-filter-btn inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-2xl border border-transparent px-3 py-2 text-xs font-extrabold text-gray-600 transition hover:bg-blue-50 hover:text-blue-700 dark:text-slate-300 dark:hover:bg-blue-500/10 dark:hover:text-blue-300"
                                data-type="{{ $key }}">
                            @if ($key !== 'TODOS')
                                <span class="text-blue-700 dark:text-blue-300">{!! $typeIcons[$key] !!}</span>
                            @else
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                            @endif
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="pointer-events-none absolute inset-x-0 bottom-0 z-20 px-3 pb-4 sm:px-5 lg:inset-x-auto lg:bottom-6 lg:right-6 lg:w-[380px] lg:px-0 lg:pb-0">
            <div class="pointer-events-auto max-h-[34vh] overflow-hidden rounded-[26px] border border-white/20 bg-white/94 shadow-[0_24px_70px_rgba(15,23,42,0.24)] backdrop-blur-xl dark:border-slate-700/80 dark:bg-slate-950/94 dark:shadow-[0_24px_70px_rgba(0,0,0,0.48)] lg:max-h-[58vh]">
                <div class="border-b border-gray-100 px-4 py-3 dark:border-slate-800">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-400 dark:text-slate-500">Resultados</p>
                            <p class="text-lg font-extrabold text-gray-950 dark:text-slate-100">
                                <span id="vbResultCount">0</span> lugares
                            </p>
                        </div>
                        <button type="button" id="vbFitBtn" class="{{ $btnGhost }} px-3 py-2 text-xs">
                            Ajustar mapa
                        </button>
                    </div>
                    <p id="vbActiveFilterLabel" class="mt-1 text-xs font-semibold text-blue-700 dark:text-blue-300">Mostrando todos los lugares</p>
                </div>

                <div id="vbResultsList" class="max-h-[22vh] overflow-y-auto p-2 lg:max-h-[44vh]"></div>

                <div id="vbEmptyState" class="hidden p-5 text-sm text-gray-500 dark:text-slate-400">
                    No encontramos lugares con esos filtros. Prueba con otro nombre o tipo.
                </div>
            </div>
        </aside>

        <div id="mapEmpty" class="pointer-events-none hidden absolute left-1/2 top-1/2 z-30 w-[min(92vw,420px)] -translate-x-1/2 -translate-y-1/2 rounded-[26px] border border-white/20 bg-white/94 p-6 text-center shadow-xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-950/94">
            <p class="text-lg font-extrabold text-gray-950 dark:text-slate-100">Aún no hay lugares con ubicación</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Cuando existan lugares con coordenadas, aparecerán en este mapa.</p>
        </div>

        <div id="mapError" class="hidden absolute left-1/2 top-1/2 z-30 w-[min(92vw,460px)] -translate-x-1/2 -translate-y-1/2 rounded-[26px] border border-red-200 bg-red-50 p-6 text-center text-red-800 shadow-xl dark:border-red-900/60 dark:bg-red-950/80 dark:text-red-300">
            <p class="text-lg font-extrabold">No se pudo cargar el mapa</p>
            <p class="mt-2 text-sm">Revisa el token de Mapbox en <span class="font-bold">MAPBOX_TOKEN</span>.</p>
        </div>
    </div>

    <a href="{{ url('/ai/voz') }}"
       class="fixed bottom-6 left-6 z-50 group"
       aria-label="Abrir Vibe IA"
       title="Vibe IA">
        <span class="absolute -inset-1 rounded-2xl bg-blue-600/20 blur-lg opacity-0 transition group-hover:opacity-100"></span>
        <span class="relative inline-flex items-center gap-3 rounded-2xl bg-blue-600 px-5 py-3 text-white shadow-lg transition hover:bg-blue-700 active:scale-[0.98]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 13.25a2.25 2.25 0 1 0 0-4.5a2.25 2.25 0 0 0 0 4.5Z" fill="currentColor"/>
            </svg>
            <span class="font-semibold">Vibe IA</span>
        </span>
    </a>

    <style>
        html, body {
            overscroll-behavior: none;
        }

        #vbMap,
        #vbMap .mapboxgl-canvas-container,
        #vbMap .mapboxgl-canvas {
            min-height: 680px !important;
            height: 100% !important;
            width: 100% !important;
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] {
            left: 1.5rem !important;
            right: auto !important;
        }

        @media (max-width: 640px) {
            a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] {
                left: 1rem !important;
                right: auto !important;
            }
        }

        .mapboxgl-ctrl-top-right {
            top: 162px !important;
            right: 18px !important;
        }

        @media (max-width: 1023px) {
            .mapboxgl-ctrl-top-right {
                top: 210px !important;
            }
        }

        .dark .mapboxgl-ctrl-group {
            background: rgb(15 23 42 / 0.95) !important;
            border: 1px solid rgb(51 65 85) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .25) !important;
        }

        .dark .mapboxgl-ctrl-group button span {
            filter: invert(1) opacity(.85);
        }

        .vb-filter-btn.is-active {
            background: rgb(37 99 235) !important;
            border-color: rgb(37 99 235) !important;
            color: #fff !important;
            box-shadow: 0 12px 30px rgba(37, 99, 235, .25);
        }

        .vb-filter-btn.is-active span {
            color: #fff !important;
        }

        .vb-marker {
            --pin-color: rgb(37 99 235);
            --pin-shadow: rgba(37, 99, 235, .30);
            position: relative;
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            border-radius: 18px 18px 18px 6px;
            background: #fff;
            border: 2px solid var(--pin-color);
            color: var(--pin-color);
            box-shadow: 0 16px 34px rgba(15, 23, 42, .18);
            cursor: pointer;
            transform: rotate(-45deg);
            transition: transform .14s ease, box-shadow .14s ease, background .14s ease, color .14s ease;
        }

        .vb-marker::after {
            content: "";
            position: absolute;
            inset: 5px;
            z-index: -1;
            border-radius: 14px 14px 14px 4px;
            background: color-mix(in srgb, var(--pin-color) 12%, white);
        }

        .dark .vb-marker {
            background: #fff;
            border-color: var(--pin-color);
            color: var(--pin-color);
        }

        .vb-marker:hover,
        .vb-marker.is-highlighted {
            transform: translateY(-4px) rotate(-45deg) scale(1.08);
            background: var(--pin-color);
            color: #fff;
            box-shadow: 0 20px 42px var(--pin-shadow);
        }

        .vb-marker svg {
            position: relative;
            z-index: 1;
            width: 20px;
            height: 20px;
            transform: rotate(45deg);
        }

        .vb-marker--restaurante {
            --pin-color: rgb(37 99 235);
            --pin-shadow: rgba(37, 99, 235, .34);
        }

        .vb-marker--cafeteria {
            --pin-color: rgb(14 165 233);
            --pin-shadow: rgba(14, 165, 233, .34);
        }

        .vb-marker--bar {
            --pin-color: rgb(124 58 237);
            --pin-shadow: rgba(124, 58, 237, .34);
        }

        .vb-marker--antro {
            --pin-color: rgb(219 39 119);
            --pin-shadow: rgba(219, 39, 119, .34);
        }

        .vb-marker--parque {
            --pin-color: rgb(22 163 74);
            --pin-shadow: rgba(22, 163, 74, .34);
        }

        .vb-marker--plaza {
            --pin-color: rgb(234 88 12);
            --pin-shadow: rgba(234, 88, 12, .34);
        }

        .vb-marker--mirador {
            --pin-color: rgb(13 148 136);
            --pin-shadow: rgba(13, 148, 136, .34);
        }

        .vb-marker--museo {
            --pin-color: rgb(79 70 229);
            --pin-shadow: rgba(79, 70, 229, .34);
        }

        .vb-marker--otro {
            --pin-color: rgb(71 85 105);
            --pin-shadow: rgba(71, 85, 105, .30);
        }

        .vb-popup.mapboxgl-popup {
            max-width: none !important;
        }

        .vb-popup .mapboxgl-popup-content {
            width: 300px;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(226, 232, 240, 1);
            background: #fff;
            padding: 0;
            box-shadow: 0 20px 42px rgba(15, 23, 42, .2);
        }

        .dark .vb-popup .mapboxgl-popup-content {
            border-color: rgb(51 65 85);
            background: rgb(15 23 42);
            color: rgb(226 232 240);
        }

        .vb-popup-card {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .vb-popup-card__media {
            height: 138px;
            background: rgb(241 245 249);
            overflow: hidden;
        }

        .dark .vb-popup-card__media {
            background: rgb(30 41 59);
        }

        .vb-popup-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <script>
        const VB_MAPBOX_TOKEN = @json($mapboxToken);
        const TYPE_ICONS = {
            RESTAURANTE: @json($typeIcons['RESTAURANTE']),
            CAFETERIA: @json($typeIcons['CAFETERIA']),
            BAR: @json($typeIcons['BAR']),
            ANTRO: @json($typeIcons['ANTRO']),
            PARQUE: @json($typeIcons['PARQUE']),
            PLAZA: @json($typeIcons['PLAZA']),
            MIRADOR: @json($typeIcons['MIRADOR']),
            MUSEO: @json($typeIcons['MUSEO']),
            OTRO: @json($typeIcons['OTRO']),
        };
        const TYPE_LABELS = @json($labels);

        let map = null;
        let allFeatures = [];
        let markers = [];
        let activeType = 'TODOS';
        let activeQuery = '';
        let highlightedMarkerId = null;
        let popup = null;
        let fitTimer = null;

        function isDarkMode() {
            return document.documentElement.classList.contains('dark');
        }

        function mapStyleUrl() {
            return isDarkMode()
                ? 'mapbox://styles/mapbox/dark-v11'
                : 'mapbox://styles/mapbox/light-v11';
        }

        function normalize(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();
        }

        function escapeHtml(value) {
            return String(value || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function iconKey(place) {
            const raw = normalize(place.iconKey || place.type);

            if (raw.includes('rest')) return 'RESTAURANTE';
            if (raw.includes('cafe')) return 'CAFETERIA';
            if (raw.includes('antro')) return 'ANTRO';
            if (raw.includes('bar')) return 'BAR';
            if (raw.includes('parque') || raw.includes('natur')) return 'PARQUE';
            if (raw.includes('plaza') || raw.includes('centro comercial') || raw.includes('mall')) return 'PLAZA';
            if (raw.includes('mirador')) return 'MIRADOR';
            if (raw.includes('museo') || raw.includes('arte')) return 'MUSEO';

            return 'OTRO';
        }

        function photoUrl(place) {
            const url = String(place.photo_url || '').trim();
            if (!url) return '';
            if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('/')) return url;
            if (url.startsWith('storage/')) return `/${url}`;
            return '';
        }

        function priceText(price) {
            const amount = Number(price);
            if (!Number.isFinite(amount)) return '';

            return amount.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
                maximumFractionDigits: 0
            });
        }

        function popupHtml(place) {
            const key = iconKey(place);
            const photo = photoUrl(place);
            const rating = place.rating ? Number(place.rating).toFixed(1) : null;
            const price = priceText(place.price);

            return `
                <article class="vb-popup-card">
                    <div class="vb-popup-card__media">
                        ${photo
                            ? `<img src="${escapeHtml(photo)}" alt="Foto de ${escapeHtml(place.name)}" onerror="this.style.display='none'">`
                            : `<div style="height:100%;display:grid;place-items:center;color:#64748b;font-weight:800;font-size:12px;">Sin foto</div>`
                        }
                    </div>
                    <div style="padding:14px;">
                        <div style="display:inline-flex;align-items:center;gap:7px;border-radius:999px;background:rgba(37,99,235,.08);color:#1d4ed8;padding:6px 10px;font-weight:900;font-size:12px;">
                            ${TYPE_ICONS[key] || TYPE_ICONS.OTRO}
                            ${escapeHtml(place.type || 'Lugar')}
                        </div>
                        <h3 style="margin:10px 0 0;font-size:16px;line-height:1.15;font-weight:950;color:inherit;">${escapeHtml(place.name || 'Lugar')}</h3>
                        <p style="margin:6px 0 0;color:#64748b;font-size:12px;font-weight:700;">${escapeHtml(place.city || 'Ubicación')}</p>
                        <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;color:#475569;font-size:12px;font-weight:800;">
                            ${rating ? `<span>⭐ ${rating}</span>` : ''}
                            ${price ? `<span>${escapeHtml(price)}</span>` : ''}
                        </div>
                        <p style="margin:10px 0 0;color:#94a3b8;font-size:11px;font-weight:700;">Click en el marcador para abrir el lugar</p>
                    </div>
                </article>
            `;
        }

        async function loadPlaces() {
            const response = await fetch(@json(route('places.geojson')), {
                headers: { Accept: 'application/json' }
            });

            if (!response.ok) return [];

            const data = await response.json();
            return data?.features || [];
        }

        function currentFeatures() {
            const query = normalize(activeQuery);

            return allFeatures.filter((feature) => {
                const place = feature.properties || {};
                const key = iconKey(place);
                const matchesType = activeType === 'TODOS' || key === activeType;

                const haystack = normalize([
                    place.name,
                    place.city,
                    place.type
                ].filter(Boolean).join(' '));

                const matchesQuery = !query || haystack.includes(query);

                return matchesType && matchesQuery;
            });
        }

        function clearMarkers() {
            markers.forEach(({ marker }) => marker.remove());
            markers = [];
            highlightedMarkerId = null;
            if (popup) popup.remove();
        }

        function markerElement(feature) {
            const place = feature.properties || {};
            const key = iconKey(place);
            const element = document.createElement('button');
            element.type = 'button';
            element.className = `vb-marker vb-marker--${key.toLowerCase()}`;
            element.innerHTML = TYPE_ICONS[key] || TYPE_ICONS.OTRO;
            element.setAttribute('aria-label', place.name || 'Lugar');

            element.addEventListener('mouseenter', () => {
                const coords = feature.geometry.coordinates;
                if (popup) popup.setLngLat(coords).setHTML(popupHtml(place)).addTo(map);
            });

            element.addEventListener('mouseleave', () => {
                if (highlightedMarkerId !== String(place.id) && popup) popup.remove();
            });

            element.addEventListener('click', () => {
                if (place.url) window.location.href = place.url;
            });

            return element;
        }

        function renderMarkers() {
            clearMarkers();

            currentFeatures().forEach((feature) => {
                const coords = feature.geometry.coordinates;
                const element = markerElement(feature);
                const marker = new mapboxgl.Marker({ element, anchor: 'bottom' })
                    .setLngLat(coords)
                    .addTo(map);

                markers.push({
                    id: String(feature.properties?.id || `${coords[0]}-${coords[1]}`),
                    marker,
                    element,
                    feature
                });
            });
        }

        function fitToFeatures() {
            const features = currentFeatures();
            if (!features.length || !map) return;

            if (features.length === 1) {
                map.flyTo({
                    center: features[0].geometry.coordinates,
                    zoom: 13.5,
                    essential: true
                });
                return;
            }

            const bounds = new mapboxgl.LngLatBounds();
            features.forEach(feature => bounds.extend(feature.geometry.coordinates));

            const isDesktop = window.matchMedia('(min-width: 1024px)').matches;

            map.fitBounds(bounds, {
                padding: isDesktop
                    ? { top: 250, right: 430, bottom: 150, left: 120 }
                    : { top: 250, right: 40, bottom: 300, left: 40 },
                maxZoom: 14.5,
                duration: 900,
                essential: true
            });
        }

        function scheduleFitToFeatures(delay = 120) {
            if (fitTimer) {
                clearTimeout(fitTimer);
            }

            fitTimer = setTimeout(() => {
                if (!map) return;

                map.resize();
                requestAnimationFrame(() => fitToFeatures());
            }, delay);
        }

        function renderResults() {
            const features = currentFeatures();
            const list = document.getElementById('vbResultsList');
            const empty = document.getElementById('vbEmptyState');
            const count = document.getElementById('vbResultCount');
            const label = document.getElementById('vbActiveFilterLabel');

            if (count) count.textContent = features.length;

            const filterLabel = activeType === 'TODOS' ? 'todos los lugares' : TYPE_LABELS[activeType].toLowerCase();
            if (label) label.textContent = activeQuery ? `Buscando "${activeQuery}" en ${filterLabel}` : `Mostrando ${filterLabel}`;

            if (!list || !empty) return;

            if (!features.length) {
                list.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');
            list.innerHTML = features.slice(0, 40).map((feature) => {
                const place = feature.properties || {};
                const key = iconKey(place);
                const price = priceText(place.price);

                return `
                    <button type="button"
                            class="vb-result-row flex w-full items-center gap-3 rounded-2xl p-3 text-left transition hover:bg-blue-50 dark:hover:bg-blue-500/10"
                            data-place-id="${escapeHtml(place.id)}">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                            ${TYPE_ICONS[key] || TYPE_ICONS.OTRO}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-extrabold text-gray-950 dark:text-slate-100">${escapeHtml(place.name || 'Lugar')}</span>
                            <span class="mt-0.5 block truncate text-xs font-semibold text-gray-500 dark:text-slate-400">${escapeHtml(place.city || 'Ubicación')} ${price ? `· ${escapeHtml(price)}` : ''}</span>
                        </span>
                    </button>
                `;
            }).join('');

            document.querySelectorAll('.vb-result-row').forEach((row) => {
                row.addEventListener('click', () => {
                    focusPlace(row.dataset.placeId);
                });
            });
        }

        function focusPlace(placeId) {
            const item = markers.find(marker => marker.id === String(placeId));
            if (!item || !map) return;

            markers.forEach(marker => marker.element.classList.remove('is-highlighted'));
            item.element.classList.add('is-highlighted');
            highlightedMarkerId = item.id;

            const coords = item.feature.geometry.coordinates;
            const place = item.feature.properties || {};

            map.flyTo({ center: coords, zoom: Math.max(map.getZoom(), 15), essential: true });
            if (popup) popup.setLngLat(coords).setHTML(popupHtml(place)).addTo(map);
        }

        function updateTypeButtons() {
            document.querySelectorAll('.vb-filter-btn').forEach((button) => {
                button.classList.toggle('is-active', button.dataset.type === activeType);
            });
        }

        function refresh({ fit = true } = {}) {
            updateTypeButtons();
            renderMarkers();
            renderResults();
            if (fit) scheduleFitToFeatures();
        }

        function bindControls() {
            document.querySelectorAll('.vb-filter-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    activeType = button.dataset.type || 'TODOS';
                    refresh();
                });
            });

            document.getElementById('vbSearchInput')?.addEventListener('input', (event) => {
                activeQuery = event.target.value || '';
                refresh({ fit: false });
                scheduleFitToFeatures(180);
            });

            document.getElementById('vbFitBtn')?.addEventListener('click', () => {
                fitToFeatures();
            });
        }

        function watchThemeChanges() {
            if (!window.MutationObserver) return;

            let currentStyle = mapStyleUrl();
            const observer = new MutationObserver(() => {
                if (!map) return;

                const nextStyle = mapStyleUrl();
                if (nextStyle === currentStyle) return;

                currentStyle = nextStyle;
                map.setStyle(nextStyle);
                setTimeout(() => {
                    map.resize();
                    fitToFeatures();
                }, 220);
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            bindControls();
            updateTypeButtons();

            try {
                if (!VB_MAPBOX_TOKEN || typeof mapboxgl === 'undefined') {
                    document.getElementById('mapError')?.classList.remove('hidden');
                    return;
                }

                mapboxgl.accessToken = VB_MAPBOX_TOKEN;

                popup = new mapboxgl.Popup({
                    closeButton: false,
                    closeOnClick: false,
                    offset: 18,
                    className: 'vb-popup'
                });

                map = new mapboxgl.Map({
                    container: 'vbMap',
                    style: mapStyleUrl(),
                    center: [-100.3899, 20.5888],
                    zoom: 11
                });

                map.addControl(new mapboxgl.NavigationControl(), 'top-right');
                map.addControl(new mapboxgl.GeolocateControl({
                    positionOptions: { enableHighAccuracy: true },
                    trackUserLocation: true,
                    showUserHeading: true
                }), 'top-right');

                watchThemeChanges();

                map.on('error', () => {
                    document.getElementById('mapError')?.classList.remove('hidden');
                });

                map.on('load', async () => {
                    map.resize();
                    allFeatures = await loadPlaces();

                    if (!allFeatures.length) {
                        document.getElementById('mapEmpty')?.classList.remove('hidden');
                    }

                    refresh({ fit: false });

                    setTimeout(() => {
                        map.resize();
                        fitToFeatures();
                    }, 250);
                });

                window.addEventListener('resize', () => {
                    if (!map) return;

                    map.resize();
                    fitToFeatures();
                });
            } catch (error) {
                document.getElementById('mapError')?.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
