<x-app-layout>
    <div class="min-h-screen vb-soft-page py-10">
        <article class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 p-6 sm:p-10 shadow-sm">
                <span class="inline-flex rounded-full bg-blue-50 dark:bg-blue-500/10 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300">Información VibeBloom</span>
                <h1 class="mt-4 text-3xl font-extrabold text-gray-950 dark:text-white">{{ $title }}</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Última actualización: 15 de julio de 2026</p>

                <div class="mt-8 space-y-6 text-sm sm:text-base leading-7 text-gray-700 dark:text-slate-300">
                    @foreach ($sections as $section)
                        <section>
                            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">{{ $section['title'] }}</h2>
                            <p class="mt-2">{{ $section['body'] }}</p>
                        </section>
                    @endforeach
                </div>

                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="mt-10 inline-flex rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-700">← Volver a Inicio</a>
            </div>
        </article>
    </div>
</x-app-layout>
