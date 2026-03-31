<nav x-data="{
        open: false,
        darkMode: localStorage.getItem('theme') === 'dark'
     }"
     x-init="
        document.documentElement.classList.toggle('dark', darkMode);
        $watch('darkMode', value => {
            localStorage.setItem('theme', value ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', value);
        });
     "
     class="bg-white/85 dark:bg-slate-950/85 backdrop-blur border-b border-gray-100 dark:border-slate-800 sticky top-0 z-50 transition-colors duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="min-h-[76px] sm:h-20 flex items-center justify-between gap-3 sm:gap-4 py-3 sm:py-0">

            <div class="flex min-w-0 items-center gap-3 sm:gap-6 flex-1">
                <div class="shrink-0 flex items-center min-w-0">
                    <a href="{{ route('dashboard') }}"
                       class="group inline-flex items-center gap-2.5 sm:gap-3 min-w-0 transition-transform duration-300 ease-out hover:scale-[1.01]">
                        <div class="shrink-0 rounded-2xl bg-white/80 dark:bg-slate-900/80 p-1.5 sm:p-2 ring-1 ring-slate-200/70 dark:ring-slate-700/70 shadow-sm">
                            <img
                                src="{{ asset('images/vibebloom.png') }}"
                                alt="VibeBloom"
                                class="h-10 w-auto sm:h-11 md:h-12 object-contain"
                                onerror="this.style.display='none'; this.parentElement.classList.add('hidden')" />
                        </div>

                        <div class="min-w-0 hidden xs:block">
                            <p class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-slate-100 leading-none truncate">
                                VibeBloom
                            </p>
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 truncate">
                                Descubre lugares y experiencias
                            </p>
                        </div>
                    </a>
                </div>

                <div class="hidden lg:flex items-center gap-2 min-w-0">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('dashboard')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Inicio
                    </x-nav-link>

                    <x-nav-link href="{{ route('places.map') }}" :active="request()->routeIs('places.map')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('places.map')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Mapa
                    </x-nav-link>

                    <x-nav-link href="{{ route('places.mine') }}" :active="request()->routeIs('places.mine')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('places.mine')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Mis lugares
                    </x-nav-link>

                    <x-nav-link href="{{ route('place-submissions.index') }}" :active="request()->routeIs('place-submissions.*')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('place-submissions.*')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Mis aprobaciones
                    </x-nav-link>

                    <x-nav-link href="{{ route('favorites.mine') }}" :active="request()->routeIs('favorites.mine')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('favorites.mine')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Mis favoritos
                    </x-nav-link>

                    <x-nav-link href="{{ route('memories.index') }}" :active="request()->routeIs('memories.*')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('memories.*')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Mis recuerdos
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden lg:flex items-center gap-3 shrink-0">
                <button
                    @click="darkMode = !darkMode"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl
                           border border-gray-200 dark:border-slate-700
                           bg-white/90 dark:bg-slate-900
                           text-gray-700 dark:text-slate-200
                           shadow-sm hover:shadow-md
                           hover:bg-gray-50 dark:hover:bg-slate-800
                           transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                    :title="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">

                    <svg x-show="!darkMode" class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <svg x-show="darkMode" x-cloak class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3v2.2M12 18.8V21M21 12h-2.2M5.2 12H3M18.4 18.4l-1.5-1.5M7.1 7.1L5.6 5.6M18.4 5.6l-1.5 1.5M7.1 16.9l-1.5 1.5"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="12" cy="12" r="4.2" stroke="currentColor" stroke-width="1.8"/>
                    </svg>

                    <span class="text-sm font-semibold" x-text="darkMode ? 'Claro' : 'Oscuro'"></span>
                </button>

                <div class="relative">
                    @auth
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 rounded-full px-2 py-1.5
                                               border border-gray-200 dark:border-slate-700
                                               bg-white/90 dark:bg-slate-900 shadow-sm
                                               hover:shadow-md transition
                                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">

                                    <img class="size-8 rounded-full object-cover"
                                         src="{{ Auth::user()->profile_photo_url }}"
                                         alt="{{ Auth::user()->name }}" />

                                    <span class="text-sm font-semibold text-gray-800 dark:text-slate-100 max-w-[150px] truncate">
                                        {{ Auth::user()->name }}
                                    </span>

                                    <svg class="w-4 h-4 text-gray-500 dark:text-slate-400"
                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="bg-white dark:bg-slate-900 dark:text-slate-100">
                                    <x-dropdown-link href="{{ route('profile.show') }}">
                                        Perfil
                                    </x-dropdown-link>

                                    <div class="border-t border-gray-200 dark:border-slate-700"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}"
                                                         onclick="event.preventDefault(); this.closest('form').submit();">
                                            Cerrar sesión
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}"
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition
                                      bg-blue-600 text-white hover:bg-blue-700
                                      focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 shadow-sm">
                                Iniciar sesión
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="px-4 py-2 rounded-xl text-sm font-semibold transition
                                          bg-blue-50 text-blue-800 border border-blue-100
                                          dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700
                                          hover:bg-blue-100 dark:hover:bg-slate-700
                                          focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 shadow-sm">
                                    Registrarse
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>

            <div class="flex lg:hidden items-center gap-2 shrink-0">
                <button
                    @click="darkMode = !darkMode"
                    class="inline-flex items-center justify-center p-2.5 rounded-xl
                           border border-gray-200 dark:border-slate-700
                           bg-white/90 dark:bg-slate-900 shadow-sm
                           text-gray-500 dark:text-slate-300 hover:text-gray-700 dark:hover:text-white
                           hover:bg-white dark:hover:bg-slate-800
                           transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                    :title="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">
                    <svg x-show="!darkMode" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3v2.2M12 18.8V21M21 12h-2.2M5.2 12H3M18.4 18.4l-1.5-1.5M7.1 7.1L5.6 5.6M18.4 5.6l-1.5 1.5M7.1 16.9l-1.5 1.5"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="12" cy="12" r="4.2" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </button>

                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2.5 rounded-xl
                               border border-gray-200 dark:border-slate-700
                               bg-white/90 dark:bg-slate-900 shadow-sm
                               text-gray-500 dark:text-slate-300 hover:text-gray-700 dark:hover:text-white
                               hover:bg-white dark:hover:bg-slate-800
                               transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                        :aria-expanded="open.toString()"
                        aria-label="Abrir menú">

                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{ 'hidden': open }"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open }"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <div id="vbSearchBarWrap"
         class="border-t border-gray-100 dark:border-slate-800 bg-white/75 dark:bg-slate-950/75 backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-slate-950/70 transition-colors duration-300">
        <div id="vbSearchBarInner"
             class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <form action="{{ route('dashboard') }}" method="GET" class="space-y-2 md:space-y-0">
                <div class="group grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_1px_minmax(0,1fr)_1px_minmax(0,1fr)_1px_minmax(0,1fr)_auto]
                            rounded-[28px] border border-gray-200/90 dark:border-slate-700/90 bg-white/95 dark:bg-slate-900/95
                            shadow-[0_8px_28px_rgba(15,23,42,0.06)]
                            dark:shadow-[0_10px_30px_rgba(0,0,0,0.34)]
                            hover:shadow-[0_12px_34px_rgba(15,23,42,0.10)]
                            dark:hover:shadow-[0_12px_36px_rgba(0,0,0,0.42)]
                            overflow-hidden transition-all duration-300">

                    <div class="min-w-0 px-4 sm:px-5 py-3.5 hover:bg-blue-50/40 dark:hover:bg-slate-800/70 transition-colors duration-200">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-2xl
                                         bg-blue-50 ring-1 ring-blue-100 text-blue-600
                                         dark:bg-blue-500/10 dark:ring-blue-500/20 dark:text-blue-400">
                                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 3.75v8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M10 3.75v8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M7 7.25h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M15.5 3.75v16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M15.5 3.75c2 0 3.25 1.55 3.25 3.45v.85H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <div class="min-w-0 w-full">
                                <label class="block text-[11px] font-extrabold uppercase tracking-[0.12em] text-gray-900 dark:text-slate-100">
                                    Tipo
                                </label>
                                <select name="type"
                                        class="mt-1 w-full bg-transparent border-0 p-0 pr-7 text-sm font-medium text-gray-700 dark:text-slate-300 focus:outline-none focus:ring-0 cursor-pointer">
                                    <option value="">Cualquiera</option>
                                    <option value="RESTAURANTE" @selected(request('type') === 'RESTAURANTE')>Restaurante</option>
                                    <option value="CAFETERIA" @selected(request('type') === 'CAFETERIA')>Cafetería</option>
                                    <option value="BAR" @selected(request('type') === 'BAR')>Bar</option>
                                    <option value="ANTRO" @selected(request('type') === 'ANTRO')>Antro</option>
                                    <option value="PARQUE" @selected(request('type') === 'PARQUE')>Parque</option>
                                    <option value="PLAZA" @selected(request('type') === 'PLAZA')>Plaza</option>
                                    <option value="MIRADOR" @selected(request('type') === 'MIRADOR')>Mirador</option>
                                    <option value="MUSEO" @selected(request('type') === 'MUSEO')>Museo</option>
                                    <option value="OTRO" @selected(request('type') === 'OTRO')>Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block my-4 w-px bg-gradient-to-b from-transparent via-gray-200 to-transparent dark:via-slate-700"></div>

                    <div class="min-w-0 px-4 sm:px-5 py-3.5 hover:bg-blue-50/40 dark:hover:bg-slate-800/70 transition-colors duration-200 border-t md:border-t-0 border-gray-100 dark:border-slate-800 md:border-0">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-2xl
                                         bg-blue-50 ring-1 ring-blue-100 text-blue-600
                                         dark:bg-blue-500/10 dark:ring-blue-500/20 dark:text-blue-400">
                                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3.5v17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M16.1 7.2c0-1.55-1.83-2.7-4.1-2.7s-4.1 1.15-4.1 2.7c0 1.72 1.5 2.38 4.1 2.93c2.67.55 4.1 1.27 4.1 3c0 1.55-1.83 2.7-4.1 2.7s-4.1-1.15-4.1-2.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <div class="min-w-0 w-full">
                                <label class="block text-[11px] font-extrabold uppercase tracking-[0.12em] text-gray-900 dark:text-slate-100">
                                    Precio
                                </label>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-400 dark:text-slate-500">MXN</span>
                                    <input type="number"
                                           name="max_price"
                                           value="{{ request('max_price') }}"
                                           placeholder="Máximo"
                                           step="1"
                                           min="0"
                                           class="w-full bg-transparent border-0 p-0 text-sm font-medium text-gray-700 dark:text-slate-300 placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block my-4 w-px bg-gradient-to-b from-transparent via-gray-200 to-transparent dark:via-slate-700"></div>

                    <div class="min-w-0 px-4 sm:px-5 py-3.5 hover:bg-blue-50/40 dark:hover:bg-slate-800/70 transition-colors duration-200 border-t md:border-t-0 border-gray-100 dark:border-slate-800 md:border-0">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-2xl
                                         bg-blue-50 ring-1 ring-blue-100 text-blue-600
                                         dark:bg-blue-500/10 dark:ring-blue-500/20 dark:text-blue-400">
                                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 20.25s5-3.72 5-8.63a5 5 0 1 0-10 0c0 4.91 5 8.63 5 8.63Z"
                                          stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <circle cx="12" cy="11.25" r="1.9"
                                            stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </span>

                            <div class="min-w-0 w-full">
                                <label class="block text-[11px] font-extrabold uppercase tracking-[0.12em] text-gray-900 dark:text-slate-100">
                                    Ciudad
                                </label>
                                <input type="text"
                                       name="city"
                                       value="{{ request('city') }}"
                                       placeholder="Ej. Querétaro"
                                       autocomplete="off"
                                       class="mt-1 w-full bg-transparent border-0 p-0 text-sm font-medium text-gray-700 dark:text-slate-300 placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block my-4 w-px bg-gradient-to-b from-transparent via-gray-200 to-transparent dark:via-slate-700"></div>

                    <div class="min-w-0 px-4 sm:px-5 py-3.5 hover:bg-blue-50/40 dark:hover:bg-slate-800/70 transition-colors duration-200 border-t md:border-t-0 border-gray-100 dark:border-slate-800 md:border-0">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-2xl
                                         bg-blue-50 ring-1 ring-blue-100 text-blue-600
                                         dark:bg-blue-500/10 dark:ring-blue-500/20 dark:text-blue-400">
                                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4.75 19.25h14.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M6.75 16.75V7.75A2 2 0 0 1 8.75 5.75h6.5a2 2 0 0 1 2 2v9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M9 9.6h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M9 12.7h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>

                            <div class="min-w-0 w-full">
                                <label class="block text-[11px] font-extrabold uppercase tracking-[0.12em] text-gray-900 dark:text-slate-100">
                                    Nombre
                                </label>
                                <input type="text"
                                       name="buscar"
                                       value="{{ request('buscar') }}"
                                       placeholder="Buscar lugar"
                                       autocomplete="off"
                                       class="mt-1 w-full bg-transparent border-0 p-0 text-sm font-medium text-gray-700 dark:text-slate-300 placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <div class="px-4 sm:px-3 py-3 md:py-0 border-t md:border-t-0 border-gray-100 dark:border-slate-800 md:border-0 flex items-center justify-end md:justify-center">
                        <button type="submit"
                                class="h-12 w-full md:w-12 rounded-2xl bg-blue-600 hover:bg-blue-700
                                       transition-all duration-200 flex items-center justify-center gap-2 md:gap-0 shadow-[0_8px_20px_rgba(37,99,235,0.28)]
                                       hover:shadow-[0_10px_24px_rgba(37,99,235,0.34)]
                                       focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                aria-label="Buscar"
                                title="Buscar">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="11" cy="11" r="6.75" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 16l3.75 3.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="md:hidden text-sm font-semibold text-white">Buscar</span>
                        </button>
                    </div>
                </div>

                @if(request()->filled('buscar') || request()->filled('city') || request()->filled('type') || request()->filled('max_price'))
                    <div class="mt-2 flex justify-end">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
                                  bg-blue-50 text-blue-700 border border-blue-100
                                  dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700
                                  hover:bg-blue-100 dark:hover:bg-slate-700 transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M5.5 5.5l9 9m0-9l-9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Limpiar filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }"
         class="hidden lg:hidden border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 py-4 space-y-2">

            <div class="mb-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 p-3">
                @auth
                    <div class="flex items-center gap-3">
                        <img class="size-10 rounded-full object-cover"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-slate-100 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                @else
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-slate-100">Bienvenido a VibeBloom</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Explora lugares y experiencias desde cualquier dispositivo.</p>
                    </div>
                @endauth
            </div>

            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                Inicio
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('places.map') }}" :active="request()->routeIs('places.map')">
                Mapa
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('places.mine') }}" :active="request()->routeIs('places.mine')">
                Mis lugares
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('place-submissions.index') }}" :active="request()->routeIs('place-submissions.*')">
                Mis aprobaciones
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('favorites.mine') }}" :active="request()->routeIs('favorites.mine')">
                Mis favoritos
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('memories.index') }}" :active="request()->routeIs('memories.*')">
                Mis recuerdos
            </x-responsive-nav-link>

            @auth
                <div class="border-t border-gray-200 dark:border-slate-700 pt-3 mt-3">
                    <x-responsive-nav-link href="{{ route('profile.show') }}">
                        Perfil
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Cerrar sesión
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="border-t border-gray-200 dark:border-slate-700 pt-3 mt-3 space-y-2">
                    <a href="{{ route('login') }}"
                       class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition">
                        Iniciar sesión
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="block w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold
                                  bg-blue-50 text-blue-800 border border-blue-100
                                  dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700
                                  hover:bg-blue-100 dark:hover:bg-slate-700 transition">
                            Registrarse
                        </a>
                    @endif
                </div>
            @endauth
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }

        #vbSearchBarWrap {
            overflow: hidden;
            max-height: 420px;
            opacity: 1;
            transition:
                max-height 180ms ease,
                opacity 140ms ease,
                border-color 140ms ease;
        }

        #vbSearchBarInner {
            will-change: transform, opacity;
            transition: transform 180ms ease, opacity 140ms ease;
            transform: translate3d(0, 0, 0);
            opacity: 1;
        }

        .vb-search-hidden {
            max-height: 0 !important;
            opacity: 0;
            border-top-color: transparent !important;
        }

        .vb-search-hidden #vbSearchBarInner {
            transform: translate3d(0, -12px, 0);
            opacity: 0;
            pointer-events: none;
        }

        @media (min-width: 768px) {
            #vbSearchBarWrap {
                max-height: 170px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchBar = document.getElementById('vbSearchBarWrap');
            if (!searchBar) return;

            let lastScrollY = window.scrollY;
            let hidden = false;
            let ticking = false;
            const threshold = 14;
            const minScroll = 80;

            function updateBar() {
                const currentScrollY = window.scrollY;
                const delta = currentScrollY - lastScrollY;

                if (Math.abs(delta) < threshold) {
                    ticking = false;
                    return;
                }

                if (currentScrollY <= 20) {
                    if (hidden) {
                        searchBar.classList.remove('vb-search-hidden');
                        hidden = false;
                    }
                    lastScrollY = currentScrollY;
                    ticking = false;
                    return;
                }

                if (delta > 0 && currentScrollY > minScroll) {
                    if (!hidden) {
                        searchBar.classList.add('vb-search-hidden');
                        hidden = true;
                    }
                } else if (delta < 0) {
                    if (hidden) {
                        searchBar.classList.remove('vb-search-hidden');
                        hidden = false;
                    }
                }

                lastScrollY = currentScrollY;
                ticking = false;
            }

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(updateBar);
                    ticking = true;
                }
            }, { passive: true });
        });
    </script>
</nav>