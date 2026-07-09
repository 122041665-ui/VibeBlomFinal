<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[24px] p-5 border border-gray-100 dark:border-slate-800 backdrop-blur";
        $btnPrimary = "px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 text-sm font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $defaultPhoto = asset('images/vibebloom.png');
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.12),transparent_34%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.14),transparent_34%)]"></div>

        <div class="{{ $container }} relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <aside class="lg:col-span-4 xl:col-span-3 space-y-5">
                    <section class="{{ $card }} lg:sticky lg:top-24">
                        <img
                            src="{{ $user->display_photo_url }}"
                            alt="{{ $user->name }}"
                            class="h-24 w-24 rounded-3xl object-cover border border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-800"
                            onerror="this.onerror=null;this.src='{{ asset('images/vibebloom.png') }}';"
                        >

                        <h1 class="mt-4 text-2xl font-extrabold text-gray-900 dark:text-slate-100 break-words">{{ $user->name }}</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400 break-words">
                            {{ $canViewPublicContent ? $user->email : 'Informacion privada' }}
                        </p>

                        <div class="mt-5 grid grid-cols-3 gap-2">
                            <div class="rounded-2xl border border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 p-3">
                                <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $places->count() }}</p>
                                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Lugares</p>
                            </div>
                            <div class="rounded-2xl border border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 p-3">
                                <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $user->followers_count }}</p>
                                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Seguidores</p>
                            </div>
                            <div class="rounded-2xl border border-gray-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 p-3">
                                <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">{{ $user->following_count }}</p>
                                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400">Siguiendo</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            @if ($isOwnProfile)
                                <a href="{{ route('places.mine') }}" class="{{ $btnPrimary }} inline-flex w-full items-center justify-center gap-2">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0-4-4a4 4 0 0 0 4 4ZM4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    Ir a mi perfil
                                </a>
                            @elseif ($isFollowing)
                                <form method="POST" action="{{ route('users.unfollow', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="{{ $btnGhost }} w-full inline-flex items-center justify-center gap-2" type="submit">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Siguiendo
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('users.follow', $user) }}">
                                    @csrf
                                    <button class="{{ $btnPrimary }} w-full inline-flex items-center justify-center gap-2" type="submit">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Seguir
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('users.index') }}" class="{{ $btnGhost }} inline-flex w-full items-center justify-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M15 18l-6-6l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Comunidad
                            </a>
                        </div>

                        @unless ($user->profile_is_public)
                            <div class="mt-5 rounded-2xl border border-amber-100 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 p-4 text-sm font-semibold text-amber-800 dark:text-amber-300">
                                Perfil privado
                            </div>
                        @endunless
                    </section>
                </aside>

                <main class="lg:col-span-8 xl:col-span-9 min-w-0 space-y-5">
                    <section class="{{ $card }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-slate-100">Lugares publicados</h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">Lugares creados o compartidos por {{ $user->name }}.</p>
                            </div>
                            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                {{ $places->count() }} {{ $places->count() === 1 ? 'lugar' : 'lugares' }}
                            </span>
                        </div>
                    </section>

                    @if (!$canViewPublicContent)
                        <section class="{{ $card }} text-center">
                            <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">Este perfil es privado</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">Los lugares de este usuario no están disponibles públicamente.</p>
                        </section>
                    @elseif ($places->isEmpty())
                        <section class="{{ $card }} text-center">
                            <p class="text-xl font-extrabold text-gray-900 dark:text-slate-100">Sin lugares publicados</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">Cuando publique lugares, aparecerán aquí.</p>
                        </section>
                    @else
                        <section class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4">
                            @foreach ($places as $place)
                                @php
                                    $isApiPlace = (bool) ($place->is_api_place ?? false);
                                    $isSubmission = $place instanceof \App\Models\PlaceSubmission;
                                    $placeId = $place->id ?? null;
                                    $photo = $defaultPhoto;

                                    if ($isApiPlace) {
                                        $photo = $place->photo ?: $defaultPhoto;
                                    } elseif ($isSubmission) {
                                        $submissionPhoto = $place->photos->first();
                                        $photo = $submissionPhoto?->url ?: $defaultPhoto;
                                    } else {
                                        $photo = $place->photo_url ?? $defaultPhoto;
                                    }
                                @endphp

                                <article class="{{ $card }} p-0 overflow-hidden transition hover:-translate-y-0.5 hover:border-blue-100 hover:shadow-lg dark:hover:border-blue-500/30">
                                    <div class="h-44 w-full overflow-hidden bg-slate-50 dark:bg-slate-800">
                                        <img
                                            src="{{ $photo }}"
                                            alt="Foto de {{ $place->name }}"
                                            class="h-full w-full object-cover"
                                            onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';this.classList.remove('object-cover');this.classList.add('object-contain','p-8');"
                                        >
                                    </div>

                                    <div class="p-4">
                                        <h3 class="truncate text-lg font-extrabold text-gray-900 dark:text-slate-100">{{ $place->name }}</h3>
                                        <p class="mt-1 truncate text-sm text-gray-600 dark:text-slate-400">{{ $place->city ?? 'Sin ciudad' }}</p>

                                        @if (($isApiPlace || !$isSubmission) && $placeId)
                                            <a href="{{ route('places.show', $placeId) }}" class="{{ $btnGhost }} mt-4 inline-flex w-full items-center justify-center gap-2">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 21s7-4.6 7-11a7 7 0 1 0-14 0c0 6.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.8"/>
                                                </svg>
                                                Ver lugar
                                            </a>
                                        @else
                                            <span class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-emerald-100 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300">Publicado</span>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </section>
                    @endif
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
