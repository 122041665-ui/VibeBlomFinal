<x-app-layout>
    @php
        $currentUser = auth()->user();
        $container = "max-w-7xl mx-auto px-6 py-6 pb-32";
        $card = "bg-white/92 dark:bg-slate-900/92 backdrop-blur shadow-sm rounded-[28px] border border-gray-100 dark:border-slate-800 p-6";
        $btnPrimary = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/80 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-100 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }} relative">
            <section class="mb-6 rounded-[30px] border border-white/80 dark:border-slate-800 bg-white/88 dark:bg-slate-900/88 backdrop-blur shadow-[0_22px_70px_rgba(15,23,42,0.10)] p-5 sm:p-6 lg:p-7">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                            <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                            Centro de control
                        </div>

                        <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                            Panel de administración
                        </h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                            Gestiona usuarios, lugares y permisos con una vista limpia de operación.
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="{{ $btnGhost }}">
                        Volver al dashboard
                    </a>
                </div>
            </section>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <article class="{{ $card }}">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-500/20">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 11a4 4 0 1 0-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h2 class="mt-5 text-lg font-extrabold text-gray-900 dark:text-slate-100">Usuarios</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-slate-400">Gestiona usuarios registrados y revisa sus roles dentro de VibeBloom.</p>
                    <a href="{{ route('admin.users') }}" class="{{ $btnGhost }} mt-5 w-full">
                        Ver usuarios
                    </a>
                </article>

                <article class="{{ $card }}">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-500/20">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21s7-4.6 7-11a7 7 0 1 0-14 0c0 6.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <h2 class="mt-5 text-lg font-extrabold text-gray-900 dark:text-slate-100">Lugares</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-slate-400">Administra el contenido publicado y mantén la información ordenada.</p>
                    <a href="{{ route('admin.places') }}" class="{{ $btnGhost }} mt-5 w-full">
                        Ver lugares
                    </a>
                </article>

                <article class="{{ $card }} {{ $currentUser->hasRole('admin') ? '' : 'opacity-70' }}">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-500/20">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3 20 7v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M9 12l2 2 4-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2 class="mt-5 text-lg font-extrabold text-gray-900 dark:text-slate-100">Roles y permisos</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-slate-400">
                        {{ $currentUser->hasRole('admin') ? 'Asigna o revoca privilegios de administración.' : 'Solo disponible para administradores.' }}
                    </p>

                    @if($currentUser->hasRole('admin'))
                        <a href="{{ route('admin.users') }}" class="{{ $btnPrimary }} mt-5 w-full">
                            Gestionar roles
                        </a>
                    @else
                        <span class="mt-5 inline-flex w-full items-center justify-center px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-sm font-semibold text-gray-500 dark:text-slate-400 border border-gray-200 dark:border-slate-700">
                            Sin acceso
                        </span>
                    @endif
                </article>
            </div>
        </div>
    </div>
</x-app-layout>
