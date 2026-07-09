<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      x-init="document.documentElement.classList.toggle('dark', darkMode)"
      :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VibeBloom') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"],
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] {
            position: fixed !important;
            right: 1.5rem !important;
            bottom: 1.5rem !important;
            left: auto !important;
            z-index: 70 !important;
            isolation: isolate;
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] > span:first-child,
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] > span:first-child {
            position: absolute !important;
            inset: -0.45rem !important;
            border-radius: 1.35rem !important;
            background: radial-gradient(circle at 30% 20%, rgba(59, 130, 246, .42), rgba(37, 99, 235, .12) 58%, transparent 72%) !important;
            filter: blur(14px) !important;
            opacity: .75 !important;
            transition: opacity .2s ease, transform .2s ease !important;
            pointer-events: none !important;
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] > span:last-child,
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] > span:last-child {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: .7rem !important;
            min-height: 3.15rem !important;
            padding: .85rem 1.15rem !important;
            border-radius: 1.15rem !important;
            border: 1px solid rgba(191, 219, 254, .72) !important;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%) !important;
            color: #fff !important;
            box-shadow: 0 18px 42px rgba(37, 99, 235, .30), inset 0 1px 0 rgba(255, 255, 255, .28) !important;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease !important;
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"]:hover > span:first-child,
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"]:hover > span:first-child {
            opacity: 1 !important;
            transform: scale(1.05) !important;
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"]:hover > span:last-child,
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"]:hover > span:last-child {
            transform: translateY(-2px) !important;
            filter: saturate(1.08) !important;
            box-shadow: 0 22px 52px rgba(37, 99, 235, .38), inset 0 1px 0 rgba(255, 255, 255, .32) !important;
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] svg,
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] svg {
            width: 1.15rem !important;
            height: 1.15rem !important;
            filter: drop-shadow(0 4px 8px rgba(15, 23, 42, .18));
        }

        a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] span.font-semibold,
        a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] span.font-semibold {
            font-weight: 800 !important;
            letter-spacing: 0 !important;
        }

        @media (max-width: 640px) {
            a[aria-label="Abrir Vibe IA"][href$="/ai/voz"],
            a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] {
                right: 1rem !important;
                bottom: 1rem !important;
            }

            a[aria-label="Abrir Vibe IA"][href$="/ai/voz"] > span:last-child,
            a[aria-label="Abrir Vibe IA"][href$="/ai/voice"] > span:last-child {
                min-height: 3rem !important;
                padding: .8rem 1rem !important;
            }
        }
    </style>
</head>

<body class="font-sans antialiased
             bg-slate-50 text-gray-900
             dark:bg-slate-950 dark:text-slate-100
             transition-colors duration-300">

    <x-banner />

    <div class="min-h-screen">

        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white/90 shadow-sm backdrop-blur border-b border-gray-100
                           dark:bg-slate-900/90 dark:border-slate-800">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-gray-100 dark:border-slate-800 bg-white/90 dark:bg-slate-950/90 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 py-10">
                <div class="grid grid-cols-1 md:grid-cols-[1.2fr_0.8fr_0.8fr] gap-8">
                    <div>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/Vibe.png') }}"
                                 alt="VibeBloom"
                                 class="h-14 w-auto object-contain">
                            <div>
                                <p class="text-sm font-extrabold text-gray-900 dark:text-slate-100">VibeBloom</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Descubre lugares, comparte experiencias.</p>
                            </div>
                        </div>

                        <p class="mt-4 max-w-xl text-sm leading-6 text-gray-600 dark:text-slate-400">
                            Plataforma escolar desarrollada como proyecto académico de la Universidad Politécnica de Querétaro. VibeBloom ayuda a explorar lugares, guardar recuerdos y consultar recomendaciones de forma visual, ordenada y colaborativa.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900 dark:text-slate-100">Información</h3>
                        <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-slate-400">
                            <li>
                                <a href="#" class="hover:text-blue-700 dark:hover:text-blue-400 transition">Acerca de la plataforma</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-blue-700 dark:hover:text-blue-400 transition">Términos de uso</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-blue-700 dark:hover:text-blue-400 transition">Condiciones del servicio</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-blue-700 dark:hover:text-blue-400 transition">Privacidad y datos</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900 dark:text-slate-100">Redes sociales</h3>
                        <div class="mt-4 flex items-center gap-3">
                            <a href="#" aria-label="Instagram"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-300 hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-400 transition">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="3.4" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="17.2" cy="6.8" r="1" fill="currentColor"/>
                                </svg>
                            </a>

                            <a href="#" aria-label="Facebook"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-300 hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-400 transition">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M14 8.25h2V5h-2.35C10.95 5 10 6.72 10 8.75V11H8v3.15h2V20h3.4v-5.85h2.25L16 11h-2.6V9.1c0-.55.22-.85.6-.85Z" fill="currentColor"/>
                                </svg>
                            </a>

                            <a href="#" aria-label="X"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-300 hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-400 transition">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 5l12 14M18 5L6 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </a>

                            <a href="#" aria-label="GitHub"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-300 hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-400 transition">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3.75a8.25 8.25 0 0 0-2.6 16.08c.41.07.56-.18.56-.4v-1.38c-2.27.49-2.75-1.1-2.75-1.1c-.37-.94-.91-1.2-.91-1.2c-.74-.5.06-.49.06-.49c.82.06 1.25.85 1.25.85c.73 1.25 1.92.89 2.38.68c.08-.53.29-.89.52-1.1c-1.81-.2-3.72-.9-3.72-4.04c0-.9.32-1.63.84-2.2c-.08-.2-.36-1.04.08-2.17c0 0 .68-.22 2.25.84a7.8 7.8 0 0 1 4.08 0c1.57-1.06 2.25-.84 2.25-.84c.44 1.13.16 1.97.08 2.17c.52.57.84 1.3.84 2.2c0 3.14-1.91 3.84-3.73 4.04c.3.25.56.75.56 1.52v2.25c0 .22.15.48.57.4A8.25 8.25 0 0 0 12 3.75Z" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>

                        <p class="mt-4 text-sm leading-6 text-gray-600 dark:text-slate-400">
                            Síguenos para novedades, avances del proyecto y actualizaciones de la comunidad VibeBloom.
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 dark:border-slate-800 pt-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">
                        © 2026 VibeBloom. Proyecto escolar UPQ.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">
                        Uso educativo. La información de lugares puede cambiar y debe validarse con cada establecimiento.
                    </p>
                </div>
            </div>
        </footer>

    </div>

    @stack('modals')

    @livewireScripts

</body>
</html>
