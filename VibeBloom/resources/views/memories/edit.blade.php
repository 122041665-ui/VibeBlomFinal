<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white dark:bg-slate-900 shadow-sm rounded-2xl p-6 border border-gray-100 dark:border-slate-800";

        $label = "font-semibold text-gray-800 dark:text-slate-100";
        $hint = "text-xs text-gray-500 dark:text-slate-400";

        $fieldBase = "w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                      text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                      focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-gray-300 dark:focus:border-slate-600 transition";

        $btnPrimary = "px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnGhost = "px-6 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition
                     active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnDanger = "px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition
                      active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";
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
                                Edición de recuerdo
                            </div>

                            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                                Editar recuerdo
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-slate-400 mt-2">
                                Actualiza la información y administra las fotos de este recuerdo.
                            </p>
                        </div>

                        <a href="{{ route('memories.index') }}" class="{{ $btnGhost }} inline-flex items-center gap-2 justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 p-4 text-sm text-red-700 dark:text-red-300 shadow-sm">
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('memories.update', $memory->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <div class="lg:col-span-2 {{ $card }}">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
                                    Información del recuerdo
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                    Mantén los datos claros, breves y fáciles de identificar.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label for="title" class="{{ $label }}">Título</label>
                                <input
                                    id="title"
                                    name="title"
                                    value="{{ old('title', $memory->title) }}"
                                    class="{{ $fieldBase }} mt-1"
                                    placeholder="Ej. Viaje a Bernal"
                                >
                                <p class="{{ $hint }} mt-1">Usa un nombre corto que te ayude a reconocerlo rápido.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="memory_date" class="{{ $label }}">Fecha</label>
                                    <input
                                        id="memory_date"
                                        type="date"
                                        name="memory_date"
                                        value="{{ old('memory_date', $memory->memory_date ? \Carbon\Carbon::parse($memory->memory_date)->format('Y-m-d') : '') }}"
                                        class="{{ $fieldBase }} mt-1"
                                    >
                                </div>

                                <div>
                                    <label for="location" class="{{ $label }}">Lugar</label>
                                    <input
                                        id="location"
                                        name="location"
                                        value="{{ old('location', $memory->location) }}"
                                        class="{{ $fieldBase }} mt-1"
                                        placeholder="Ej. Querétaro"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="description" class="{{ $label }}">Descripción</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="7"
                                    class="{{ $fieldBase }} mt-1"
                                    placeholder="Describe qué pasó, por qué fue importante o qué quieres recordar."
                                >{{ old('description', $memory->description) }}</textarea>
                                <p class="{{ $hint }} mt-1">Puedes dejarla vacía si prefieres un recuerdo más breve.</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 {{ $card }} lg:sticky lg:top-6">
                        <div class="space-y-5">
                            <div>
                                <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
                                    Fotos del recuerdo
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                    Puedes conservar las actuales o reemplazarlas por nuevas.
                                </p>
                            </div>

                            @php
                                $photos = collect($memory->photos ?? []);

                                $photosNormalized = $photos->map(function ($photo) {
                                    $url = $photo->url
                                        ?? $photo->photo_url
                                        ?? (!empty($photo->path) ? asset('storage/' . ltrim($photo->path, '/')) : null);

                                    return (object) [
                                        'id' => $photo->id ?? null,
                                        'path' => $photo->path ?? null,
                                        'url' => $url,
                                    ];
                                })->filter(fn ($photo) => !empty($photo->url))->values();
                            @endphp

                            <div>
                                <label class="{{ $label }}">Fotos actuales</label>

                                @if ($photosNormalized->count())
                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3 p-2 bg-blue-50/40 dark:bg-slate-800/50 rounded-2xl border border-blue-100 dark:border-slate-700">
                                        @foreach ($photosNormalized as $p)
                                            <img
                                                src="{{ $p->url }}"
                                                alt="Foto del recuerdo"
                                                class="w-full h-44 object-cover rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm"
                                                loading="lazy"
                                            >
                                        @endforeach
                                    </div>

                                    <p class="{{ $hint }} mt-2">
                                        Si seleccionas nuevas fotos, se reemplazarán todas las actuales.
                                    </p>
                                @else
                                    <div class="mt-2 rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 p-6 text-center">
                                        <p class="text-sm text-gray-500 dark:text-slate-400">Este recuerdo aún no tiene fotos.</p>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label for="photos" class="{{ $label }}">Reemplazar fotos</label>

                                <input id="photos" type="file" name="photos[]" accept="image/*" multiple class="hidden">

                                <label for="photos"
                                       class="{{ $fieldBase }} mt-1 cursor-pointer flex items-center justify-between hover:bg-blue-50/40 dark:hover:bg-slate-800 transition">
                                    <span id="photosLabelText" class="text-gray-600 dark:text-slate-300">Seleccionar fotos</span>
                                    <span id="photosCount" class="text-xs text-gray-500 dark:text-slate-400">0/3</span>
                                </label>

                                <p class="{{ $hint }} mt-1">Máximo 3 imágenes. Formatos recomendados: JPG, PNG o WEBP.</p>

                                <div id="preview" class="mt-3 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3"></div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="{{ $btnPrimary }} w-full">
                                    Guardar cambios
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <div class="mt-6 {{ $card }}">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
                            Zona de eliminación
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                            Eliminará este recuerdo y sus fotos asociadas de forma permanente.
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('memories.destroy', $memory->id) }}"
                          onsubmit="return confirm('¿Seguro que deseas eliminar este recuerdo? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="{{ $btnDanger }}">
                            Eliminar recuerdo
                        </button>
                    </form>
                </div>
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

    <script>
        const input = document.getElementById('photos');
        const preview = document.getElementById('preview');
        const count = document.getElementById('photosCount');
        const labelText = document.getElementById('photosLabelText');

        if (input) {
            input.addEventListener('change', () => {
                preview.innerHTML = '';

                let files = Array.from(input.files || []);
                if (files.length > 3) {
                    files = files.slice(0, 3);

                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));
                    input.files = dt.files;
                }

                count.textContent = `${files.length}/3`;
                labelText.textContent = files.length ? 'Fotos seleccionadas' : 'Seleccionar fotos';

                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = "rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow transition bg-white dark:bg-slate-900";
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-28 object-cover" alt="Vista previa">
                            <div class="h-1 w-full bg-blue-600"></div>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }
    </script>

    <div class="h-20 sm:h-24"></div>
</x-app-layout>