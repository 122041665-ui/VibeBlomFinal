<section class="mb-7">
    <form action="{{ request()->routeIs('dashboard') ? route('dashboard') : route('places.index') }}" method="GET" data-live-place-search class="mx-auto max-w-5xl rounded-2xl border border-gray-200/90 dark:border-slate-700 bg-white/95 dark:bg-slate-900/95 p-2 shadow-sm">
        <div class="flex items-center gap-2">
            <svg class="ml-2 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="6.75" stroke="currentColor" stroke-width="2"/>
                <path d="M16 16l3.75 3.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input type="search" name="buscar" value="{{ request('buscar') }}" minlength="2" maxlength="100" autocomplete="off" placeholder="Nombre, ciudad o tipo" aria-label="Buscar lugares por nombre, ciudad o tipo" class="min-w-0 flex-1 border-0 bg-transparent px-1 py-2 text-sm font-semibold text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-white dark:placeholder:text-slate-500">
            <details class="group relative">
                <summary class="cursor-pointer list-none rounded-xl px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 dark:text-slate-300 dark:hover:bg-slate-800">Filtros</summary>
                <div class="absolute right-0 top-12 z-20 w-72 space-y-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                    <select name="type" aria-label="Tipo de lugar" class="w-full rounded-xl border-gray-200 dark:border-slate-700 dark:bg-slate-950 text-sm">
                        <option value="">Todos los tipos</option>
                        @foreach (['RESTAURANTE' => 'Restaurante', 'CAFETERIA' => 'Cafetería', 'BAR' => 'Bar', 'ANTRO' => 'Antro', 'PARQUE' => 'Parque', 'PLAZA' => 'Plaza', 'MIRADOR' => 'Mirador', 'MUSEO' => 'Museo', 'OTRO' => 'Otro'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="city" value="{{ request('city') }}" maxlength="100" placeholder="Ciudad" class="w-full rounded-xl border-gray-200 dark:border-slate-700 dark:bg-slate-950 text-sm">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" max="1000000" step="1" placeholder="Precio máximo" class="w-full rounded-xl border-gray-200 dark:border-slate-700 dark:bg-slate-950 text-sm">
                </div>
            </details>
            @if(request()->filled('buscar') || request()->filled('city') || request()->filled('type') || request()->filled('max_price'))
                <button type="reset" class="hidden sm:inline-flex rounded-xl px-3 py-2 text-xs font-bold text-gray-500 hover:bg-gray-50 dark:text-slate-400" onclick="setTimeout(() => this.form.requestSubmit(), 0)">Limpiar</button>
            @endif
            <button class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-60" type="submit" data-search-button>Buscar</button>
        </div>
    </form>
</section>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-live-place-search]').forEach(form => {
                const button = form.querySelector('[data-search-button]');
                const input = form.querySelector('input[name="buscar"]');
                let timer = null;

                async function updateResults() {
                    const url = new URL(form.action, window.location.origin);
                    new FormData(form).forEach((value, key) => {
                        if (String(value).trim() !== '') url.searchParams.set(key, value);
                    });

                    button.disabled = true;
                    button.textContent = 'Buscando…';

                    try {
                        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!response.ok) throw new Error('search_failed');
                        const documentResult = new DOMParser().parseFromString(await response.text(), 'text/html');
                        const nextResults = documentResult.querySelector('[data-place-results]');
                        const currentResults = document.querySelector('[data-place-results]');
                        if (nextResults && currentResults) {
                            currentResults.replaceWith(nextResults);
                            history.replaceState({}, '', url);
                        }
                    } catch (error) {
                        form.submit();
                    } finally {
                        button.disabled = false;
                        button.textContent = 'Buscar';
                    }
                }

                form.addEventListener('submit', event => {
                    event.preventDefault();
                    updateResults();
                });
                input?.addEventListener('input', () => {
                    clearTimeout(timer);
                    timer = setTimeout(updateResults, 350);
                });
                form.querySelectorAll('select, input[name="city"], input[name="max_price"]').forEach(control => {
                    control.addEventListener('change', updateResults);
                });
            });
        });
    </script>
@endonce
