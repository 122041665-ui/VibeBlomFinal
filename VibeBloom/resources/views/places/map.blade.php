<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $shell = "max-w-7xl mx-auto";
        $card = "bg-white dark:bg-slate-900 shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800";

        $btnPrimary = "px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-6 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition
                     active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $hint = "text-sm text-gray-600 dark:text-slate-400 mt-1";
        $title = "text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight";

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

        $labels = [
            'RESTAURANTE' => 'Restaurante',
            'CAFETERIA' => 'Cafetería',
            'BAR' => 'Bar',
            'ANTRO' => 'Antro',
            'PARQUE' => 'Parque',
            'PLAZA' => 'Plaza',
            'MIRADOR' => 'Mirador',
            'MUSEO' => 'Museo',
            'OTRO' => 'Otro',
        ];

        $mapboxToken = (string) env('MAPBOX_TOKEN');
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.10),transparent_55%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.10),transparent_55%)]"></div>

        <div class="{{ $container }}">
            <div class="{{ $shell }} mb-6">
                <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur shadow-sm p-5 sm:p-6 lg:p-7">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                Explora por tipo y ubicación
                            </div>

                            <h1 class="{{ $title }} mt-4">Mapa de lugares</h1>
                            <p class="{{ $hint }}">Hover para ver detalles, click para abrir el lugar.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('dashboard') }}" class="{{ $btnGhost }} inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 18l-6-6 6-6" />
                                </svg>
                                Volver
                            </a>

                            <a href="{{ route('places.create') }}" class="{{ $btnPrimary }}">
                                Crear lugar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ $shell }}">
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                    <aside class="xl:col-span-4">
                        <div class="{{ $card }} xl:sticky xl:top-6">
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div>
                                    <div class="text-base font-extrabold text-gray-900 dark:text-slate-100">Filtrar por tipo</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 leading-relaxed">
                                        Presiona un botón para mostrar solo ese tipo. Presiona de nuevo para quitar el filtro.
                                    </div>
                                </div>

                                <button type="button"
                                        id="vbClearType"
                                        class="{{ $btnGhost }} px-4 py-2 text-sm hidden">
                                    Quitar filtro
                                </button>
                            </div>

                            <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-2 gap-2.5">
                                @foreach($labels as $k => $label)
                                    <button type="button"
                                            class="vb-type-btn flex items-center gap-2 rounded-2xl px-3.5 py-3 border border-blue-100 dark:border-slate-700 bg-blue-50 dark:bg-slate-800
                                                   text-blue-800 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-slate-700 transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                            data-type="{{ $k }}">
                                        <span class="vb-gloss-plain">
                                            {!! $typeIcons[$k] !!}
                                        </span>
                                        <span class="text-xs font-semibold">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-5 rounded-2xl border border-blue-100 dark:border-slate-700 bg-blue-50/70 dark:bg-slate-800/80 px-4 py-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-slate-200">
                                        <span class="text-gray-500 dark:text-slate-400 font-medium">Mostrando:</span>
                                        <span id="vbFilterLabel" class="text-blue-700 dark:text-blue-400 font-extrabold">Todos</span>
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-slate-400">
                                        Tipos
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="button"
                                            id="vbFullscreenBtn"
                                            class="{{ $btnPrimary }} w-full justify-center px-4 py-2.5 text-sm inline-flex items-center gap-2">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 3H5a2 2 0 0 0-2 2v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M15 3h4a2 2 0 0 1 2 2v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M9 21H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M15 21h4a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        Pantalla completa
                                    </button>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl border border-gray-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/60 p-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-slate-100">Sugerencia</div>
                                <p class="mt-1 text-xs leading-relaxed text-gray-500 dark:text-slate-400">
                                    Usa pantalla completa para revisar mejor la distribución de los lugares y detectar zonas con más actividad.
                                </p>
                            </div>
                        </div>
                    </aside>

                    <section class="xl:col-span-8">
                        <div class="{{ $card }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <div>
                                    <div class="text-base font-extrabold text-gray-900 dark:text-slate-100">Navegación del mapa</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                        Tip: si no ves popups, mueve el mouse sobre un marcador
                                    </div>
                                </div>

                                <div class="inline-flex items-center gap-2 rounded-full border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:text-slate-300">
                                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                    Vista interactiva
                                </div>
                            </div>

                            <div id="map"
                                 class="w-full rounded-[24px] border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden bg-white dark:bg-slate-950"
                                 style="height: 680px;"></div>

                            <div id="mapEmpty" class="hidden mt-4 text-sm text-gray-600 dark:text-slate-400">
                                No hay lugares con coordenadas todavía. Asegúrate de guardar <span class="font-semibold">lat/lng</span> al crear un lugar.
                            </div>

                            <div id="mapError" class="hidden mt-4 rounded-2xl border border-red-200 dark:border-red-900/60 bg-red-50 dark:bg-red-950/40 p-4 text-sm text-red-800 dark:text-red-300">
                                No se pudo cargar el mapa. Revisa el token de Mapbox (MAPBOX_TOKEN) y la consola del navegador.
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div id="vbMapOverlay" class="hidden fixed inset-0 z-[9999]">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative mx-auto h-[92vh] w-[96vw] max-w-7xl top-[4vh]">
            <div class="h-full bg-white dark:bg-slate-900 rounded-[28px] shadow-xl border border-gray-200 dark:border-slate-700 overflow-hidden flex flex-col">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="h-9 w-9 rounded-xl bg-blue-50 dark:bg-slate-800 border border-blue-100 dark:border-slate-700 grid place-items-center text-blue-700 dark:text-blue-400">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 21s7-4.6 7-11a7 7 0 1 0-14 0c0 6.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M12 11a2 2 0 1 0 0-4a2 2 0 0 0 0 4Z" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-slate-100">Mapa de lugares</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">
                                Filtro: <span id="vbFilterLabelFs" class="font-semibold text-blue-700 dark:text-blue-400">Todos</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" id="vbExitFullscreen" class="{{ $btnGhost }} px-4 py-2 text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>

                <div id="mapFs" class="w-full flex-1 bg-white dark:bg-slate-950"></div>
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

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

    <style>
        .vb-gloss-plain {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
        }

        .mapboxgl-ctrl-top-right { top: 12px !important; right: 12px !important; }
        .mapboxgl-ctrl-top-left { top: 12px !important; left: 12px !important; }

        .dark .mapboxgl-ctrl-group {
            background: rgb(15 23 42 / 0.95) !important;
            border: 1px solid rgb(51 65 85) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .25) !important;
        }

        .dark .mapboxgl-ctrl-group button span {
            filter: invert(1) opacity(.85);
        }

        .vb-popup {
            pointer-events: none;
            z-index: 9999 !important;
        }

        .vb-popup.mapboxgl-popup {
            max-width: none !important;
        }

        .vb-popup .mapboxgl-popup-content {
            max-width: none !important;
            width: 320px;
            border-radius: 18px;
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(229, 231, 235, 1);
            box-shadow: 0 12px 34px rgba(0, 0, 0, .14);
            background: #fff;
        }

        .dark .vb-popup .mapboxgl-popup-content {
            background: rgb(15 23 42);
            border-color: rgb(51 65 85);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .35);
        }

        .vb-popup .mapboxgl-popup-tip {
            border-top-color: #fff !important;
        }

        .dark .vb-popup .mapboxgl-popup-tip {
            border-top-color: rgb(15 23 42) !important;
        }

        .vb-marker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(229, 231, 235, 1);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .10);
            backdrop-filter: blur(8px);
            cursor: pointer;
            user-select: none;
            transition: transform .12s ease, box-shadow .12s ease;
            max-width: 220px;
            white-space: nowrap;
        }

        .dark .vb-marker {
            background: rgba(15, 23, 42, .96);
            border-color: rgba(51, 65, 85, 1);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .28);
        }

        .vb-marker:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 34px rgba(0, 0, 0, .14);
        }

        .vb-marker__icon {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(229, 231, 235, 1);
            background: rgba(37, 99, 235, .08);
            color: rgba(29, 78, 216, 1);
            flex: 0 0 auto;
        }

        .dark .vb-marker__icon {
            border-color: rgba(51, 65, 85, 1);
            background: rgba(59, 130, 246, .18);
            color: rgb(96 165 250);
        }

        .vb-marker__icon svg {
            width: 16px;
            height: 16px;
            display: block;
        }

        .vb-marker__name {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
            font-weight: 900;
            font-size: 12.5px;
            color: rgba(17, 24, 39, 1);
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .vb-marker__name {
            color: rgb(241 245 249);
        }

        .vb-mini {
            width: 100%;
            background: #fff;
            font-family: ui-sans-serif, system-ui;
        }

        .dark .vb-mini {
            background: rgb(15 23 42);
        }

        .vb-mini__media {
            height: 170px;
            position: relative;
            overflow: hidden;
            background: rgba(243, 244, 246, 1);
        }

        .dark .vb-mini__media {
            background: rgb(30 41 59);
        }

        .vb-mini__bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: blur(18px);
            transform: scale(1.25);
            opacity: .55;
        }

        .vb-mini__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: scale(1.02);
            display: block;
        }

        .vb-mini__noimg {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: rgba(107, 114, 128, 1);
            font-weight: 800;
            font-size: 12px;
        }

        .dark .vb-mini__noimg {
            color: rgb(148 163 184);
        }

        .vb-mini__noimg svg {
            width: 16px;
            height: 16px;
            color: rgba(107, 114, 128, 1);
        }

        .dark .vb-mini__noimg svg {
            color: rgb(148 163 184);
        }

        .vb-mini__body {
            padding: 12px;
        }

        .vb-mini__badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(239, 246, 255, 1);
            color: rgba(29, 78, 216, 1);
            border: 1px solid rgba(219, 234, 254, 1);
            font-weight: 900;
            font-size: 12px;
        }

        .dark .vb-mini__badge {
            background: rgba(30, 41, 59, 1);
            color: rgb(96 165 250);
            border-color: rgb(51 65 85);
        }

        .vb-mini__badgeIcon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .vb-mini__badgeIcon svg {
            width: 16px;
            height: 16px;
            display: block;
        }

        .vb-mini__title {
            margin-top: 10px;
            font-weight: 950;
            font-size: 15px;
            color: rgba(17, 24, 39, 1);
            line-height: 1.15;
            white-space: normal;
            word-break: break-word;
        }

        .dark .vb-mini__title {
            color: rgb(241 245 249);
        }

        .vb-mini__meta {
            margin-top: 6px;
            color: rgba(107, 114, 128, 1);
            font-size: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            row-gap: 4px;
            align-items: flex-start;
            white-space: normal;
        }

        .dark .vb-mini__meta {
            color: rgb(148 163 184);
        }

        .vb-mini__meta span {
            white-space: normal;
        }

        .vb-mini__bottom {
            padding: 10px 12px 12px 12px;
            border-top: 1px solid rgba(243, 244, 246, 1);
        }

        .dark .vb-mini__bottom {
            border-top-color: rgb(30 41 59);
        }

        .vb-mini__hint {
            color: rgba(156, 163, 175, 1);
            font-size: 11px;
            line-height: 1.2;
        }

        .dark .vb-mini__hint {
            color: rgb(148 163 184);
        }

        .vb-mini__bar {
            height: 4px;
            width: 100%;
            background: rgba(37, 99, 235, 1);
        }

        .vb-type-btn.is-active {
            background: rgba(37, 99, 235, 1) !important;
            border-color: rgba(37, 99, 235, 1) !important;
            color: #fff !important;
            box-shadow: 0 10px 24px rgba(37, 99, 235, .22);
        }

        .vb-type-btn.is-active .vb-gloss-plain svg {
            color: #fff !important;
        }
    </style>

    <script>
        const VB_MAPBOX_TOKEN = @json($mapboxToken);

        const popup = new mapboxgl.Popup({
            closeButton: false,
            closeOnClick: false,
            offset: 18,
            className: 'vb-popup'
        });

        let allFeatures = [];
        let activeType = null;
        let map = null;
        let mapFs = null;
        let activeMarkerId = null;

        const markersMain = [];
        const markersFs = [];

        function escapeAttr(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;');
        }

        function decodeEntitiesLite(value) {
            return String(value)
                .replaceAll('&quot;', '"')
                .replaceAll('&#34;', '"')
                .replaceAll('&apos;', "'")
                .replaceAll('&#39;', "'")
                .replaceAll('&gt;', '>')
                .replaceAll('&#62;', '>')
                .replaceAll('&lt;', '<')
                .replaceAll('&#60;', '<')
                .replaceAll('&amp;', '&');
        }

        function safePhoto(url) {
            if (!url) return '';

            let cleaned = decodeEntitiesLite(String(url)).trim();

            if (cleaned.includes('<img')) {
                const match = cleaned.match(/src\s*=\s*["']([^"']+)["']/i);
                if (match && match[1]) cleaned = match[1].trim();
            }

            cleaned = cleaned.split('"')[0]
                .split("'")[0]
                .split('<')[0]
                .split('>')[0]
                .split(';')[0]
                .trim();

            if (cleaned.startsWith('storage/')) {
                cleaned = '/' + cleaned;
            }

            if (!(cleaned.startsWith('/') || cleaned.startsWith('http://') || cleaned.startsWith('https://'))) {
                return '';
            }

            try {
                cleaned = encodeURI(cleaned);
            } catch (error) {}

            return cleaned;
        }

        function noImgHtml() {
            return `
                <div class="vb-mini__noimg">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 11l2.5 2.5L14 10l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 9h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    Sin foto
                </div>
            `;
        }

        function typeSvg(keyUpper) {
            const icons = {
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

            return icons[keyUpper] || icons.OTRO;
        }

        function iconsHasKey(key) {
            return ['RESTAURANTE', 'CAFETERIA', 'BAR', 'ANTRO', 'PARQUE', 'PLAZA', 'MIRADOR', 'MUSEO', 'OTRO'].includes(key);
        }

        function keyToUpper(iconKey, typeLabel) {
            const normalized = (iconKey || '').toString().trim().toLowerCase();

            const mapKeys = {
                restaurante: 'RESTAURANTE',
                cafeteria: 'CAFETERIA',
                'café': 'CAFETERIA',
                bar: 'BAR',
                antro: 'ANTRO',
                parque: 'PARQUE',
                plaza: 'PLAZA',
                mirador: 'MIRADOR',
                museo: 'MUSEO',
                otro: 'OTRO'
            };

            if (mapKeys[normalized]) {
                return mapKeys[normalized];
            }

            if (typeLabel) {
                const fallback = typeLabel.toString().trim().toUpperCase();
                if (iconsHasKey(fallback)) {
                    return fallback;
                }
            }

            return 'OTRO';
        }

        function money(price) {
            const amount = Number(price);
            if (!Number.isFinite(amount)) return null;

            return amount.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
        }

        function renderMiniCard(place) {
            const iconKey = (place.iconKey || '').toString();
            const upper = keyToUpper(iconKey, place.type);
            const svg = typeSvg(upper);

            const rating = place.rating ? `⭐ ${Number(place.rating).toFixed(1)}` : '⭐ —';
            const city = place.city ? place.city : 'Ubicación';
            const price = money(place.price);

            const rawPhoto = safePhoto(place.photo_url);
            const photoAttr = escapeAttr(rawPhoto);
            const photoCss = escapeAttr(rawPhoto);

            const media = rawPhoto
                ? `
                    <div class="vb-mini__media">
                        <div class="vb-mini__bg" style="background-image:url('${photoCss}')"></div>
                        <img class="vb-mini__img" src="${photoAttr}" alt="Foto del lugar"
                             onerror="this.onerror=null;this.style.display='none';" />
                    </div>
                  `
                : `<div class="vb-mini__media">${noImgHtml()}</div>`;

            return `
                <div class="vb-mini">
                    ${media}
                    <div class="vb-mini__body">
                        <span class="vb-mini__badge">
                            <span class="vb-mini__badgeIcon">${svg}</span>
                            <span>${escapeAttr(place.type || 'Lugar')}</span>
                        </span>

                        <div class="vb-mini__title">${escapeAttr(place.name || 'Lugar')}</div>

                        <div class="vb-mini__meta">
                            <span>${escapeAttr(city)}</span>
                            <span>•</span>
                            <span>${escapeAttr(rating)}</span>
                            ${price ? `<span>•</span><span>${escapeAttr(price)}</span>` : ``}
                        </div>
                    </div>

                    <div class="vb-mini__bottom">
                        <div class="vb-mini__hint">Click para abrir el lugar</div>
                    </div>

                    <div class="vb-mini__bar"></div>
                </div>
            `;
        }

        async function loadPlaces() {
            const response = await fetch(@json(route('places.geojson')), {
                headers: { Accept: 'application/json' }
            });

            if (!response.ok) {
                return { type: 'FeatureCollection', features: [] };
            }

            return await response.json();
        }

        function clearMarkers(markerArray) {
            markerArray.forEach(marker => marker.remove());
            markerArray.length = 0;
            activeMarkerId = null;
        }

        function addPlaceMarker(mapInstance, markerArray, feature) {
            const coords = feature.geometry.coordinates;
            const place = feature.properties || {};

            const upper = keyToUpper((place.iconKey || '').toString(), place.type);
            const svg = typeSvg(upper);

            const element = document.createElement('div');
            element.className = 'vb-marker';
            element.innerHTML = `
                <div class="vb-marker__icon">${svg}</div>
                <div class="vb-marker__name" title="${escapeAttr(place.name || '')}">${escapeAttr(place.name || 'Lugar')}</div>
            `;

            element.addEventListener('pointerenter', () => {
                const markerId = String(place.id ?? `${coords[0]}-${coords[1]}`);

                if (activeMarkerId === markerId) return;

                activeMarkerId = markerId;
                popup.setLngLat(coords).setHTML(renderMiniCard(place)).addTo(mapInstance);
            });

            element.addEventListener('pointerleave', () => {
                const markerId = String(place.id ?? `${coords[0]}-${coords[1]}`);

                if (activeMarkerId === markerId) {
                    popup.remove();
                    activeMarkerId = null;
                }
            });

            element.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (place.url) {
                    window.location.href = place.url;
                }
            });

            const marker = new mapboxgl.Marker({
                element,
                anchor: 'bottom'
            }).setLngLat(coords).addTo(mapInstance);

            markerArray.push(marker);
        }

        function computeBounds(features) {
            const bounds = new mapboxgl.LngLatBounds();
            features.forEach(feature => bounds.extend(feature.geometry.coordinates));
            return bounds;
        }

        function setFilterLabel() {
            const types = @json($labels);
            const label = activeType ? (types[activeType] || activeType) : 'Todos';

            const labelMain = document.getElementById('vbFilterLabel');
            const labelFs = document.getElementById('vbFilterLabelFs');
            const clearButton = document.getElementById('vbClearType');

            if (labelMain) labelMain.textContent = label;
            if (labelFs) labelFs.textContent = label;

            if (clearButton) {
                if (activeType) clearButton.classList.remove('hidden');
                else clearButton.classList.add('hidden');
            }
        }

        function applyTypeButtons() {
            document.querySelectorAll('.vb-type-btn').forEach(button => {
                const type = button.getAttribute('data-type');

                if (activeType && type === activeType) {
                    button.classList.add('is-active');
                } else {
                    button.classList.remove('is-active');
                }
            });
        }

        function filteredFeatures() {
            if (!activeType) return allFeatures;

            return allFeatures.filter(feature => {
                const place = feature.properties || {};
                const type = (place.type || '').toString().trim().toUpperCase();
                const iconKey = (place.iconKey || '').toString().trim().toUpperCase();

                return type === activeType || iconKey === activeType;
            });
        }

        function renderMarkers(mapInstance, markerArray) {
            const features = filteredFeatures();
            clearMarkers(markerArray);
            features.forEach(feature => addPlaceMarker(mapInstance, markerArray, feature));
        }

        function fitToCurrent(mapInstance) {
            const features = filteredFeatures();
            if (!features.length) return;

            const bounds = computeBounds(features);
            mapInstance.fitBounds(bounds, { padding: 90, maxZoom: 15 });
        }

        function openFullscreen() {
            const overlay = document.getElementById('vbMapOverlay');
            overlay.classList.remove('hidden');

            if (!mapFs) {
                mapFs = new mapboxgl.Map({
                    container: 'mapFs',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: map ? map.getCenter() : [-99.1332, 19.4326],
                    zoom: map ? map.getZoom() : 11
                });

                mapFs.addControl(new mapboxgl.NavigationControl(), 'top-right');

                mapFs.on('load', () => {
                    renderMarkers(mapFs, markersFs);
                    fitToCurrent(mapFs);
                });
            } else {
                mapFs.resize();
                renderMarkers(mapFs, markersFs);
                fitToCurrent(mapFs);
            }
        }

        function closeFullscreen() {
            const overlay = document.getElementById('vbMapOverlay');
            overlay.classList.add('hidden');
        }

        function setActiveType(typeKeyOrNull) {
            activeType = typeKeyOrNull;
            setFilterLabel();
            applyTypeButtons();

            if (map) {
                renderMarkers(map, markersMain);
                fitToCurrent(map);
            }

            if (mapFs) {
                renderMarkers(mapFs, markersFs);
                fitToCurrent(mapFs);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            setFilterLabel();

            try {
                if (!VB_MAPBOX_TOKEN) {
                    document.getElementById('mapError')?.classList.remove('hidden');
                    return;
                }

                mapboxgl.accessToken = VB_MAPBOX_TOKEN;

                map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: [-99.1332, 19.4326],
                    zoom: 11
                });

                map.addControl(new mapboxgl.NavigationControl(), 'top-right');

                map.on('error', () => {
                    document.getElementById('mapError')?.classList.remove('hidden');
                });

                map.on('load', async () => {
                    const data = await loadPlaces();

                    if (!data?.features?.length) {
                        document.getElementById('mapEmpty')?.classList.remove('hidden');
                        return;
                    }

                    allFeatures = data.features;
                    renderMarkers(map, markersMain);
                    fitToCurrent(map);
                });

                document.getElementById('vbFullscreenBtn')?.addEventListener('click', () => {
                    if (!VB_MAPBOX_TOKEN) return;

                    openFullscreen();
                    setTimeout(() => {
                        if (mapFs) mapFs.resize();
                    }, 50);
                });

                document.getElementById('vbExitFullscreen')?.addEventListener('click', closeFullscreen);

                document.getElementById('vbClearType')?.addEventListener('click', () => {
                    setActiveType(null);
                });

                document.querySelectorAll('.vb-type-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const type = button.getAttribute('data-type');

                        if (activeType === type) setActiveType(null);
                        else setActiveType(type);
                    });
                });
            } catch (error) {
                document.getElementById('mapError')?.classList.remove('hidden');
            }
        });
    </script>

    <div class="h-20 sm:h-24"></div>
</x-app-layout>