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
                     border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
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
                                Nuevo recuerdo
                            </div>

                            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                                Crear recuerdo
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-slate-400 mt-2">
                                Guarda un momento importante con su información y sus fotos.
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
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('memories.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <div class="lg:col-span-2 {{ $card }}">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div>
                                <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
                                    Información del recuerdo
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                    Agrega un título claro, la fecha, el lugar y una breve descripción.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="{{ $label }}">Título</label>
                                <input
                                    name="title"
                                    value="{{ old('title') }}"
                                    class="{{ $fieldBase }} mt-1"
                                    placeholder="Ej. Viaje a Bernal"
                                >
                                <p class="{{ $hint }} mt-1">Usa un nombre corto que te ayude a identificarlo fácilmente.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="{{ $label }}">Fecha</label>
                                    <input
                                        type="date"
                                        name="memory_date"
                                        value="{{ old('memory_date') }}"
                                        class="{{ $fieldBase }} mt-1"
                                    >
                                </div>

                                <div>
                                    <label class="{{ $label }}">Lugar</label>
                                    <input
                                        name="location"
                                        value="{{ old('location') }}"
                                        class="{{ $fieldBase }} mt-1"
                                        placeholder="Ej. Querétaro"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="{{ $label }}">Descripción</label>
                                <textarea
                                    name="description"
                                    rows="6"
                                    class="{{ $fieldBase }} mt-1"
                                    placeholder="Cuenta el recuerdo, qué pasó o por qué fue importante..."
                                >{{ old('description') }}</textarea>
                                <p class="{{ $hint }} mt-1">Puedes dejarla breve o más detallada, según prefieras.</p>
                            </div>
                        </div>
                    </div>

                    <div class="{{ $card }} lg:sticky lg:top-6">
                        <div class="space-y-5">
                            <div>
                                <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
                                    Fotos del recuerdo
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                    Puedes subir hasta 3 imágenes para acompañar este momento.
                                </p>
                            </div>

                            <div>
                                <label class="{{ $label }}">Seleccionar fotos</label>

                                <input id="photos" type="file" name="photos[]" accept="image/*" multiple class="hidden">

                                <label for="photos"
                                       class="{{ $fieldBase }} mt-2 cursor-pointer flex items-center justify-between hover:bg-blue-50/40 dark:hover:bg-slate-800 transition">
                                    <span id="photosLabelText" class="text-gray-600 dark:text-slate-300">
                                        Seleccionar fotos
                                    </span>
                                    <span id="photosCount" class="text-xs text-gray-500 dark:text-slate-400">0/3</span>
                                </label>

                                <p class="{{ $hint }} mt-2">
                                    Máximo 3 fotos. Formatos recomendados: JPG, PNG o WEBP.
                                </p>
                            </div>

                            <div id="preview" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3"></div>

                            <div class="pt-2">
                                <button type="submit" class="{{ $btnPrimary }} w-full">
                                    Guardar recuerdo
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <a href="{{ url('/ai/voz') }}"
       class="fixed bottom-6 right-6 z-50 group"
       aria-label="Abrir Vibe IA"
       title="Vibe IA">
        <span class="absolute -inset-1 rounded-2xl bg-blue-600/20 blur-lg opacity-0 group-hover:opacity-100 transition"></span>
        <span class="relative inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-blue-600 text-white shadow-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" stroke="currentColor" stroke-width="1.8"/>
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

        let selectedFiles = [];

        function syncInputFiles() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        function updateUI() {
            count.textContent = `${selectedFiles.length}/3`;
            labelText.textContent = selectedFiles.length ? 'Fotos seleccionadas' : 'Seleccionar fotos';
        }

        function renderPreview() {
            preview.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = "relative rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-900 group";

                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-28 object-cover" alt="Vista previa">
                        <div class="h-1 w-full bg-blue-600"></div>
                        <button
                            type="button"
                            data-remove-index="${index}"
                            class="absolute top-2 right-2 inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/95 dark:bg-slate-900/95 text-red-600 dark:text-red-400 shadow-sm border border-gray-200 dark:border-slate-700 hover:bg-red-50 dark:hover:bg-slate-800 transition"
                            aria-label="Eliminar foto"
                            title="Eliminar foto"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;

                    preview.appendChild(div);

                    const removeBtn = div.querySelector('[data-remove-index]');
                    removeBtn.addEventListener('click', () => {
                        selectedFiles.splice(index, 1);
                        syncInputFiles();
                        updateUI();
                        renderPreview();
                    });
                };

                reader.readAsDataURL(file);
            });
        }

        input.addEventListener('change', () => {
            let incomingFiles = Array.from(input.files || []);

            if (!incomingFiles.length) {
                selectedFiles = [];
                syncInputFiles();
                updateUI();
                renderPreview();
                return;
            }

            selectedFiles = incomingFiles.slice(0, 3);

            syncInputFiles();
            updateUI();
            renderPreview();
        });
    </script>

    <div class="h-20 sm:h-24"></div>
</x-app-layout>