<x-guest-layout>
    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden px-4 py-8">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="relative min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div class="relative">
                <x-authentication-card-logo />
            </div>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white/92 dark:bg-slate-900/92 shadow-[0_22px_70px_rgba(15,23,42,0.10)] overflow-hidden rounded-[28px] border border-white/80 dark:border-slate-800 backdrop-blur prose">
                {!! $policy !!}

                <div class="not-prose mt-8 border-t border-gray-100 pt-6">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-700"><span aria-hidden="true">←</span> Volver a Inicio</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
