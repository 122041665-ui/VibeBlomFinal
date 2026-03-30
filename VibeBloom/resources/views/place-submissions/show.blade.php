<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-28";
        $card = "bg-white dark:bg-slate-900 shadow rounded-2xl border border-gray-100 dark:border-slate-800";
        $btnGhost = "inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnDanger = "inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-700 dark:text-red-300 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-red-100 dark:border-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";
        $hint = "text-sm text-gray-500 dark:text-slate-400 mt-1";
        $label = "text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400";
        $value = "mt-1 text-base font-semibold text-gray-900 dark:text-white";
        $defaultPhoto = asset('images/default.jpg');
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
        <div class="{{ $container }}">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Revisar solicitud</h1>
                    <p class="{{ $hint }}">Vista previa de la información enviada a aprobación.</p>
                </div>

                <a href="{{ route('place-submissions.index') }}" class="{{ $btnGhost }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Volver
                </a>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                <div class="xl:col-span-8">
                    <div class="{{ $card }} p-6">
                        @php
                            $photos = $placeSubmission->photos ?? collect();
                            $statusText = match($placeSubmission->status) {
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                                default => 'Pendiente de aprobación',
                            };

                            $statusClasses = match($placeSubmission->status) {
                                'approved' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/20',
                                'rejected' => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-300 border border-red-100 dark:border-red-500/20',
                                default => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-500/20',
                            };
                        @endphp

                        @if($photos->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($photos as $photo)
                                    <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950">
                                        <img src="{{ asset('storage/' . $photo->path) }}"
                                             alt="{{ $placeSubmission->name }}"
                                             class="w-full h-64 object-cover"
                                             onerror="this.onerror=null;this.src='{{ $defaultPhoto }}';">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 p-8 text-center">
                                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8 11l2.5 2.5L14 10l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9 9h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">No hay fotos cargadas</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">La solicitud no incluye imágenes.</p>
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20">
                                Solicitud
                            </span>

                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                {{ $statusText }}
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="{{ $label }}">Nombre</p>
                                <p class="{{ $value }}">
                                    {{ $placeSubmission->name }}
                                </p>
                            </div>

                            <div>
                                <p class="{{ $label }}">Tipo</p>
                                <p class="{{ $value }}">
                                    {{ $placeSubmission->type }}
                                </p>
                            </div>

                            <div>
                                <p class="{{ $label }}">Ciudad</p>
                                <p class="{{ $value }}">
                                    {{ $placeSubmission->city ?: 'Sin ciudad' }}
                                </p>
                            </div>

                            <div>
                                <p class="{{ $label }}">Dirección</p>
                                <p class="{{ $value }}">
                                    {{ $placeSubmission->address ?: 'Sin dirección' }}
                                </p>
                            </div>

                            <div>
                                <p class="{{ $label }}">Precio</p>
                                <p class="{{ $value }}">
                                    MXN ${{ number_format((float) $placeSubmission->price, 2) }}
                                </p>
                            </div>

                            <div>
                                <p class="{{ $label }}">Calificación</p>

                                <div class="mt-2 flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= (int) $placeSubmission->rating)
                                            <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.962a1 1 0 00.95.69h4.168c.969 0 1.371 1.24.588 1.81l-3.374 2.451a1 1 0 00-.364 1.118l1.287 3.962c.3.921-.755 1.688-1.538 1.118l-3.374-2.452a1 1 0 00-1.176 0l-3.374 2.452c-.783.57-1.838-.197-1.538-1.118l1.287-3.962a1 1 0 00-.364-1.118L2.045 9.39c-.783-.57-.38-1.81.588-1.81h4.168a1 1 0 00.95-.69l1.286-3.962z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24"
                                                 aria-hidden="true">
                                                <path stroke-width="2"
                                                      d="M11.049 2.927l1.902 3.962a1 1 0 00.95.69h4.168l-3.374 2.451a1 1 0 00-.364 1.118l1.287 3.962-3.374-2.452a1 1 0 00-1.176 0l-3.374 2.452 1.287-3.962a1 1 0 00-.364-1.118L2.045 7.579h4.168a1 1 0 00.95-.69l1.286-3.962z"/>
                                            </svg>
                                        @endif
                                    @endfor

                                    <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-slate-200">
                                        {{ max(0, min(5, (int) $placeSubmission->rating)) }}/5
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="{{ $label }}">Descripción</p>

                            <div class="mt-2 rounded-2xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 p-4 text-sm text-gray-700 dark:text-slate-200 leading-relaxed">
                                {{ $placeSubmission->description ?: 'Sin descripción.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-4">
                    <div class="{{ $card }} p-6 xl:sticky xl:top-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            Estado de la solicitud
                        </h2>

                        <div class="mt-4 space-y-4">
                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
                                <p class="{{ $label }}">
                                    Estado actual
                                </p>

                                <p class="mt-2 text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $statusText }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
                                <p class="{{ $label }}">
                                    Envío a revisión
                                </p>

                                <p class="mt-2 text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $placeSubmission->sent_to_flask ? 'Simulado correctamente' : 'Pendiente' }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                    {{ $placeSubmission->sent_to_flask_at ? $placeSubmission->sent_to_flask_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                </p>
                            </div>

                            @if(!is_null($placeSubmission->lat) && !is_null($placeSubmission->lng))
                                <div class="rounded-2xl border border-gray-200 dark:border-slate-700 p-4">
                                    <p class="{{ $label }}">Coordenadas</p>

                                    <div class="mt-2 space-y-1 text-sm text-gray-700 dark:text-slate-200">
                                        <p><span class="font-semibold">Lat:</span> {{ $placeSubmission->lat }}</p>
                                        <p><span class="font-semibold">Lng:</span> {{ $placeSubmission->lng }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('place-submissions.destroy', $placeSubmission) }}"
                              method="POST"
                              class="mt-6">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="{{ $btnDanger }} w-full justify-center"
                                onclick="return confirm('¿Seguro que quieres eliminar esta solicitud?')">
                                Eliminar solicitud
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>