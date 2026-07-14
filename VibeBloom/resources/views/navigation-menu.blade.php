<nav x-data="{
        open: false,
        darkMode: localStorage.getItem('theme') === 'dark'
     }"
     x-init="$watch('darkMode', value => {
        localStorage.setItem('theme', value ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', value);
     })"
     class="bg-white/88 dark:bg-slate-950/88 backdrop-blur border-b border-gray-100 dark:border-slate-800 sticky top-0 z-50 transition-colors duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 sm:h-[4.5rem] items-center">
            @guest
                <div class="w-full flex items-center justify-center">
                    <a href="{{ route('dashboard') }}"
                       class="group inline-flex items-center justify-center transition-transform duration-300 ease-out hover:scale-[1.01]"
                       aria-label="VibeBloom">
                        <img
                            src="{{ asset('images/Vibe.png') }}"
                            alt="VibeBloom"
                            class="block h-16 sm:h-[4.5rem] w-auto object-contain
                                   transition-all duration-300
                                   drop-shadow-sm
                                   group-hover:opacity-95" />
                    </a>
                </div>
            @else

            <div class="flex items-center shrink-0">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}"
                       class="group inline-flex items-center transition-transform duration-300 ease-out hover:scale-[1.02]">
                        <img
                            src="{{ asset('images/Vibe.png') }}"
                            alt="VibeBloom"
                            class="block h-14 sm:h-16 w-auto max-w-[176px] sm:max-w-[196px] object-contain
                                   transition-all duration-300
                                   drop-shadow-sm
                                   group-hover:opacity-95" />
                    </a>
                </div>

                <div class="hidden space-x-1.5 sm:ms-6 sm:flex items-center">

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

                    <x-nav-link href="{{ Route::has('users.index') ? route('users.index') : url('/usuarios') }}" :active="request()->routeIs('users.*')"
                        class="px-3 py-2 rounded-xl text-sm font-semibold transition
                               hover:bg-blue-50 hover:text-blue-700
                               dark:hover:bg-slate-800 dark:hover:text-blue-400
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30
                               {{ request()->routeIs('users.*')
                                    ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-slate-800 dark:text-blue-400 dark:border-slate-700'
                                    : 'text-gray-700 dark:text-slate-300' }}">
                        Comunidad
                    </x-nav-link>

                </div>
            </div>

            <form action="{{ route('places.index') }}" method="GET"
                  class="hidden xl:flex flex-1 max-w-xl mx-4">
                <div class="w-full grid grid-cols-[minmax(140px,1.5fr)_108px_108px_84px_42px] items-center rounded-xl border border-gray-200/90 dark:border-slate-700/90 bg-white/95 dark:bg-slate-900/95 shadow-sm overflow-hidden">
                    <label class="flex items-center gap-2 px-4 py-2.5 border-r border-gray-100 dark:border-slate-800 min-w-0">
                        <svg class="w-4 h-4 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="6.75" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 16l3.75 3.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="sr-only">Buscar lugar</span>
                        <input type="text"
                               name="buscar"
                               value="{{ request('buscar') }}"
                               placeholder="Buscar lugares"
                               autocomplete="off"
                               class="w-full min-w-0 bg-transparent border-0 p-0 text-sm font-semibold text-gray-800 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-0">
                    </label>

                    <label class="px-3 py-2.5 border-r border-gray-100 dark:border-slate-800">
                        <span class="sr-only">Tipo</span>
                        <select name="type"
                                class="w-full bg-transparent border-0 p-0 pr-7 text-sm font-semibold text-gray-700 dark:text-slate-300 focus:outline-none focus:ring-0 cursor-pointer">
                            <option value="">Tipo</option>
                            <option value="RESTAURANTE" @selected(request('type') === 'RESTAURANTE')>Restaurante</option>
                            <option value="CAFETERIA" @selected(request('type') === 'CAFETERIA')>Cafetería</option>
                            <option value="BAR" @selected(request('type') === 'BAR')>Bar</option>
                            <option value="ANTRO" @selected(request('type') === 'ANTRO')>Antro</option>
                            <option value="PARQUE" @selected(request('type') === 'PARQUE')>Parque</option>
                            <option value="PLAZA" @selected(request('type') === 'PLAZA')>Plaza</option>
                            <option value="CENTRO COMERCIAL" @selected(request('type') === 'CENTRO COMERCIAL')>Centro comercial</option>
                            <option value="MIRADOR" @selected(request('type') === 'MIRADOR')>Mirador</option>
                            <option value="MUSEO" @selected(request('type') === 'MUSEO')>Museo</option>
                            <option value="OTRO" @selected(request('type') === 'OTRO')>Otro</option>
                        </select>
                    </label>

                    <label class="px-3 py-2.5 border-r border-gray-100 dark:border-slate-800">
                        <span class="sr-only">Ciudad</span>
                        <input type="text"
                               name="city"
                               value="{{ request('city') }}"
                               placeholder="Ciudad"
                               autocomplete="off"
                               class="w-full bg-transparent border-0 p-0 text-sm font-semibold text-gray-700 dark:text-slate-300 placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-0">
                    </label>

                    <label class="px-3 py-2.5 border-r border-gray-100 dark:border-slate-800">
                        <span class="sr-only">Precio máximo</span>
                        <input type="number"
                               name="max_price"
                               value="{{ request('max_price') }}"
                               placeholder="MXN"
                               step="1"
                               min="0"
                               class="w-full bg-transparent border-0 p-0 text-sm font-semibold text-gray-700 dark:text-slate-300 placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-0">
                    </label>

                    <button type="submit"
                            class="m-1.5 h-[2.125rem] w-[2.125rem] rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                            aria-label="Buscar"
                            title="Buscar">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="6.75" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 16l3.75 3.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </form>

            @if(request()->filled('buscar') || request()->filled('city') || request()->filled('type') || request()->filled('max_price'))
                <a href="{{ route('places.index') }}"
                   class="hidden xl:inline-flex items-center justify-center rounded-lg border border-blue-100 dark:border-slate-700 bg-blue-50 dark:bg-slate-800 px-3 py-2 text-xs font-bold text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-slate-700 transition">
                    Limpiar
                </a>
            @endif

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @php
                    $navNotifications = Auth::user()
                        ->notifications()
                        ->with('actor')
                        ->latest()
                        ->take(6)
                        ->get();
                    $navUnreadNotifications = Auth::user()
                        ->notifications()
                        ->whereNull('read_at')
                        ->count();
                @endphp

                <div class="relative">
                    <x-dropdown align="right" width="96" contentClasses="bg-white/95 dark:bg-slate-900/95 dark:text-slate-100">
                        <x-slot name="trigger">
                            <button class="relative inline-flex items-center justify-center rounded-xl p-2.5
                                           border border-gray-200 dark:border-slate-700
                                           bg-white/90 dark:bg-slate-900 shadow-sm
                                           hover:shadow-md transition
                                           focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                    aria-label="Abrir notificaciones">
                                <svg class="size-6 text-gray-600 dark:text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>

                                @if ($navUnreadNotifications > 0)
                                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[10px] font-extrabold text-white ring-2 ring-white dark:ring-slate-950">
                                        {{ $navUnreadNotifications > 9 ? '9+' : $navUnreadNotifications }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-96 max-w-[calc(100vw-2rem)]">
                                <div class="flex items-center justify-between gap-3 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-gray-900 dark:text-slate-100">Notificaciones</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">{{ $navUnreadNotifications }} sin leer</p>
                                    </div>

                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-blue-700 dark:text-blue-300 hover:underline">
                                            Marcar leídas
                                        </button>
                                    </form>
                                </div>

                                <div class="border-t border-gray-100 dark:border-slate-800"></div>

                                <div class="max-h-96 overflow-y-auto p-2">
                                    @forelse ($navNotifications as $notification)
                                        <a href="{{ $notification->url ?: route('notifications.index') }}"
                                           class="group flex gap-3 rounded-2xl p-3 transition hover:bg-blue-50 dark:hover:bg-slate-800">
                                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300' : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300' }}">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </span>

                                            <span class="min-w-0 flex-1">
                                                <span class="flex items-start justify-between gap-2">
                                                    <span class="truncate text-sm font-bold text-gray-900 dark:text-slate-100 group-hover:text-blue-700 dark:group-hover:text-blue-300">
                                                        {{ $notification->title }}
                                                    </span>
                                                    @if (!$notification->read_at)
                                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                                                    @endif
                                                </span>

                                                @if ($notification->body)
                                                    <span class="mt-0.5 block line-clamp-2 text-xs leading-5 text-gray-500 dark:text-slate-400">
                                                        {{ $notification->body }}
                                                    </span>
                                                @endif

                                                <span class="mt-1 block text-[11px] font-semibold text-gray-400 dark:text-slate-500">
                                                    {{ $notification->created_at?->diffForHumans() }}
                                                </span>
                                            </span>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center">
                                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-slate-100">Sin notificaciones</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Aquí aparecerá la actividad importante.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="border-t border-gray-100 dark:border-slate-800 p-2">
                                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-center rounded-xl px-3 py-2 text-sm font-bold text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                        Ver todas
                                    </a>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <div class="ms-1 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center justify-center rounded-xl p-2.5
                                               border border-gray-200 dark:border-slate-700
                                               bg-white/90 dark:bg-slate-900 shadow-sm
                                               hover:shadow-md transition
                                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                                        aria-label="Abrir menú de usuario">

                                    <svg class="size-6 text-gray-600 dark:text-slate-300" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="bg-white dark:bg-slate-900 dark:text-slate-100">
                                    <div class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <img class="size-10 rounded-full object-cover"
                                                 src="{{ Auth::user()->display_photo_url }}"
                                                 alt="{{ Auth::user()->name }}" />
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-gray-900 dark:text-slate-100 truncate">
                                                    {{ Auth::user()->name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-slate-400 truncate">
                                                    {{ Auth::user()->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-slate-700"></div>

                                    <x-dropdown-link href="{{ route('places.mine') }}">
                                        Perfil
                                    </x-dropdown-link>

                                    <div class="border-t border-gray-200 dark:border-slate-700"></div>

                                    <button type="button"
                                            @click="darkMode = !darkMode"
                                            class="w-full flex items-center justify-between px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-800 transition">
                                        <span>Modo oscuro</span>
                                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400" x-text="darkMode ? 'Activo' : 'Inactivo'"></span>
                                    </button>

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
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden gap-2">
                <a href="{{ route('notifications.index') }}"
                   class="relative inline-flex items-center justify-center p-2 rounded-xl
                          border border-gray-200 dark:border-slate-700
                          bg-white/90 dark:bg-slate-900 shadow-sm
                          text-gray-500 dark:text-slate-300 hover:text-gray-700 dark:hover:text-white hover:bg-white dark:hover:bg-slate-800
                          transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30"
                   aria-label="Abrir notificaciones">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>

                    @if (($navUnreadNotifications ?? 0) > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[10px] font-extrabold text-white ring-2 ring-white dark:ring-slate-950">
                            {{ $navUnreadNotifications > 9 ? '9+' : $navUnreadNotifications }}
                        </span>
                    @endif
                </a>

                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-xl
                               border border-gray-200 dark:border-slate-700
                               bg-white/90 dark:bg-slate-900 shadow-sm
                               text-gray-500 dark:text-slate-300 hover:text-gray-700 dark:hover:text-white hover:bg-white dark:hover:bg-slate-800
                               transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">

                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open }"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>

                        <path :class="{ 'hidden': ! open }"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endguest

        </div>
    </div>

    @auth
    <div :class="{ 'block': open, 'hidden': !open }"
         class="hidden sm:hidden border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-950 transition-colors duration-300">
        <div class="px-4 py-4 space-y-2">

            <form action="{{ route('places.index') }}" method="GET"
                  class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900 p-3 space-y-3 shadow-sm">
                <label class="block">
                    <span class="text-xs font-extrabold uppercase text-gray-500 dark:text-slate-400">Buscar</span>
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           placeholder="Nombre del lugar"
                           autocomplete="off"
                           class="mt-1 w-full rounded-xl border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm font-semibold text-gray-800 dark:text-slate-100 focus:border-blue-400 focus:ring-blue-200 dark:focus:border-blue-500 dark:focus:ring-blue-500/30">
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-xs font-extrabold uppercase text-gray-500 dark:text-slate-400">Tipo</span>
                        <select name="type"
                                class="mt-1 w-full rounded-xl border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm font-semibold text-gray-800 dark:text-slate-100 focus:border-blue-400 focus:ring-blue-200 dark:focus:border-blue-500 dark:focus:ring-blue-500/30">
                            <option value="">Todos</option>
                            <option value="RESTAURANTE" @selected(request('type') === 'RESTAURANTE')>Restaurante</option>
                            <option value="CAFETERIA" @selected(request('type') === 'CAFETERIA')>Cafetería</option>
                            <option value="BAR" @selected(request('type') === 'BAR')>Bar</option>
                            <option value="ANTRO" @selected(request('type') === 'ANTRO')>Antro</option>
                            <option value="PARQUE" @selected(request('type') === 'PARQUE')>Parque</option>
                            <option value="PLAZA" @selected(request('type') === 'PLAZA')>Plaza</option>
                            <option value="CENTRO COMERCIAL" @selected(request('type') === 'CENTRO COMERCIAL')>Centro comercial</option>
                            <option value="MIRADOR" @selected(request('type') === 'MIRADOR')>Mirador</option>
                            <option value="MUSEO" @selected(request('type') === 'MUSEO')>Museo</option>
                            <option value="OTRO" @selected(request('type') === 'OTRO')>Otro</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-xs font-extrabold uppercase text-gray-500 dark:text-slate-400">Precio</span>
                        <input type="number"
                               name="max_price"
                               value="{{ request('max_price') }}"
                               placeholder="MXN"
                               step="1"
                               min="0"
                               class="mt-1 w-full rounded-xl border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm font-semibold text-gray-800 dark:text-slate-100 focus:border-blue-400 focus:ring-blue-200 dark:focus:border-blue-500 dark:focus:ring-blue-500/30">
                    </label>
                </div>

                <label class="block">
                    <span class="text-xs font-extrabold uppercase text-gray-500 dark:text-slate-400">Ciudad</span>
                    <input type="text"
                           name="city"
                           value="{{ request('city') }}"
                           placeholder="Ej. Querétaro"
                           autocomplete="off"
                           class="mt-1 w-full rounded-xl border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm font-semibold text-gray-800 dark:text-slate-100 focus:border-blue-400 focus:ring-blue-200 dark:focus:border-blue-500 dark:focus:ring-blue-500/30">
                </label>

                <div class="grid grid-cols-[1fr_auto] gap-3">
                    @if(request()->filled('buscar') || request()->filled('city') || request()->filled('type') || request()->filled('max_price'))
                        <a href="{{ route('places.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-blue-100 dark:border-slate-700 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-bold text-blue-700 dark:text-blue-400">
                            Limpiar
                        </a>
                    @else
                        <span></span>
                    @endif

                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition">
                        Buscar
                    </button>
                </div>
            </form>

            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                Inicio
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('places.map') }}" :active="request()->routeIs('places.map')">
                Mapa
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ Route::has('users.index') ? route('users.index') : url('/usuarios') }}" :active="request()->routeIs('users.*')">
                Comunidad
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('notifications.index') }}" :active="request()->routeIs('notifications.*')">
                Notificaciones
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('places.mine') }}" :active="request()->routeIs('places.mine')">
                Perfil
            </x-responsive-nav-link>

                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-4">
                    <div class="px-3 text-sm font-semibold text-gray-800 dark:text-slate-100">{{ Auth::user()->name }}</div>
                    <div class="px-3 text-xs text-gray-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
                </div>

                <button type="button"
                        @click="darkMode = !darkMode"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-slate-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800 focus:outline-none focus:text-gray-800 focus:bg-gray-50 dark:focus:bg-slate-800 transition">
                    <span>Modo oscuro</span>
                    <span class="text-xs font-semibold text-gray-500 dark:text-slate-400" x-text="darkMode ? 'Activo' : 'Inactivo'"></span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>

        </div>
    </div>
    @endauth

</nav>
