<x-app-layout>

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet" />

    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white dark:bg-slate-900 shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800";

        $btnPrimary = "px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $btnGhost = "inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400
                     font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700
                     focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $labelClass = "font-semibold text-gray-800 dark:text-slate-100";
        $hintClass = "text-xs text-gray-500 dark:text-slate-400 mt-1";

        $field = "w-full mt-1 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                  text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                  focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-gray-300 dark:focus:border-slate-600 transition";

        $select = $field . " pr-10";

        $fileHidden = "hidden";
        $fileFake = $field . " cursor-pointer flex items-center justify-between gap-3";

        $placeId = is_array($place) ? ($place['id'] ?? null) : ($place->id ?? null);
        $placeName = is_array($place) ? ($place['name'] ?? '') : ($place->name ?? '');
        $placeCity = is_array($place) ? ($place['city'] ?? '') : ($place->city ?? '');
        $placeType = is_array($place) ? ($place['type'] ?? '') : ($place->type ?? '');
        $placeRating = is_array($place) ? ($place['rating'] ?? 0) : ($place->rating ?? 0);
        $placePrice = is_array($place) ? ($place['price'] ?? 0) : ($place->price ?? 0);
        $placeAddress = is_array($place) ? ($place['address'] ?? '') : ($place->address ?? '');
        $placeDescription = is_array($place) ? ($place['description'] ?? '') : ($place->description ?? '');
        $placeLat = is_array($place) ? ($place['lat'] ?? null) : ($place->lat ?? null);
        $placeLng = is_array($place) ? ($place['lng'] ?? null) : ($place->lng ?? null);
        $placePhoto = is_array($place) ? ($place['photo'] ?? null) : ($place->photo ?? null);
        $placePhotos = is_array($place) ? ($place['photos'] ?? []) : ($place->photos ?? []);
        $placePhotosUrls = is_array($place) ? ($place['photos_urls'] ?? []) : ($place->photos_urls ?? []);

        $currentCity = old('city', $placeCity);
        $currentAddress = old('address', $placeAddress);

        $normalizedPhotos = [];

        if (!empty($placePhoto)) {
            $normalizedPhotos[] = str_starts_with($placePhoto, 'http')
                ? $placePhoto
                : asset('storage/' . ltrim($placePhoto, '/'));
        }

        if (is_array($placePhotosUrls)) {
            foreach ($placePhotosUrls as $url) {
                if (!empty($url)) {
                    $normalizedPhotos[] = $url;
                }
            }
        }

        if (is_array($placePhotos)) {
            foreach ($placePhotos as $photoItem) {
                if (is_array($photoItem)) {
                    $url = $photoItem['url']
                        ?? $photoItem['photo_url']
                        ?? (!empty($photoItem['path']) ? asset('storage/' . ltrim($photoItem['path'], '/')) : null);
                } elseif (is_object($photoItem)) {
                    $url = $photoItem->url
                        ?? $photoItem->photo_url
                        ?? (!empty($photoItem->path) ? asset('storage/' . ltrim($photoItem->path, '/')) : null);
                } else {
                    $url = is_string($photoItem) ? asset('storage/' . ltrim($photoItem, '/')) : null;
                }

                if (!empty($url)) {
                    $normalizedPhotos[] = $url;
                }
            }
        }

        $normalizedPhotos = array_values(array_unique($normalizedPhotos));
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
                                Edición de lugar
                            </div>

                            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                                Editar lugar
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-slate-400 mt-2">
                                Actualiza la información para que tu lugar se vea mejor en el feed.
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

            <form action="{{ route('places.update', $placeId) }}" method="POST" enctype="multipart/form-data" id="placeForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                    <div class="xl:col-span-7">
                        <div class="{{ $card }}">
                            @if ($errors->any())
                                <div class="mb-6 rounded-2xl border border-red-200 dark:border-red-900/60 bg-red-50 dark:bg-red-950/40 p-4">
                                    <p class="font-semibold text-red-700 dark:text-red-300">Corrige lo siguiente:</p>
                                    <ul class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="space-y-6">
                                <div>
                                    <label class="{{ $labelClass }}">Nombre del lugar</label>
                                    <input type="text" name="name" value="{{ old('name', $placeName) }}" required
                                           oninvalid="this.setCustomValidity('Completa este campo')"
                                           oninput="this.setCustomValidity('')"
                                           class="{{ $field }}"
                                           placeholder="Ej. Puerta La Victoria">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="{{ $labelClass }}">Tipo de lugar</label>
                                        @php $currentType = old('type', $placeType); @endphp
                                        <select name="type" required class="{{ $select }}">
                                            <option value="" disabled {{ $currentType ? '' : 'selected' }}>Selecciona una opción</option>
                                            <option value="Restaurante" {{ $currentType === 'Restaurante' ? 'selected' : '' }}>Restaurante</option>
                                            <option value="Cafetería" {{ $currentType === 'Cafetería' ? 'selected' : '' }}>Cafetería</option>
                                            <option value="Bar" {{ $currentType === 'Bar' ? 'selected' : '' }}>Bar</option>
                                            <option value="Antro" {{ $currentType === 'Antro' ? 'selected' : '' }}>Antro</option>
                                            <option value="Parque" {{ $currentType === 'Parque' ? 'selected' : '' }}>Parque</option>
                                            <option value="Mirador" {{ $currentType === 'Mirador' ? 'selected' : '' }}>Mirador</option>
                                            <option value="Museo" {{ $currentType === 'Museo' ? 'selected' : '' }}>Museo</option>
                                            <option value="Plaza" {{ $currentType === 'Plaza' ? 'selected' : '' }}>Plaza</option>
                                            <option value="Centro comercial" {{ $currentType === 'Centro comercial' ? 'selected' : '' }}>Centro comercial</option>
                                            <option value="Otro" {{ $currentType === 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        <p class="{{ $hintClass }}">Ayuda a ordenar mejor el feed.</p>
                                    </div>

                                    <div>
                                        <label class="{{ $labelClass }}">Calificación</label>

                                        @php $currentRating = old('rating', $placeRating); @endphp
                                        <input type="hidden" name="rating" id="rating" value="{{ $currentRating }}">

                                        <div class="{{ $field }} flex items-center justify-between">
                                            <div id="starRating" class="flex items-center gap-1 select-none">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button"
                                                            class="star-btn p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition"
                                                            data-value="{{ $i }}"
                                                            aria-label="Calificar {{ $i }} estrellas">
                                                        <svg class="star w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                            <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"
                                                                  stroke="currentColor" stroke-width="1.5" />
                                                        </svg>
                                                    </button>
                                                @endfor
                                            </div>

                                            <span id="ratingText" class="text-sm text-gray-600 dark:text-slate-300"></span>
                                        </div>

                                        <p class="{{ $hintClass }}">5 = imperdible, 3 = bien, 1 = no volvería.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Precio aprox. por persona</label>
                                    <div class="relative mt-1">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 dark:text-slate-400 font-semibold text-sm">MXN</span>
                                        <input type="number" name="price" value="{{ old('price', $placePrice) }}" required
                                               class="{{ $field }} pl-14" step="0.01" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="xl:hidden">
                                    <label class="{{ $labelClass }}">Ubicación del lugar</label>

                                    <div class="relative mt-3">
                                        <label class="text-sm {{ $labelClass }}">Ciudad</label>

                                        <input
                                            type="text"
                                            id="cityInputMobile"
                                            value="{{ $currentCity }}"
                                            autocomplete="off"
                                            class="{{ $field }}"
                                            placeholder="Escribe una ciudad (Ej. Querétaro, CDMX...)"
                                        >

                                        <div id="cityDropdownMobile"
                                             class="hidden absolute z-30 mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-lg overflow-hidden">
                                            <div id="cityListMobile" class="max-h-64 overflow-auto"></div>
                                        </div>

                                        <p class="{{ $hintClass }}">Solo se aceptan ciudades reales.</p>
                                    </div>

                                    <div id="mapMobile" class="w-full h-72 mt-4 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm"></div>

                                    <div class="relative mt-4">
                                        <label class="text-sm {{ $labelClass }}">Dirección</label>

                                        <input
                                            type="text"
                                            id="addressInputMobile"
                                            value="{{ $currentAddress }}"
                                            autocomplete="off"
                                            class="{{ $field }}"
                                            placeholder="Ej. Av. Constituyentes 120, Centro"
                                        >

                                        <div id="addressDropdownMobile"
                                             class="hidden absolute z-30 mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-lg overflow-hidden">
                                            <div id="addressListMobile" class="max-h-64 overflow-auto"></div>
                                        </div>

                                        <p class="{{ $hintClass }}">Puedes elegir una sugerencia o dar clic en el mapa para ajustar.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Fotos actuales</label>

                                    @if (count($normalizedPhotos))
                                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            @foreach ($normalizedPhotos as $photoUrl)
                                                <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-900">
                                                    <img src="{{ $photoUrl }}" class="w-full h-40 object-cover" alt="Foto actual">
                                                </div>
                                            @endforeach
                                        </div>

                                        <p class="{{ $hintClass }}">Si subes nuevas fotos, se reemplazan las actuales.</p>
                                    @else
                                        <p class="text-gray-500 dark:text-slate-400 text-sm mt-2">Este lugar no tiene fotos guardadas.</p>
                                    @endif
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Cambiar fotos</label>

                                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple class="{{ $fileHidden }}">

                                    <label for="photos" class="{{ $fileFake }}">
                                        <span class="text-gray-700 dark:text-slate-200 font-medium">Seleccionar fotos</span>
                                        <span id="filesText" class="text-gray-500 dark:text-slate-400 text-sm">0/3</span>
                                    </label>

                                    <p class="{{ $hintClass }}">Puedes subir de 1 a 3 fotos nuevas.</p>
                                    <div id="photoPreview" class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3"></div>
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Descripción</label>
                                    <textarea name="description" rows="4" class="{{ $field }}"
                                              placeholder="Describe el ambiente, etc.">{{ old('description', $placeDescription) }}</textarea>
                                </div>

                                <div class="flex justify-end xl:hidden">
                                    <button type="submit" class="{{ $btnPrimary }}">Guardar cambios</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden xl:block xl:col-span-5">
                        <div class="{{ $card }} xl:sticky xl:top-6">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">Ubicación del lugar</h2>
                                    <p class="{{ $hintClass }}">Ajusta ciudad, dirección y marcador sin salir del formulario.</p>
                                </div>

                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20">
                                    Mapa
                                </span>
                            </div>

                            <div class="relative mt-4">
                                <label class="text-sm {{ $labelClass }}">Ciudad</label>

                                <input
                                    type="text"
                                    id="cityInput"
                                    name="city"
                                    value="{{ $currentCity }}"
                                    required
                                    autocomplete="off"
                                    class="{{ $field }}"
                                    placeholder="Escribe una ciudad (Ej. Querétaro, CDMX...)"
                                >

                                <input type="hidden" id="cityPlaceId" value="">

                                <div id="cityDropdown"
                                     class="hidden absolute z-30 mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-lg overflow-hidden">
                                    <div id="cityList" class="max-h-64 overflow-auto"></div>
                                </div>

                                <p class="{{ $hintClass }}">Solo se aceptan ciudades reales.</p>
                            </div>

                            <div id="map" class="w-full h-80 mt-4 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm"></div>

                            <input type="hidden" id="lat" name="lat" value="{{ old('lat', $placeLat) }}">
                            <input type="hidden" id="lng" name="lng" value="{{ old('lng', $placeLng) }}">

                            <div class="relative mt-4">
                                <label class="text-sm {{ $labelClass }}">Dirección</label>

                                <input
                                    type="text"
                                    id="addressInput"
                                    name="address"
                                    value="{{ $currentAddress }}"
                                    autocomplete="off"
                                    class="{{ $field }}"
                                    placeholder="Ej. Av. Constituyentes 120, Centro"
                                >

                                <div id="addressDropdown"
                                     class="hidden absolute z-30 mt-2 w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-lg overflow-hidden">
                                    <div id="addressList" class="max-h-64 overflow-auto"></div>
                                </div>

                                <p class="{{ $hintClass }}">Puedes elegir una sugerencia o dar clic en el mapa para ajustar.</p>
                            </div>

                            <div class="hidden xl:flex justify-end mt-6">
                                <button type="submit" form="placeForm" class="{{ $btnPrimary }}">Guardar cambios</button>
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
        mapboxgl.accessToken = "{{ config('services.mapbox.token') }}";

        const initialLat = parseFloat("{{ old('lat', $placeLat) }}");
        const initialLng = parseFloat("{{ old('lng', $placeLng) }}");

        const cityInput = document.getElementById('cityInput');
        const cityPlaceId = document.getElementById('cityPlaceId');
        const cityDropdown = document.getElementById('cityDropdown');
        const cityList = document.getElementById('cityList');

        const addressInput = document.getElementById('addressInput');
        const addressDropdown = document.getElementById('addressDropdown');
        const addressList = document.getElementById('addressList');

        const cityInputMobile = document.getElementById('cityInputMobile');
        const cityDropdownMobile = document.getElementById('cityDropdownMobile');
        const cityListMobile = document.getElementById('cityListMobile');

        const addressInputMobile = document.getElementById('addressInputMobile');
        const addressDropdownMobile = document.getElementById('addressDropdownMobile');
        const addressListMobile = document.getElementById('addressListMobile');

        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');

        let cityFeature = null;
        let cityTimer = null;
        let addrTimer = null;
        let marker = null;
        let mobileMarker = null;

        const desktopMap = document.getElementById('map');
        const mobileMap = document.getElementById('mapMobile');

        const map = desktopMap ? new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: (!isNaN(initialLng) && !isNaN(initialLat)) ? [initialLng, initialLat] : [-100.3899, 20.5888],
            zoom: (!isNaN(initialLng) && !isNaN(initialLat)) ? 14 : 5
        }) : null;

        const mapMobile = mobileMap ? new mapboxgl.Map({
            container: 'mapMobile',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: (!isNaN(initialLng) && !isNaN(initialLat)) ? [initialLng, initialLat] : [-100.3899, 20.5888],
            zoom: (!isNaN(initialLng) && !isNaN(initialLat)) ? 14 : 5
        }) : null;

        function resizeMaps() {
            setTimeout(() => {
                if (map) map.resize();
                if (mapMobile) mapMobile.resize();
            }, 120);
        }

        function setMarkerPosition(lng, lat, flyZoom = 16) {
            latInput.value = lat;
            lngInput.value = lng;

            if (marker) marker.remove();
            if (mobileMarker) mobileMarker.remove();

            if (map) {
                marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);
                map.flyTo({ center: [lng, lat], zoom: flyZoom, essential: true });
            }

            if (mapMobile) {
                mobileMarker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(mapMobile);
                mapMobile.flyTo({ center: [lng, lat], zoom: flyZoom, essential: true });
            }
        }

        function setMarkerWithoutFly(lng, lat) {
            latInput.value = lat;
            lngInput.value = lng;

            if (marker) marker.remove();
            if (mobileMarker) mobileMarker.remove();

            if (map) marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);
            if (mapMobile) mobileMarker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(mapMobile);
        }

        if (!isNaN(initialLat) && !isNaN(initialLng)) {
            setMarkerWithoutFly(initialLng, initialLat);
        }

        async function geocode(text, params = {}) {
            if (!text) return null;

            const base = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(text)}.json`;
            const urlParams = new URLSearchParams({
                access_token: mapboxgl.accessToken,
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

        async function reverseGeocode(lng, lat) {
            const params = { types: 'address', limit: '1' };

            if (cityFeature?.center) {
                params.proximity = `${cityFeature.center[0]},${cityFeature.center[1]}`;
            }

            const base = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json`;
            const urlParams = new URLSearchParams({
                access_token: mapboxgl.accessToken,
                language: 'es',
                country: 'mx',
                ...params
            });

            const res = await fetch(`${base}?${urlParams.toString()}`);
            if (!res.ok) return null;

            const data = await res.json();
            return data?.features?.[0]?.place_name || null;
        }

        function openDD(dd) {
            dd?.classList.remove('hidden');
        }

        function closeDD(dd) {
            dd?.classList.add('hidden');
        }

        function renderButtons(listEl, features, onPick) {
            if (!listEl) return;

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
            if (cityPlaceId) cityPlaceId.value = '';
        }

        function fillCityValue(value) {
            if (cityInput) cityInput.value = value;
            if (cityInputMobile) cityInputMobile.value = value;
        }

        function fillAddressValue(value) {
            if (addressInput) addressInput.value = value;
            if (addressInputMobile) addressInputMobile.value = value;
        }

        async function updateCityMatches() {
            const query = (cityInput?.value || cityInputMobile?.value || '').trim();
            if (query.length < 2) {
                closeDD(cityDropdown);
                closeDD(cityDropdownMobile);
                return;
            }

            const data = await geocode(query, { types: 'place' });
            const features = data?.features || [];

            const onPick = (feature) => {
                cityFeature = feature;
                if (cityPlaceId) cityPlaceId.value = feature.id || '';

                fillCityValue(normalizeCityName(feature));
                closeDD(cityDropdown);
                closeDD(cityDropdownMobile);

                const [lng, lat] = feature.center;
                if (map) map.flyTo({ center: [lng, lat], zoom: 12, essential: true });
                if (mapMobile) mapMobile.flyTo({ center: [lng, lat], zoom: 12, essential: true });

                if ((addressInput?.value || addressInputMobile?.value || '').trim().length >= 3) {
                    updateAddressMatches();
                }
            };

            renderButtons(cityList, features, onPick);
            renderButtons(cityListMobile, features, onPick);

            openDD(cityDropdown);
            openDD(cityDropdownMobile);
        }

        async function updateAddressMatches() {
            const address = (addressInput?.value || addressInputMobile?.value || '').trim();
            if (address.length < 3) {
                closeDD(addressDropdown);
                closeDD(addressDropdownMobile);
                return;
            }

            if (!cityFeature || !cityPlaceId?.value) {
                closeDD(addressDropdown);
                closeDD(addressDropdownMobile);
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

            const onPick = (feature) => {
                fillAddressValue(feature.place_name);
                closeDD(addressDropdown);
                closeDD(addressDropdownMobile);

                const [lng, lat] = feature.center;
                setMarkerPosition(lng, lat, 16);
            };

            renderButtons(addressList, features, onPick);
            renderButtons(addressListMobile, features, onPick);

            openDD(addressDropdown);
            openDD(addressDropdownMobile);
        }

        async function handleMapClick(event) {
            const { lng, lat } = event.lngLat;
            setMarkerPosition(lng, lat, 16);

            const addr = await reverseGeocode(lng, lat);
            if (addr) fillAddressValue(addr);
        }

        if (map) {
            map.on('load', resizeMaps);
            map.on('click', handleMapClick);
        }

        if (mapMobile) {
            mapMobile.on('load', resizeMaps);
            mapMobile.on('click', handleMapClick);
        }

        [cityInput, cityInputMobile].forEach(input => {
            if (!input) return;

            input.addEventListener('focus', () => updateCityMatches());

            input.addEventListener('input', () => {
                fillCityValue(input.value);
                invalidateCitySelection();

                if (cityInput) cityInput.setCustomValidity('');
                clearTimeout(cityTimer);
                cityTimer = setTimeout(updateCityMatches, 220);
            });

            input.addEventListener('blur', () => {
                if (cityInput && !cityPlaceId?.value) {
                    cityInput.setCustomValidity('Selecciona una ciudad de la lista.');
                } else if (cityInput) {
                    cityInput.setCustomValidity('');
                }
            });
        });

        [addressInput, addressInputMobile].forEach(input => {
            if (!input) return;

            input.addEventListener('focus', () => {
                if ((input.value || '').trim().length >= 3) updateAddressMatches();
            });

            input.addEventListener('input', () => {
                fillAddressValue(input.value);
                clearTimeout(addrTimer);
                addrTimer = setTimeout(updateAddressMatches, 220);
            });

            input.addEventListener('keydown', async (e) => {
                if (e.key !== 'Enter') return;
                e.preventDefault();

                const value = (input.value || '').trim();
                if (value.length < 3) return;

                if (!cityFeature || !cityPlaceId?.value) {
                    if (cityInput) {
                        cityInput.focus();
                        cityInput.setCustomValidity('Primero selecciona una ciudad de la lista.');
                        cityInput.reportValidity();
                    }
                    return;
                } else if (cityInput) {
                    cityInput.setCustomValidity('');
                }

                const cityName = normalizeCityName(cityFeature);
                const combined = cityName ? `${value}, ${cityName}` : value;

                const params = { types: 'address', limit: '1' };
                if (cityFeature?.center) params.proximity = `${cityFeature.center[0]},${cityFeature.center[1]}`;
                if (cityFeature?.bbox && Array.isArray(cityFeature.bbox) && cityFeature.bbox.length === 4) {
                    params.bbox = cityFeature.bbox.join(',');
                }

                const data = await geocode(combined, params);
                const feature = data?.features?.[0];

                if (feature?.center) {
                    fillAddressValue(feature.place_name);
                    setMarkerPosition(feature.center[0], feature.center[1], 16);
                }
            });
        });

        document.addEventListener('click', (e) => {
            if (cityDropdown && !cityDropdown.contains(e.target) && e.target !== cityInput) closeDD(cityDropdown);
            if (cityDropdownMobile && !cityDropdownMobile.contains(e.target) && e.target !== cityInputMobile) closeDD(cityDropdownMobile);
            if (addressDropdown && !addressDropdown.contains(e.target) && e.target !== addressInput) closeDD(addressDropdown);
            if (addressDropdownMobile && !addressDropdownMobile.contains(e.target) && e.target !== addressInputMobile) closeDD(addressDropdownMobile);
        });

        window.addEventListener('resize', resizeMaps);

        (async function bootstrapCity() {
            const city = (cityInput?.value || cityInputMobile?.value || '').trim();
            if (city.length < 2) return;

            const data = await geocode(city, { types: 'place', limit: '1' });
            const feature = data?.features?.[0];
            if (!feature) return;

            cityFeature = feature;
            if (cityPlaceId) cityPlaceId.value = feature.id || '';
            fillCityValue(normalizeCityName(feature));

            const latVal = parseFloat(latInput.value);
            const lngVal = parseFloat(lngInput.value);
            const hasCoords = !isNaN(latVal) && !isNaN(lngVal);

            if (!hasCoords && feature.center) {
                if (map) map.flyTo({ center: feature.center, zoom: 12, essential: true });
                if (mapMobile) mapMobile.flyTo({ center: feature.center, zoom: 12, essential: true });
            }
        })();

        const ratingInput = document.getElementById('rating');
        const ratingText = document.getElementById('ratingText');
        const starButtons = document.querySelectorAll('.star-btn');

        function setStars(value) {
            const currentValue = parseInt(value || 0);

            starButtons.forEach(btn => {
                const starVal = parseInt(btn.dataset.value);
                const svg = btn.querySelector('svg');

                if (starVal <= currentValue) {
                    svg.classList.add('text-yellow-500');
                    svg.querySelector('path').setAttribute('fill', 'currentColor');
                } else {
                    svg.classList.remove('text-yellow-500');
                    svg.querySelector('path').setAttribute('fill', 'none');
                }
            });

            ratingText.textContent = currentValue >= 1 ? `${currentValue}/5` : '';
        }

        starButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                ratingInput.value = btn.dataset.value;
                setStars(btn.dataset.value);
            });

            btn.addEventListener('mouseenter', () => setStars(btn.dataset.value));
            btn.addEventListener('mouseleave', () => setStars(ratingInput.value));
        });

        setStars(ratingInput.value);

        const photosInput = document.getElementById('photos');
        const previewContainer = document.getElementById('photoPreview');
        const filesText = document.getElementById('filesText');

        function setFilesCounter(total) {
            if (!filesText) return;
            filesText.textContent = `${total}/3`;
        }

        if (photosInput) {
            setFilesCounter((photosInput.files || []).length);

            photosInput.addEventListener('change', () => {
                if (!previewContainer) return;

                previewContainer.innerHTML = "";
                let files = Array.from(photosInput.files || []);

                if (files.length > 3) {
                    alert('Solo puedes subir hasta 3 fotos. Se tomarán las primeras 3.');
                    files = files.slice(0, 3);

                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));
                    photosInput.files = dt.files;
                }

                setFilesCounter(files.length);

                files.forEach((file) => {
                    if (!file.type.startsWith('image/')) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const card = document.createElement('div');
                        card.className = "rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm bg-white dark:bg-slate-900";

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = "Vista previa";
                        img.className = "w-full h-40 object-cover";

                        card.appendChild(img);
                        previewContainer.appendChild(card);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }
    </script>

    <div class="h-20 sm:h-24"></div>
</x-app-layout>