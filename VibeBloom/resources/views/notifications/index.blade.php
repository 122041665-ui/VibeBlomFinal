<x-app-layout>
    @php
        $container = "max-w-5xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[28px] p-6 border border-gray-100 dark:border-slate-800 backdrop-blur";
        $btnPrimary = "px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
        $btnGhost = "px-6 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-400 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }} relative">
            <section class="{{ $card }} mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Centro de actividad
                        </div>
                        <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">
                            Notificaciones
                        </h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                            Actividad reciente de aprobaciones, seguidores, lugares y acciones importantes.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button class="{{ $btnGhost }} inline-flex w-full sm:w-auto items-center justify-center gap-2" type="submit">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 12l3 3l7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Marcar leídas
                        </button>
                    </form>
                </div>
            </section>

            @if ($notifications->isEmpty())
                <section class="{{ $card }} text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-slate-100">Sin notificaciones</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">Cuando haya actividad nueva, aparecerá aquí.</p>
                </section>
            @else
                <section class="space-y-3">
                    @foreach ($notifications as $notification)
                        <a href="{{ route('notifications.open', $notification) }}" class="group block {{ $card }} p-4 transition hover:-translate-y-0.5 hover:border-blue-100 hover:shadow-lg dark:hover:border-blue-500/30">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300' : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                        <p class="font-extrabold text-gray-900 dark:text-slate-100 group-hover:text-blue-700 dark:group-hover:text-blue-300">
                                            {{ $notification->title }}
                                        </p>
                                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">
                                            {{ $notification->created_at?->diffForHumans() }}
                                        </span>
                                    </div>

                                    @if ($notification->body)
                                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-slate-400">
                                            {{ $notification->body }}
                                        </p>
                                    @endif
                                </div>

                                @if (!$notification->read_at)
                                    <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-600"></span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </section>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
