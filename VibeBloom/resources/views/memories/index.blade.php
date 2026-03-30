<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";

        $cardBase = "group relative bg-white dark:bg-slate-900 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-slate-800
                     transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-xl hover:border-blue-100 dark:hover:border-blue-500/30";

        $cardInner = "p-5";

        $btnPrimary = "px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnGhost = "px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition
                     active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnDanger = "px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm transition
                      active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";

        $btnChip = "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold
                    bg-blue-50 dark:bg-slate-800 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-slate-700
                    hover:bg-blue-100 dark:hover:bg-slate-700 transition
                    focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $chip = "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                 bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 border border-blue-100 dark:border-blue-500/20";

        $hint = "text-sm text-gray-600 dark:text-slate-400 mt-1";
        $title = "text-lg font-extrabold text-gray-900 dark:text-slate-100";

        $photoIconUrl = asset('images/vb-photo-icon.png');
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
                                Colección personal
                            </div>

                            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                                Mis recuerdos
                            </h1>
                            <p class="{{ $hint }}">Guarda momentos importantes con sus fotos.</p>
                        </div>

                        <a href="{{ route('memories.create') }}" class="{{ $btnPrimary }} inline-flex items-center justify-center gap-2 shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Nuevo recuerdo
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="max-w-7xl mx-auto mb-5 rounded-2xl border border-green-200 dark:border-green-500/30 bg-green-50 dark:bg-green-500/10 p-3.5 text-sm font-medium text-green-700 dark:text-green-300 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($memories->isEmpty())
                <div class="max-w-7xl mx-auto {{ $cardBase }} p-8 sm:p-10 text-center">
                    <div class="mx-auto w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 flex items-center justify-center text-blue-700 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75h-7.5A2.25 2.25 0 006 9v10.5A2.25 2.25 0 008.25 21h7.5A2.25 2.25 0 0018 18.75V9A2.25 2.25 0 0015.75 6.75z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V5.25A2.25 2.25 0 0111.25 3h1.5A2.25 2.25 0 0115 5.25v1.5" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-slate-100 mt-5">Aún no tienes recuerdos</h2>
                    <p class="text-gray-600 dark:text-slate-400 mt-2 max-w-xl mx-auto leading-6">
                        Crea el primero y conserva tus momentos importantes con sus fotos, ubicación y detalles.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('memories.create') }}" class="{{ $btnPrimary }}">Crear mi primer recuerdo</a>
                    </div>
                </div>
            @else
                <div class="max-w-7xl mx-auto mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-300 w-fit">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                        {{ $memories->total() }} {{ $memories->total() === 1 ? 'recuerdo registrado' : 'recuerdos registrados' }}
                    </div>

                    <div class="text-xs text-gray-500 dark:text-slate-400">
                        Abre un recuerdo para ver o recorrer sus fotos
                    </div>
                </div>

                <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($memories as $m)
                        @php
                            $photos = collect($m->photos ?? []);

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

                            $cover = $photosNormalized->first();

                            $date = null;
                            if (!empty($m->memory_date)) {
                                try {
                                    $date = \Carbon\Carbon::parse($m->memory_date)->format('d/m/Y');
                                } catch (\Throwable $e) {
                                    $date = null;
                                }
                            }

                            $updatedAt = null;
                            if (!empty($m->updated_at)) {
                                try {
                                    $updatedAt = \Carbon\Carbon::parse($m->updated_at)->format('d/m/Y');
                                } catch (\Throwable $e) {
                                    $updatedAt = null;
                                }
                            }

                            $photosJson = $photosNormalized->pluck('url')->values()->all();
                        @endphp

                        <div class="{{ $cardBase }}">
                            <span class="pointer-events-none absolute inset-0 z-10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <span class="absolute inset-0 bg-gradient-to-t from-blue-600/10 via-transparent to-transparent"></span>
                            </span>

                            @if ($cover)
                                <button type="button"
                                        onclick='openViewer(@json($photosJson), 0)'
                                        class="relative block w-full text-left">
                                    <img
                                        src="{{ $cover->url }}"
                                        alt="Foto del recuerdo"
                                        class="w-full h-56 object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                        loading="lazy"
                                    >

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition"></div>

                                    <div class="absolute top-3 left-3 z-20">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-white/90 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-slate-100 backdrop-blur">
                                            {{ $photosNormalized->count() }} foto{{ $photosNormalized->count() === 1 ? '' : 's' }}
                                        </span>
                                    </div>

                                    <div class="absolute bottom-3 right-3 z-20">
                                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-600/95 text-white text-sm font-semibold shadow-sm
                                                     opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition">
                                            <img src="{{ $photoIconUrl }}" alt="Fotos" class="w-4 h-4 object-contain">
                                            Ver fotos
                                        </span>
                                    </div>
                                </button>
                            @else
                                <div class="w-full h-56 bg-blue-50/40 dark:bg-slate-800/60 flex items-center justify-center">
                                    <div class="text-center text-gray-500 dark:text-slate-400">
                                        <div class="mx-auto w-14 h-14 rounded-3xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 flex items-center justify-center text-blue-700 dark:text-blue-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5h18M6 7.5V6a3 3 0 013-3h6a3 3 0 013 3v1.5M6 7.5v13.5h12V7.5" />
                                            </svg>
                                        </div>
                                        <p class="mt-3 text-sm font-semibold">Sin fotos</p>
                                        <p class="text-xs text-gray-400 dark:text-slate-500">Edita el recuerdo para agregar</p>
                                    </div>
                                </div>
                            @endif

                            <div class="{{ $cardInner }}">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="{{ $title }} transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                        {{ $m->title }}
                                    </h3>

                                    <a href="{{ route('memories.edit', $m->id) }}" class="{{ $btnChip }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 20h4l10.5-10.5a2 2 0 0 0 0-2.8l-.2-.2a2 2 0 0 0-2.8 0L5 17v3Z"
                                                  stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <path d="M13.5 6.5l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        Editar
                                    </a>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    @if ($date)
                                        <span class="{{ $chip }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-700 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v1.5M17.25 3v1.5M3.75 7.5h16.5" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M9 12h.01M12 12h.01M15 12h.01" />
                                            </svg>
                                            {{ $date }}
                                        </span>
                                    @endif

                                    @if ($m->location)
                                        <span class="{{ $chip }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-700 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6-4.35 6-10a6 6 0 10-12 0c0 5.65 6 10 6 10z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11.25a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" />
                                            </svg>
                                            {{ $m->location }}
                                        </span>
                                    @endif
                                </div>

                                @if ($m->description)
                                    <p class="text-sm text-gray-700 dark:text-slate-300 mt-3 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($m->description, 140) }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-3">Sin descripción.</p>
                                @endif

                                <div class="mt-5 flex items-center justify-between gap-3">
                                    <span class="text-xs text-gray-500 dark:text-slate-400">
                                        Actualizado: {{ $updatedAt ?? '—' }}
                                    </span>

                                    <form method="POST"
                                          action="{{ route('memories.destroy', $m->id) }}"
                                          onsubmit="return confirm('¿Eliminar este recuerdo? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="{{ $btnDanger }}">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="max-w-7xl mx-auto mt-8">
                    {{ $memories->links() }}
                </div>
            @endif
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

    <div id="viewer" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
        <div class="relative w-full max-w-5xl bg-white dark:bg-slate-900 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-slate-700">

            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <span class="font-extrabold text-gray-900 dark:text-slate-100">Fotos del recuerdo</span>
                    <span id="viewerCounter" class="text-sm text-gray-600 dark:text-slate-400"></span>
                </div>

                <button onclick="closeViewer()" class="{{ $btnGhost }}">Cerrar</button>
            </div>

            <div class="relative bg-black flex items-center justify-center">
                <button id="viewerPrev"
                        onclick="prevPhoto()"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-700
                               flex items-center justify-center shadow-sm hover:shadow transition
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-800 dark:text-slate-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <img id="viewerImage" src="" alt="Foto del recuerdo" class="max-h-[70vh] w-auto object-contain">

                <button id="viewerNext"
                        onclick="nextPhoto()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-700
                               flex items-center justify-center shadow-sm hover:shadow transition
                               focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-800 dark:text-slate-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <div id="viewerThumbs" class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-700 p-3">
                <div class="flex items-center gap-2 justify-center flex-wrap"></div>
            </div>

        </div>
    </div>

    <script>
        let viewerPhotos = [];
        let viewerIndex = 0;

        function openViewer(urls, startIndex) {
            if (!urls || urls.length === 0) return;

            viewerPhotos = urls;
            viewerIndex = startIndex || 0;

            const viewer = document.getElementById('viewer');
            viewer.classList.remove('hidden');
            viewer.classList.add('flex');

            renderViewer();
            renderThumbs();

            document.addEventListener('keydown', handleViewerKeys);
        }

        function closeViewer() {
            const viewer = document.getElementById('viewer');
            viewer.classList.add('hidden');
            viewer.classList.remove('flex');

            viewerPhotos = [];
            viewerIndex = 0;

            const image = document.getElementById('viewerImage');
            if (image) {
                image.src = '';
            }

            const counter = document.getElementById('viewerCounter');
            if (counter) {
                counter.textContent = '';
            }

            const thumbsWrap = document.querySelector('#viewerThumbs > div');
            if (thumbsWrap) {
                thumbsWrap.innerHTML = '';
            }

            document.removeEventListener('keydown', handleViewerKeys);
        }

        function nextPhoto() {
            if (!viewerPhotos.length) return;
            viewerIndex = (viewerIndex + 1) % viewerPhotos.length;
            renderViewer();
            highlightThumb();
        }

        function prevPhoto() {
            if (!viewerPhotos.length) return;
            viewerIndex = (viewerIndex - 1 + viewerPhotos.length) % viewerPhotos.length;
            renderViewer();
            highlightThumb();
        }

        function goToPhoto(index) {
            viewerIndex = index;
            renderViewer();
            highlightThumb();
        }

        function renderViewer() {
            const image = document.getElementById('viewerImage');
            image.src = viewerPhotos[viewerIndex] || '';

            const counter = document.getElementById('viewerCounter');
            counter.textContent = `${viewerIndex + 1} de ${viewerPhotos.length}`;

            const prevBtn = document.getElementById('viewerPrev');
            const nextBtn = document.getElementById('viewerNext');

            if (viewerPhotos.length <= 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
            }
        }

        function renderThumbs() {
            const thumbsWrap = document.querySelector('#viewerThumbs > div');
            if (!thumbsWrap) return;

            thumbsWrap.innerHTML = '';

            viewerPhotos.forEach((src, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow transition focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30';
                button.style.width = '72px';
                button.style.height = '54px';
                button.onclick = () => goToPhoto(index);

                const image = document.createElement('img');
                image.src = src;
                image.alt = 'Miniatura';
                image.className = 'w-full h-full object-cover';

                button.appendChild(image);
                thumbsWrap.appendChild(button);
            });

            highlightThumb();
        }

        function highlightThumb() {
            const thumbsWrap = document.querySelector('#viewerThumbs > div');
            if (!thumbsWrap) return;

            const buttons = thumbsWrap.querySelectorAll('button');
            buttons.forEach((button, index) => {
                if (index === viewerIndex) {
                    button.classList.add('ring-2', 'ring-blue-600', 'border-blue-200', 'dark:border-blue-500');
                } else {
                    button.classList.remove('ring-2', 'ring-blue-600', 'border-blue-200', 'dark:border-blue-500');
                }
            });
        }

        function handleViewerKeys(event) {
            const viewer = document.getElementById('viewer');
            if (viewer.classList.contains('hidden')) return;

            if (event.key === 'Escape') closeViewer();
            if (event.key === 'ArrowRight') nextPhoto();
            if (event.key === 'ArrowLeft') prevPhoto();
        }

        document.addEventListener('click', function (event) {
            const viewer = document.getElementById('viewer');
            if (viewer.classList.contains('hidden')) return;
            if (event.target === viewer) closeViewer();
        });
    </script>

    <div class="h-20 sm:h-24"></div>
</x-app-layout>