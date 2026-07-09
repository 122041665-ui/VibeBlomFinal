<x-app-layout>
    @php
        $container = "max-w-6xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[24px] p-5 border border-gray-100 dark:border-slate-800 backdrop-blur";
        $btnPrimary = "px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 text-sm font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $input = "w-full rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 text-sm font-semibold text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.12),transparent_34%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.14),transparent_34%)]"></div>

        <div class="{{ $container }} relative">
            <section class="{{ $card }} mb-5">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                    <div class="min-w-0">
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                            Comunidad
                        </div>
                        <h1 class="mt-3 text-3xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                            Usuarios
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
                            Encuentra perfiles públicos y sigue a personas con lugares interesantes.
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="{{ $btnGhost }} inline-flex w-full sm:w-auto items-center justify-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 18l-6-6l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Inicio
                    </a>
                </div>

                <form method="GET" action="{{ route('users.index') }}" class="mt-5 grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_auto] gap-3">
                    <input type="search" name="q" value="{{ $query }}" class="{{ $input }}" placeholder="Buscar usuario">
                    <button class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21l-4.35-4.35M10.8 18.6a7.8 7.8 0 1 1 0-15.6a7.8 7.8 0 0 1 0 15.6Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                        </svg>
                        Buscar
                    </button>
                </form>
            </section>

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

                        <article class="{{ $card }} transition hover:-translate-y-0.5 hover:border-blue-100 hover:shadow-lg dark:hover:border-blue-500/30">
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
