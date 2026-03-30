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

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.10),transparent_55%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.10),transparent_55%)]"></div>

        <div class="{{ $container }}">
            <div class="mb-6">
                <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur shadow-sm p-5 sm:p-6 lg:p-7">
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
                                Completa los datos para enviar tu lugar a revisión.
                            </p>
                        </div>

                        <a href="{{ route('places.mine') }}" class="{{ $btnGhost }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                            Volver a mis lugares
                        </a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 max-w-7xl mx-auto rounded-2xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 p-4 text-sm text-red-700 dark:text-red-300 shadow-sm">
                    <p class="font-semibold">Corrige lo siguiente:</p>
                    <ul class="mt-2 list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('place-submissions.store') }}" method="POST" enctype="multipart/form-data" id="placeForm">
                @csrf

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
                                        <input type="hidden" name="rating" id="rating" value="{{ $currentRating }}">

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

                                            <span id="ratingText" class="text-sm font-semibold text-gray-600 dark:text-slate-300"></span>
                                        </div>

                                        <p class="{{ $hint }}">5 = imperdible, 3 = bien, 1 = no volvería.</p>
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
                                           accept="image/*"
                                           multiple
                                           required
                                           oninvalid="this.setCustomValidity('Sube al menos 1 foto (máx. 3)')"
                                           oninput="this.setCustomValidity('')"
                                           class="{{ $fileHidden }}">

                                    <label for="photos" class="{{ $fileFake }}">
                                        <span class="text-gray-700 dark:text-slate-200 font-medium">Seleccionar fotos</span>
                                        <span id="filesText" class="text-gray-500 dark:text-slate-400 text-sm">0/3</span>
                                    </label>

                                    <p class="{{ $hint }}">Puedes subir de 1 a 3 fotos. Vista previa abajo. Puedes eliminar cualquiera antes de enviarlo.</p>

                                    <div id="photoPreview" class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3"></div>
                                </div>

                                <div>
                                    <label class="{{ $label }}">Descripción</label>
                                    <textarea name="description"
                                              rows="4"
                                              class="{{ $fieldBase }}"
                                              placeholder="Describe el ambiente, lo que lo hace especial, etc.">{{ old('description') }}</textarea>
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
                                    <p class="{{ $hint }}">Selecciona la ciudad, ajusta la dirección y coloca el marcador en el mapa.</p>
                                </div>

                                <span class="{{ $pill }}">
                                    Mapa
                                </span>
                            </div>

                            <div class="relative mt-4">
                                <label class="text-sm {{ $label }}">Ciudad</label>

                                <input type="text"
                                       id="cityInput"
                                       name="city"
                                       value="{{ old('city','') }}"
                                       required
                                       autocomplete="off"
                                       class="{{ $fieldBase }}"
                                       placeholder="Escribe una ciudad (Ej. Querétaro, CDMX...)">

                                <input type="hidden" id="cityPlaceId" name="city_place_id" value="{{ old('city_place_id','') }}">

                                <div id="cityDropdown"
                                     class="hidden absolute z-30 mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-lg overflow-hidden">
                                    <div id="cityList" class="max-h-64 overflow-auto"></div>
                                </div>

                                <p class="{{ $hint }}">Solo se aceptan ciudades reales seleccionadas desde la lista.</p>
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

    <div id="approvalToast"
         class="fixed top-5 right-5 z-[9999] hidden items-center gap-3 rounded-2xl border border-green-200 dark:border-green-500/30 bg-white dark:bg-slate-900 px-4 py-3 shadow-lg">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-slate-100">Aprobación enviada</p>
            <p class="text-xs text-gray-500 dark:text-slate-400">Tu lugar fue enviado correctamente para revisión.</p>
        </div>
    </div>

    <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

    <script>
        const MAPBOX_TOKEN = @json(config('services.mapbox.token'));

        if (!MAPBOX_TOKEN) {
            console.error('Mapbox token no configurado: config("services.mapbox.token")');
        }

        mapboxgl.accessToken = MAPBOX_TOKEN || '';

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

        const ratingInput = document.getElementById('rating');
        const ratingText = document.getElementById('ratingText');
        const starButtons = document.querySelectorAll('.star-btn');

        const placeForm = document.getElementById('placeForm');
        const approvalToast = document.getElementById('approvalToast');

        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [-100.3899, 20.5888],
            zoom: 5
        });

        let marker = null;
        let cityFeature = null;
        let cityTimer = null;
        let addrTimer = null;
        let isSubmittingApproved = false;
        let selectedFiles = Array.from(photosInput.files || []);

        function setMarker(lng, lat, flyZoom = 16) {
            latInput.value = lat;
            lngInput.value = lng;

            if (marker) marker.remove();
            marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);

            map.flyTo({
                center: [lng, lat],
                zoom: flyZoom,
                essential: true
            });
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

        function syncInputFiles() {
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

            if (files.length > 3) {
                alert('Solo puedes subir hasta 3 fotos. Se tomarán las primeras 3.');
                files = files.slice(0, 3);
            }

            selectedFiles = files;
            syncInputFiles();
            renderPhotoPreview();
            photosInput.setCustomValidity('');
        });

        syncInputFiles();
        renderPhotoPreview();

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

            ratingText.textContent = currentValue >= 1 ? `${currentValue}/5` : '';
        }

        starButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                ratingInput.value = btn.dataset.value;
                setStars(ratingInput.value);
            });

            btn.addEventListener('mouseenter', () => setStars(btn.dataset.value));
        });

        document.getElementById('starRating')?.addEventListener('mouseleave', () => {
            setStars(ratingInput.value);
        });

        setStars(ratingInput.value);

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
                map.flyTo({ center: [lng, lat], zoom: 12, essential: true });

                if ((addressInput.value || '').trim().length >= 3) {
                    updateAddressMatches();
                }
            });

            openDD(cityDropdown);
        }

        cityInput.addEventListener('focus', () => updateCityMatches());

        cityInput.addEventListener('input', () => {
            invalidateCitySelection();
            cityInput.setCustomValidity('');
            clearTimeout(cityTimer);
            cityTimer = setTimeout(updateCityMatches, 220);
        });

        cityInput.addEventListener('blur', () => {
            if (!cityPlaceId.value) {
                cityInput.setCustomValidity('Selecciona una ciudad de la lista.');
            } else {
                cityInput.setCustomValidity('');
            }
        });

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
                cityInput.setCustomValidity('Primero selecciona una ciudad de la lista.');
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

        function showApprovalToast() {
            approvalToast.classList.remove('hidden');
            approvalToast.classList.add('flex');
        }

        placeForm.addEventListener('submit', (e) => {
            if (isSubmittingApproved) return;

            const hasCity = !!(cityPlaceId.value || '').trim();
            const lat = (latInput.value || '').trim();
            const lng = (lngInput.value || '').trim();

            if (!hasCity) {
                e.preventDefault();
                cityInput.setCustomValidity('Selecciona una ciudad de la lista.');
                cityInput.reportValidity();
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
                photosInput.reportValidity();
                return;
            }

            photosInput.setCustomValidity('');

            e.preventDefault();
            showApprovalToast();

            isSubmittingApproved = true;

            setTimeout(() => {
                placeForm.submit();
            }, 900);
        });
    </script>

    <div class="h-20 sm:h-24"></div>

</x-app-layout>