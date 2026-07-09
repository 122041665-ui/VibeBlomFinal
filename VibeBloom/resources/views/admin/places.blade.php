<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-32";
        $btnGhost = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/80 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-100 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }} relative space-y-6">
            <section class="rounded-[30px] border border-white/80 dark:border-slate-800 bg-white/88 dark:bg-slate-900/88 backdrop-blur shadow-[0_22px_70px_rgba(15,23,42,0.10)] p-5 sm:p-6 lg:p-7">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                            <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                            Administración
                        </div>
                        <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">Gestión de lugares</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">Administra los lugares publicados en la plataforma.</p>
                    </div>

                    <a href="{{ route('admin.index') }}" class="{{ $btnGhost }}">Volver al panel</a>
                </div>
            </section>

            <section class="bg-white/92 dark:bg-slate-900/92 backdrop-blur shadow-sm rounded-[28px] border border-gray-100 dark:border-slate-800 p-6">
                <p class="text-sm leading-6 text-gray-600 dark:text-slate-400">
                    Aquí se mostrará la administración de lugares.
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
