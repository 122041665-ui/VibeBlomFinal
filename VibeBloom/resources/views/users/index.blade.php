<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[24px] p-5 border border-gray-100 dark:border-slate-800 backdrop-blur";
        $btnPrimary = "px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 text-sm font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $input = "w-full rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 text-sm font-semibold text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
    @endphp

    <div class="min-h-screen vb-soft-page relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.12),transparent_34%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.14),transparent_34%)]"></div>

        <div class="{{ $container }} relative">
            <x-flash-messages />
            <section class="relative mb-6 overflow-hidden rounded-[30px] border border-blue-100 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-6 text-white shadow-[0_24px_70px_rgba(37,99,235,.24)] sm:p-8 dark:border-blue-500/20">
                <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                    <div class="min-w-0">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-50 backdrop-blur">
                            Comunidad VibeBloom
                        </div>
                        <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">
                            Descubre personas y nuevos lugares
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100">
                            Encuentra perfiles públicos y sigue a personas con lugares interesantes.
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 18l-6-6l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Inicio
                    </a>
                </div>

                <form method="GET" action="{{ Route::has('users.index') ? route('users.index') : url('/usuarios') }}" class="mt-5 grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_auto] gap-3">
                    <input type="search" name="q" value="{{ $query }}" class="w-full rounded-2xl border border-white/20 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-lg outline-none ring-blue-200 focus:ring-4" maxlength="100" autocomplete="off" placeholder="Busca por nombre o correo" aria-label="Buscar usuario por nombre o correo">
                    <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950/80 px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-slate-950">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21l-4.35-4.35M10.8 18.6a7.8 7.8 0 1 1 0-15.6a7.8 7.8 0 0 1 0 15.6Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                        </svg>
                        Buscar
                    </button>
                </form>
            </section>

            @error('q')
                <p class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</p>
            @enderror

            @if ($users->isEmpty())
                <section class="{{ $card }} text-center">
                    <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">No hay usuarios públicos</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">Prueba con otra búsqueda.</p>
                </section>
            @else
                <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($users as $user)
                        @php
                            $isFollowing = in_array((int) $user->id, $followingIds, true);
                            $placesCount = (int) ($user->platform_places_count ?? $user->places_count ?? 0);
                        @endphp

                        <article class="group relative overflow-hidden rounded-[26px] border border-blue-100/80 bg-gradient-to-br from-white to-blue-50/60 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl dark:border-slate-800 dark:from-slate-900 dark:to-blue-950/30">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 via-sky-400 to-indigo-500"></div>
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->display_photo_url }}" alt="{{ $user->name }}" class="h-16 w-16 shrink-0 rounded-2xl object-cover border border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-800">

                                <div class="min-w-0 flex-1">
                                    <h2 class="truncate text-lg font-extrabold text-gray-900 dark:text-slate-100">{{ $user->name }}</h2>
                                    <p class="mt-1 text-sm font-semibold text-blue-700 dark:text-blue-300">
                                        {{ $placesCount }} {{ $placesCount === 1 ? 'lugar' : 'lugares' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <a href="{{ route('users.show', $user) }}" class="{{ $btnGhost }} inline-flex items-center justify-center">
                                    <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0-4-4a4 4 0 0 0 4 4ZM4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    Ver perfil
                                </a>

                                @if ($isFollowing)
                                    <form method="POST" action="{{ route('users.unfollow', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="{{ $btnGhost }} w-full inline-flex items-center justify-center" type="submit">
                                            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Siguiendo
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('users.follow', $user) }}">
                                        @csrf
                                        <button class="{{ $btnPrimary }} w-full inline-flex items-center justify-center" type="submit">
                                            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            Seguir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
