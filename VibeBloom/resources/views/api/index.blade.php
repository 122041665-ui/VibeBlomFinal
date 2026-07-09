<x-app-layout>
    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="max-w-7xl mx-auto px-6 py-6 pb-32 relative">
            <section class="mb-6 rounded-[30px] border border-white/80 dark:border-slate-800 bg-white/88 dark:bg-slate-900/88 backdrop-blur shadow-[0_22px_70px_rgba(15,23,42,0.10)] p-5 sm:p-6 lg:p-7">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                    Integraciones
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                    {{ __('API Tokens') }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                    Administra accesos técnicos con el mismo control visual de VibeBloom.
                </p>
            </section>

            @livewire('api.api-token-manager')
        </div>
    </div>
</x-app-layout>
