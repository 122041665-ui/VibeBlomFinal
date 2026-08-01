<x-app-layout>

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet" />

    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/90 dark:bg-slate-900/90 backdrop-blur shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800";

        $label = "font-semibold text-gray-800 dark:text-slate-100";
        $hint = "text-xs text-gray-500 dark:text-slate-400 mt-1";

        $fieldBase = "w-full mt-1 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                      text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                      focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-blue-300 dark:focus:border-blue-500 transition";

        $fileHidden = "hidden";
        $fileFake = "w-full mt-1 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                     cursor-pointer flex items-center justify-between gap-3
                     text-gray-900 dark:text-slate-100
                     hover:bg-blue-50/40 dark:hover:bg-slate-800
                     focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-200 dark:focus-within:ring-blue-500/30 transition";

        $btnPrimary = "inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm
                       transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnGhost = "inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400
                     font-semibold rounded-xl shadow-sm transition active:scale-[0.99]
                     border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $pill = "inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700
                 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300";
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }}">
            <div class="mb-6">
                <div class="rounded-[30px] border border-white/80 dark:border-slate-800 bg-white/88 dark:bg-slate-900/88 backdrop-blur shadow-[0_22px_70px_rgba(15,23,42,0.10)] p-5 sm:p-6 lg:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="{{ $pill }}">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                                Alta de lugar
                            </div>

                            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                Agregar un nuevo lugar
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-slate-400 mt-2">
                                Completa los datos. Un administrador revisará la solicitud antes de publicarla.
                            </p>
                        </div>

                        <a href="{{ route('places.mine') }}" class="{{ $btnGhost }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                            Volver al perfil
                        </a>
                    </div>
                </div>
            </div>

            <x-flash-messages />

            <form action="{{ route('place-submissions.store') }}" method="POST" enctype="multipart/form-data" id="placeForm" novalidate>
                @csrf

                <div id="clientValidationErrors" class="mb-5 hidden rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"></div>

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                    <div class="xl:col-span-7">
                        <div class="{{ $card }}">
                            <div class="flex items-start justify-between gap-4 mb-6">
                                <div>
                                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
                                        Información general
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                        Captura los datos principales del lugar para que la revisión sea más clara.
                                    </p>
                                </div>

                                <span class="{{ $pill }} hidden sm:inline-flex">
                                    Formulario
                                </span>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="{{ $label }}">Nombre del lugar</label>
                                    <input type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           oninvalid="this.setCustomValidity('Completa este campo')"
                                           oninput="this.setCustomValidity('')"
                                           class="{{ $fieldBase }}"
                                           placeholder="Ej. Puerta La Victoria">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="{{ $label }}">Tipo de lugar</label>
                                        <select name="type"
                                                required
                                                oninvalid="this.setCustomValidity('Selecciona un tipo de lugar')"
                                                oninput="this.setCustomValidity('')"
                                                class="{{ $fieldBase }} pr-10">
                                            <option value="" disabled {{ old('type') ? '' : 'selected' }}>Selecciona una opción</option>
                                            <option value="Restaurante" {{ old('type') === 'Restaurante' ? 'selected' : '' }}>Restaurante</option>
                                            <option value="Cafetería" {{ old('type') === 'Cafetería' ? 'selected' : '' }}>Cafetería</option>
                                            <option value="Bar" {{ old('type') === 'Bar' ? 'selected' : '' }}>Bar</option>
                                            <option value="Antro" {{ old('type') === 'Antro' ? 'selected' : '' }}>Antro</option>
                                            <option value="Parque" {{ old('type') === 'Parque' ? 'selected' : '' }}>Parque</option>
                                            <option value="Mirador" {{ old('type') === 'Mirador' ? 'selected' : '' }}>Mirador</option>
                                            <option value="Museo" {{ old('type') === 'Museo' ? 'selected' : '' }}>Museo</option>
                                            <option value="Plaza" {{ old('type') === 'Plaza' ? 'selected' : '' }}>Plaza</option>
                                            <option value="Centro comercial" {{ old('type') === 'Centro comercial' ? 'selected' : '' }}>Centro comercial</option>
                                            <option value="Otro" {{ old('type') === 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        <p class="{{ $hint }}">Ayuda a organizar mejor el contenido dentro de la plataforma.</p>
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Calificación</label>

                                        @php $currentRating = old('rating', 0); @endphp
                                        <input type="hidden" name="rating" id="rating" value="{{ $currentRating }}" required min="1" max="5">

                                        <div class="{{ $fieldBase }} flex items-center justify-between">
                                            <div id="starRating" class="flex items-center gap-1 select-none">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button"
                                                            class="star-btn p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition"
                                                            data-value="{{ $i }}"
                                                            aria-label="Calificar {{ $i }} estrellas">
                                                        <svg class="star w-6 h-6 text-gray-300 dark:text-slate-600 transition" viewBox="0 0 24 24" fill="none">
                                                            <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"
                                                                  stroke="currentColor" stroke-width="1.5" />
                                                        </svg>
                                                    </button>
                                                @endfor
                                            </div>

                                            <span id="ratingText" class="text-sm font-semibold text-gray-600 dark:text-slate-300">Selecciona una opción</span>
                                        </div>

                                        <p id="ratingError" class="{{ $hint }}">Obligatorio · 5 = imperdible, 3 = bien, 1 = no volvería.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="{{ $label }}">Precio aprox. por persona</label>

                                    <div class="relative mt-1">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 dark:text-slate-400 font-semibold text-sm">
                                            MXN
                                        </span>

                                        <input type="number"
                                               name="price"
                                               value="{{ old('price') }}"
                                               required
                                               oninvalid="this.setCustomValidity('Completa este campo')"
                                               oninput="this.setCustomValidity('')"
                                               class="{{ $fieldBase }} pl-14"
                                               placeholder="0.00"
                                               step="0.01">
                                    </div>
                                </div>

                                <div>
                                    <label class="{{ $label }}">Fotos del lugar (hasta 3)</label>

                                    <input type="file"
                                           id="photos"
                                           name="photos[]"
                                           accept="image/jpeg,image/png,image/webp"
                                           multiple
                                           required
                                           oninvalid="showPhotoRequiredAlert(this)"
                                           oninput="this.setCustomValidity('')"
                                           class="{{ $fileHidden }}">

                                    <label for="photos" class="{{ $fileFake }}">
                                        <span class="text-gray-700 dark:text-slate-200 font-medium">Seleccionar fotos</span>
                                        <span id="filesText" class="text-gray-500 dark:text-slate-400 text-sm">0/3</span>
                                    </label>

                                    <p class="{{ $hint }}">Obligatorio: 1 a 3 fotos JPG, PNG o WEBP, máximo 5 MB cada una.</p>
                                    <p id="photosError" class="mt-2 hidden text-sm font-semibold text-red-600 dark:text-red-400" role="alert"></p>

                                    <div id="photoPreview" class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3"></div>
                                </div>

                                <div>
                                    <label class="{{ $label }}">Descripción</label>
                                    <textarea name="description"
                                              rows="4"
                                              required
                                              minlength="20"
                                              maxlength="1000"
                                              class="{{ $fieldBase }}"
                                              placeholder="Describe el ambiente, lo que lo hace especial, etc.">{{ old('description') }}</textarea>
                                    <div class="mt-2 flex items-center justify-between gap-3">
                                        <p class="{{ $hint }} !mt-0">Obligatoria, entre 20 y 1000 caracteres. Se revisará antes de enviarse.</p>
                                        <span id="descriptionCount" class="text-xs font-semibold text-gray-500 dark:text-slate-400">0/1000</span>
                                    </div>
                                </div>

                                <div class="flex justify-end xl:hidden">
                                    <button type="submit" class="{{ $btnPrimary }}" id="submitPlaceBtnMobile">
                                        Enviar a aprobación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="xl:col-span-5">
                        <div class="{{ $card }} xl:sticky xl:top-6">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">Ubicación del lugar</h2>
                                    <p class="{{ $hint }}">Selecciona el estado, escribe la dirección y coloca el marcador en el mapa.</p>
                                </div>

                                <span class="{{ $pill }}">
                                    Mapa
                                </span>
                            </div>

                            <div class="relative mt-4">
                                <label class="text-sm {{ $label }}">Estado</label>

                                <select
                                       id="cityInput"
                                       name="city"
                                       required
                                       class="{{ $fieldBase }}">
                                    <option value="">Selecciona un estado</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->name }}" data-code="{{ $state->code }}" @selected(old('city') === $state->name)>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden" id="cityPlaceId" name="city_place_id" value="{{ old('city_place_id','') }}">

                                <div id="cityDropdown" class="hidden"><div id="cityList"></div></div>

                                <p class="{{ $hint }}">Catálogo oficial de las 32 entidades federativas de México.</p>
                            </div>

                            <div id="map" class="w-full h-80 mt-4 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-950 overflow-hidden"></div>

                            <input type="hidden" id="lat" name="lat" value="{{ old('lat') }}">
                            <input type="hidden" id="lng" name="lng" value="{{ old('lng') }}">

                            <div class="relative mt-4">
                                <label class="text-sm {{ $label }}">Dirección</label>

                                <input type="text"
                                       id="addressInput"
                                       name="address"
                                       value="{{ old('address','') }}"
                                       autocomplete="off"
                                       class="{{ $fieldBase }}"
                                       placeholder="Ej. Av. Constituyentes 120, Centro">

                                <div id="addressDropdown"
                                     class="hidden absolute z-30 mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-lg overflow-hidden">
                                    <div id="addressList" class="max-h-64 overflow-auto"></div>
                                </div>

                                <p class="{{ $hint }}">Puedes elegir una sugerencia o dar clic directamente en el mapa.</p>
                            </div>

                            <div class="hidden xl:flex justify-end mt-6">
                                <button type="submit" class="{{ $btnPrimary }}" id="submitPlaceBtn">
                                    Enviar a aprobación
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
        <span class="relative inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-blue-600 text-white shadow-lg hover:bg-blue-700 active:scale-[0.98] transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 13.25a2.25 2.25 0 1 0 0-4.5a2.25 2.25 0 0 0 0 4.5Z" fill="currentColor"/>
            </svg>
            <span class="font-semibold">Vibe IA</span>
        </span>
    </a>

    <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

    <script>
        const MAPBOX_TOKEN = @json(config('services.mapbox.token'));
        const SHOULD_RESTORE_PHOTOS = @json($errors->any() || session('error'));
        const PHOTO_DRAFT_KEY = 'new-place-photos';

        if (!MAPBOX_TOKEN) {
            console.error('Mapbox token no configurado: config("services.mapbox.token")');
        }

        const canUseMapbox = Boolean(MAPBOX_TOKEN && window.mapboxgl);

        if (canUseMapbox) {
            mapboxgl.accessToken = MAPBOX_TOKEN;
        }

        const cityInput = document.getElementById('cityInput');
        const cityPlaceId = document.getElementById('cityPlaceId');
        const cityDropdown = document.getElementById('cityDropdown');
        const cityList = document.getElementById('cityList');

        const addressInput = document.getElementById('addressInput');
        const addressDropdown = document.getElementById('addressDropdown');
        const addressList = document.getElementById('addressList');

        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');

        const photosInput = document.getElementById('photos');
        const previewContainer = document.getElementById('photoPreview');
        const filesText = document.getElementById('filesText');
        const photosError = document.getElementById('photosError');
        const descriptionInput = document.querySelector('textarea[name="description"]');
        const descriptionCount = document.getElementById('descriptionCount');

        const ratingInput = document.getElementById('rating');
        const ratingText = document.getElementById('ratingText');
        const starButtons = document.querySelectorAll('.star-btn');

        const placeForm = document.getElementById('placeForm');

        const mapContainer = document.getElementById('map');
        let map = null;

        let marker = null;
        let cityFeature = null;
        let cityTimer = null;
        let addrTimer = null;
        let selectedFiles = Array.from(photosInput.files || []);

        function openPhotoDraftDb() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open('vibebloom-form-drafts', 1);
                request.onupgradeneeded = () => request.result.createObjectStore('drafts');
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async function savePhotoDraft() {
            try {
                const db = await openPhotoDraftDb();
                const transaction = db.transaction('drafts', 'readwrite');
                transaction.objectStore('drafts').put(selectedFiles, PHOTO_DRAFT_KEY);
                transaction.oncomplete = () => db.close();
            } catch (_) {}
        }

        async function restorePhotoDraft() {
            try {
                const db = await openPhotoDraftDb();
                const request = db.transaction('drafts').objectStore('drafts').get(PHOTO_DRAFT_KEY);
                request.onsuccess = () => {
                    if (SHOULD_RESTORE_PHOTOS && Array.isArray(request.result)) {
                        selectedFiles = request.result;
                        syncInputFiles();
                        renderPhotoPreview();
                    }
                    db.close();
                };
            } catch (_) {}
        }

        async function clearPhotoDraft() {
            try {
                const db = await openPhotoDraftDb();
                const transaction = db.transaction('drafts', 'readwrite');
                transaction.objectStore('drafts').delete(PHOTO_DRAFT_KEY);
                transaction.oncomplete = () => db.close();
            } catch (_) {}
        }

        function showPhotoRequiredAlert(input) {
            input.setCustomValidity('Agrega al menos una foto del lugar.');
            photosError.textContent = 'Agrega al menos una foto antes de enviar la solicitud.';
            photosError.classList.remove('hidden');
            document.querySelector('label[for="photos"]')?.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            document.querySelector('label[for="photos"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function setMarker(lng, lat, flyZoom = 16) {
            latInput.value = lat;
            lngInput.value = lng;

            if (!map) return;

            if (marker) marker.remove();
            marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);
            map.flyTo({ center: [lng, lat], zoom: flyZoom, essential: true });
        }

        async function reverseGeocode(lng, lat) {
            if (!MAPBOX_TOKEN) return null;

            const params = { types: 'address', limit: '1' };

            if (cityFeature?.center) {
                params.proximity = `${cityFeature.center[0]},${cityFeature.center[1]}`;
            }

            const base = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json`;
            const urlParams = new URLSearchParams({
                access_token: MAPBOX_TOKEN,
                language: 'es',
                country: 'mx',
                ...params
            });

            const res = await fetch(`${base}?${urlParams.toString()}`);
            if (!res.ok) return null;

            const data = await res.json();
            return data?.features?.[0]?.place_name || null;
        }

        function syncInputFiles() {
            if (typeof DataTransfer === 'undefined') return;

            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            photosInput.files = dt.files;
        }

        function setFilesCounter(total) {
            filesText.textContent = `${total}/3`;
        }

        function renderPhotoPreview() {
            previewContainer.innerHTML = '';
            setFilesCounter(selectedFiles.length);

            selectedFiles.forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const card = document.createElement('div');
                    card.className = "relative rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-900";

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = "Vista previa";
                    img.className = "w-full h-40 object-cover";

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = "absolute top-2 right-2 w-8 h-8 rounded-full bg-black/70 hover:bg-black/85 text-white flex items-center justify-center shadow-md transition";
                    removeBtn.setAttribute('aria-label', 'Eliminar foto');
                    removeBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    `;

                    removeBtn.addEventListener('click', () => {
                        selectedFiles.splice(index, 1);
                        syncInputFiles();
                        renderPhotoPreview();
                        savePhotoDraft();
                    });

                    card.appendChild(img);
                    card.appendChild(removeBtn);
                    previewContainer.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        }

        photosInput.addEventListener('change', () => {
            let files = Array.from(photosInput.files || []);

            photosError.classList.add('hidden');
            photosError.textContent = '';
            document.querySelector('label[for="photos"]')?.classList.remove('border-red-400', 'ring-2', 'ring-red-100');

            if (files.length > 3) {
                photosError.textContent = 'Solo puedes subir hasta 3 fotos. Se conservaron las primeras 3.';
                photosError.classList.remove('hidden');
                files = files.slice(0, 3);
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const invalid = files.find(file => !allowedTypes.includes(file.type) || file.size > 5 * 1024 * 1024);
            if (invalid) {
                photosError.textContent = `“${invalid.name}” no es válida. Usa JPG, PNG o WEBP de máximo 5 MB.`;
                photosError.classList.remove('hidden');
                files = files.filter(file => allowedTypes.includes(file.type) && file.size <= 5 * 1024 * 1024);
            }

            selectedFiles = files;
            syncInputFiles();
            renderPhotoPreview();
            savePhotoDraft();
            photosInput.setCustomValidity('');
        });

        syncInputFiles();
        renderPhotoPreview();
        if (SHOULD_RESTORE_PHOTOS) restorePhotoDraft(); else clearPhotoDraft();

        function updateDescriptionCount() {
            descriptionCount.textContent = `${descriptionInput.value.length}/1000`;
        }
        descriptionInput.addEventListener('input', updateDescriptionCount);
        updateDescriptionCount();

        function setStars(value) {
            const currentValue = Math.max(0, Math.min(5, parseInt(value || 0, 10)));

            starButtons.forEach(btn => {
                const starVal = parseInt(btn.dataset.value, 10);
                const svg = btn.querySelector('svg');
                const path = svg?.querySelector('path');

                if (starVal <= currentValue) {
                    svg.classList.add('text-yellow-500');
                    svg.classList.remove('text-gray-300', 'dark:text-slate-600');
                    if (path) path.setAttribute('fill', 'currentColor');
                } else {
                    svg.classList.remove('text-yellow-500');
                    svg.classList.add('text-gray-300', 'dark:text-slate-600');
                    if (path) path.setAttribute('fill', 'none');
                }
            });

            ratingText.textContent = currentValue >= 1 ? `${currentValue}/5` : 'Selecciona una opción';
        }

        starButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                ratingInput.value = btn.dataset.value;
                ratingInput.setCustomValidity('');
                document.getElementById('ratingError')?.classList.remove('text-red-600', 'dark:text-red-400', 'font-semibold');
                setStars(ratingInput.value);
            });

            btn.addEventListener('mouseenter', () => setStars(btn.dataset.value));
        });

        document.getElementById('starRating')?.addEventListener('mouseleave', () => {
            setStars(ratingInput.value);
        });

        setStars(ratingInput.value);

        placeForm?.addEventListener('submit', (event) => {
            if (Number(ratingInput.value) < 1) {
                event.preventDefault();
                ratingInput.setCustomValidity('Selecciona de 1 a 5 estrellas.');
                const error = document.getElementById('ratingError');
                error?.classList.add('text-red-600', 'dark:text-red-400', 'font-semibold');
                error?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                starButtons[0]?.focus();
            }
        });

        if (canUseMapbox) {
            map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [-100.3899, 20.5888],
                zoom: 5
            });

            map.on('load', () => {
                setTimeout(() => map.resize(), 120);
            });

            map.on('click', async (e) => {
                const { lng, lat } = e.lngLat;
                setMarker(lng, lat, 16);

                const addr = await reverseGeocode(lng, lat);
                if (addr) {
                    addressInput.value = addr;
                }
            });
        } else if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="h-full w-full flex items-center justify-center p-6 text-center bg-blue-50/60 dark:bg-slate-800/60">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">Mapa no disponible</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Configura MAPBOX_TOKEN para seleccionar ubicación.</p>
                    </div>
                </div>
            `;
        }

        function openDD(dropdown) {
            dropdown.classList.remove('hidden');
        }

        function closeDD(dropdown) {
            dropdown.classList.add('hidden');
        }

        async function geocode(text, params = {}) {
            if (!text || !MAPBOX_TOKEN) return null;

            const base = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(text)}.json`;
            const urlParams = new URLSearchParams({
                access_token: MAPBOX_TOKEN,
                autocomplete: 'true',
                limit: '8',
                language: 'es',
                country: 'mx',
                ...params
            });

            const res = await fetch(`${base}?${urlParams.toString()}`);
            if (!res.ok) return null;

            return await res.json();
        }

        function renderButtons(listEl, features, onPick) {
            listEl.innerHTML = '';

            if (!features || !features.length) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-sm text-gray-500 dark:text-slate-400';
                empty.textContent = 'No hay coincidencias.';
                listEl.appendChild(empty);
                return;
            }

            features.forEach((feature) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-800 transition';
                btn.textContent = feature.place_name;

                btn.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    onPick(feature);
                });

                listEl.appendChild(btn);
            });
        }

        function normalizeCityName(feature) {
            const text = (feature?.text || '').trim();
            if (text) return text;

            const place = (feature?.place_name || '').split(',')[0] || '';
            return place.trim();
        }

        function invalidateCitySelection() {
            cityFeature = null;
            cityPlaceId.value = '';
        }

        async function updateCityMatches() {
            const query = (cityInput.value || '').trim();
            if (query.length < 2) {
                closeDD(cityDropdown);
                return;
            }

            const data = await geocode(query, { types: 'place' });
            const features = data?.features || [];

            renderButtons(cityList, features, (feature) => {
                cityFeature = feature;
                cityPlaceId.value = feature.id || '';
                cityInput.value = normalizeCityName(feature);
                closeDD(cityDropdown);

                const [lng, lat] = feature.center;
                latInput.value = lat;
                lngInput.value = lng;

                if (map) {
                    map.flyTo({ center: [lng, lat], zoom: 12, essential: true });
                }

                if ((addressInput.value || '').trim().length >= 3) {
                    updateAddressMatches();
                }
            });

            openDD(cityDropdown);
        }

        async function selectState() {
            const option = cityInput.options[cityInput.selectedIndex];
            cityPlaceId.value = option?.dataset?.code || '';
            cityInput.setCustomValidity(cityInput.value ? '' : 'Selecciona un estado.');
            cityFeature = cityInput.value ? { text: cityInput.value, place_name: cityInput.value } : null;

            if (!cityInput.value) return;
            const data = await geocode(`${cityInput.value}, México`, { types: 'region', limit: '1' });
            const feature = data?.features?.[0];
            if (feature?.center) {
                cityFeature = feature;
                if (map) map.flyTo({ center: feature.center, zoom: 7, essential: true });
            }
        }

        cityInput.addEventListener('change', selectState);
        if (cityInput.value) {
            selectState();
        }

        async function updateAddressMatches() {
            const address = (addressInput.value || '').trim();

            if (address.length < 3) {
                closeDD(addressDropdown);
                return;
            }

            if (!cityFeature || !cityPlaceId.value) {
                closeDD(addressDropdown);
                return;
            }

            const cityName = normalizeCityName(cityFeature);
            const combined = cityName ? `${address}, ${cityName}` : address;

            const params = { types: 'address', limit: '8' };

            if (cityFeature?.center) {
                params.proximity = `${cityFeature.center[0]},${cityFeature.center[1]}`;
            }

            if (cityFeature?.bbox && Array.isArray(cityFeature.bbox) && cityFeature.bbox.length === 4) {
                params.bbox = cityFeature.bbox.join(',');
            }

            const data = await geocode(combined, params);
            const features = data?.features || [];

            renderButtons(addressList, features, (feature) => {
                addressInput.value = feature.place_name;
                closeDD(addressDropdown);

                const [lng, lat] = feature.center;
                setMarker(lng, lat, 16);
            });

            openDD(addressDropdown);
        }

        addressInput.addEventListener('focus', () => {
            if ((addressInput.value || '').trim().length >= 3) {
                updateAddressMatches();
            }
        });

        addressInput.addEventListener('input', () => {
            clearTimeout(addrTimer);
            addrTimer = setTimeout(updateAddressMatches, 220);
        });

        addressInput.addEventListener('keydown', async (e) => {
            if (e.key !== 'Enter') return;

            e.preventDefault();

            const address = (addressInput.value || '').trim();
            if (address.length < 3) return;

            if (!cityFeature || !cityPlaceId.value) {
                cityInput.focus();
                cityInput.setCustomValidity('Primero selecciona un estado.');
                cityInput.reportValidity();
                return;
            }

            cityInput.setCustomValidity('');

            const cityName = normalizeCityName(cityFeature);
            const combined = cityName ? `${address}, ${cityName}` : address;

            const params = { types: 'address', limit: '1' };

            if (cityFeature?.center) {
                params.proximity = `${cityFeature.center[0]},${cityFeature.center[1]}`;
            }

            if (cityFeature?.bbox && Array.isArray(cityFeature.bbox) && cityFeature.bbox.length === 4) {
                params.bbox = cityFeature.bbox.join(',');
            }

            const data = await geocode(combined, params);
            const feature = data?.features?.[0];

            if (feature?.center) {
                addressInput.value = feature.place_name;
                setMarker(feature.center[0], feature.center[1], 16);
            }
        });

        document.addEventListener('click', (e) => {
            if (!cityDropdown.contains(e.target) && e.target !== cityInput) {
                closeDD(cityDropdown);
            }

            if (!addressDropdown.contains(e.target) && e.target !== addressInput) {
                closeDD(addressDropdown);
            }
        });

        placeForm.addEventListener('submit', (e) => {
            const nameInput = placeForm.querySelector('[name="name"]');
            const typeInput = placeForm.querySelector('[name="type"]');
            const priceInput = placeForm.querySelector('[name="price"]');
            const validationBox = document.getElementById('clientValidationErrors');
            const validationErrors = [];

            if (!(nameInput.value || '').trim()) validationErrors.push('Escribe el nombre del lugar.');
            if (!(typeInput.value || '').trim()) validationErrors.push('Selecciona un tipo de lugar.');
            if (!(priceInput.value || '').trim() || Number(priceInput.value) < 0) validationErrors.push('Indica un precio válido, igual o mayor que cero.');
            if ((descriptionInput.value || '').trim().length < 20) validationErrors.push('La descripción debe tener al menos 20 caracteres.');

            if (validationErrors.length) {
                e.preventDefault();
                validationBox.innerHTML = `<strong>Revisa el formulario:</strong><ul class="mt-2 list-disc pl-5">${validationErrors.map(message => `<li>${message}</li>`).join('')}</ul>`;
                validationBox.classList.remove('hidden');
                validationBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            validationBox.classList.add('hidden');
            const hasCity = !!(cityPlaceId.value || '').trim();
            const lat = (latInput.value || '').trim();
            const lng = (lngInput.value || '').trim();

            if (!hasCity) {
                e.preventDefault();
                cityInput.setCustomValidity('Selecciona un estado.');
                cityInput.reportValidity();
                return;
            }

            if (Number(ratingInput.value) < 1) {
                e.preventDefault();
                const error = document.getElementById('ratingError');
                error.textContent = 'Selecciona una calificación de 1 a 5 estrellas antes de continuar.';
                error.classList.add('text-red-600', 'dark:text-red-400', 'font-semibold');
                error.scrollIntoView({ behavior: 'smooth', block: 'center' });
                starButtons[0]?.focus();
                return;
            }

            cityInput.setCustomValidity('');

            if (!lat || !lng) {
                e.preventDefault();
                alert('Selecciona una ubicación en el mapa para guardar latitud y longitud.');
                return;
            }

            if (selectedFiles.length < 1) {
                e.preventDefault();
                photosInput.setCustomValidity('Sube al menos 1 foto (máx. 3)');
                photosError.textContent = 'Agrega al menos una foto antes de enviar la solicitud.';
                photosError.classList.remove('hidden');
                document.querySelector('label[for="photos"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            photosInput.setCustomValidity('');

            placeForm.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                button.textContent = 'Enviando solicitud…';
            });
        });
    </script>

    <div class="h-20 sm:h-24"></div>

</x-app-layout>
