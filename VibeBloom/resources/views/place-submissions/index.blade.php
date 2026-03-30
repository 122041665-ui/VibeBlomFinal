<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/90 dark:bg-slate-900/90 backdrop-blur shadow-sm rounded-[28px] border border-gray-100 dark:border-slate-800";

        $btnPrimary = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnDanger = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-700 dark:text-red-300 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-red-100 dark:border-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";

        $hint = "text-sm text-gray-600 dark:text-slate-400 mt-1";
        $title = "text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight";
        $defaultPhoto = asset('images/default.jpg');
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.10),transparent_55%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.10),transparent_55%)]"></div>

        <div class="{{ $container }}">
            <div class="mb-6">
                <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur shadow-sm p-5 sm:p-6 lg:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                Solicitudes enviadas
                            </div>

                            <h1 class="{{ $title }} mt-4">Mis aprobaciones</h1>
                            <p class="{{ $hint }}">Aquí aparecen las solicitudes enviadas.</p>
                        </div>

                        <a href="{{ route('places.create') }}" class="{{ $btnPrimary }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Nueva solicitud
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 dark:border-green-500/30 bg-green-50 dark:bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-300 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($submissions->count())
                <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300 w-fit">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                        {{ $submissions->count() }} {{ $submissions->count() === 1 ? 'solicitud registrada' : 'solicitudes registradas' }}
                    </div>

                    <div class="text-xs text-gray-500 dark:text-slate-400">
                        Consulta el estado y elimina las que ya no necesites
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($submissions as $submission)
                        @php
                            $firstPhoto = $submission->photos->first();
                            $rating = max(0, min(5, (int) ($submission->rating ?? 0)));

                            $statusText = match($submission->status) {
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                                default => 'Pendiente',
                            };

                            $statusClasses = match($submission->status) {
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-500/15 dark:text-green-300 border border-green-200 dark:border-green-500/20',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-300 border border-red-200 dark:border-red-500/20',
                                default => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300 border border-amber-200 dark:border-amber-500/20',
                            };
                        @endphp

                        <article class="group relative">
                            <span class="pointer-events-none absolute inset-0 z-10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <span class="absolute inset-0 rounded-[28px] bg-gradient-to-t from-blue-600/10 via-transparent to-transparent"></span>
                            </span>

                            <div class="{{ $card }} overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                                <div class="relative">
                                    @if($firstPhoto)
                                        <img src="{{ asset('storage/' . $firstPhoto->path) }}"
                                             alt="{{ $submission->name }}"
                                             class="w-full h-52 object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                                             onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';">
                                    @else
                                        <div class="w-full h-52 bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                                            <div class="text-center px-4">
                                                <div class="mx-auto w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-300 mb-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                                        <path d="M8 11l2.5 2.5L14 10l2 2"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-700 dark:text-slate-200">Sin foto</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-tight transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                        {{ $submission->name }}
                                    </h2>

                                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                                        {{ $submission->type }} · {{ $submission->city }}
                                    </p>

                                    <div class="mt-4 space-y-3 text-sm">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-gray-500 dark:text-slate-400">Calificación</span>

                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $rating)
                                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.962a1 1 0 00.95.69h4.168c.969 0 1.371 1.24.588 1.81l-3.374 2.451a1 1 0 00-.364 1.118l1.287 3.962c.3.921-.755 1.688-1.538 1.118l-3.374-2.452a1 1 0 00-1.176 0l-3.374 2.452c-.783.57-1.838-.197-1.538-1.118l1.287-3.962a1 1 0 00-.364-1.118L2.045 9.39c-.783-.57-.38-1.81.588-1.81h4.168a1 1 0 00.95-.69l1.286-3.962z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300 dark:text-slate-600"
                                                             fill="none"
                                                             stroke="currentColor"
                                                             viewBox="0 0 24 24"
                                                             aria-hidden="true">
                                                            <path stroke-width="2"
                                                                  d="M11.049 2.927l1.902 3.962a1 1 0 00.95.69h4.168l-3.374 2.451a1 1 0 00-.364 1.118l1.287 3.962-3.374-2.452a1 1 0 00-1.176 0l-3.374 2.452 1.287-3.962a1 1 0 00-.364-1.118L2.045 7.579h4.168a1 1 0 00.95-.69l1.286-3.962z"/>
                                                        </svg>
                                                    @endif
                                                @endfor
                                                <span class="ml-1 font-semibold text-gray-700 dark:text-slate-200">{{ $rating }}/5</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-gray-500 dark:text-slate-400">Precio</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                MXN ${{ number_format((float) $submission->price, 2) }}
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-gray-500 dark:text-slate-400">Enviado</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                {{ $submission->sent_to_flask ? 'Sí' : 'No' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <form action="{{ route('place-submissions.destroy', $submission) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="{{ $btnDanger }} w-full"
                                                    onclick="return confirm('¿Seguro que quieres eliminar esta solicitud?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $submissions->links() }}
                </div>
            @else
                <div class="{{ $card }} p-10 text-center">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12h6M12 9v6"/>
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Aún no tienes solicitudes
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-2">
                        Cuando envíes un lugar aparecerá aquí.
                    </p>

                    <a href="{{ route('places.create') }}" class="{{ $btnPrimary }} mt-5">
                        Crear primera solicitud
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="h-20 sm:h-24"></div>
</x-app-layout>