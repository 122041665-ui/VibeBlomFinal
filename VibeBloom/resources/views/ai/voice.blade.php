<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white dark:bg-slate-900 shadow-sm rounded-[28px] border border-gray-100 dark:border-slate-800";
        $hint = "text-sm text-gray-600 dark:text-slate-400";
        $hintXs = "text-xs text-gray-500 dark:text-slate-400";

        $fieldBase = "w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                      text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                      focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-blue-400 dark:focus:border-blue-500 transition";

        $buttonPrimary = "px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                          active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $buttonGhost = "px-4 py-2.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-800 dark:text-blue-300 font-semibold rounded-xl shadow-sm transition
                        active:scale-[0.99] border border-blue-100 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

        $buttonDanger = "px-4 py-2.5 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-700 dark:text-red-300 font-semibold rounded-xl shadow-sm transition
                         active:scale-[0.99] border border-red-200 dark:border-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30";

        $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20";

        $userName = auth()->check()
            ? (auth()->user()->name ?? null)
            : null;
    @endphp

    @once
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endonce

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100/70 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(37,99,235,0.10),transparent_55%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.10),transparent_55%)]"></div>

        <div class="{{ $container }}">
            <div class="mb-6">
                <div class="rounded-[28px] border border-gray-100 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur shadow-sm p-5 sm:p-6 lg:p-7">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <div class="{{ $pill }} mb-3">
                                <span class="inline-flex w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Asistente inteligente
                            </div>

                            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight leading-tight">
                                Asistente de <span class="text-blue-600 dark:text-blue-400">VibeBloom</span>
                            </h1>

                            <p class="{{ $hint }} mt-2">
                                Escribe o habla y encuentra lugares de forma más rápida.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button id="btnClearChatTop"
                                    type="button"
                                    class="{{ $buttonDanger }} inline-flex items-center gap-2"
                                    title="Limpiar chat">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.5 3a1 1 0 00-1 1V5H5a1 1 0 100 2h.293l.853 9.388A2 2 0 008.138 18h3.724a2 2 0 001.992-1.612L14.707 7H15a1 1 0 100-2h-2.5V4a1 1 0 00-1-1h-3Zm2 2h-1V5h1v0Zm-2.215 2 .75 8.25h2.93l.75-8.25H8.285Z" clip-rule="evenodd"/>
                                </svg>
                                Limpiar chat
                            </button>

                            <a href="{{ route('dashboard') }}"
                               class="{{ $buttonGhost }} inline-flex items-center gap-2"
                               title="Regresar a lugares">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H16a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                Volver a lugares
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ $card }} overflow-hidden">
                <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_.85fr]">
                    <div class="border-b xl:border-b-0 xl:border-r border-gray-200 dark:border-slate-800">
                        <div class="px-6 py-4 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div>
                                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">Conversación</h2>
                                    <p class="text-sm text-gray-600 dark:text-slate-400">
                                        El asistente interpreta la búsqueda y propone opciones.
                                    </p>
                                </div>

                                <span class="{{ $pill }}">
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Activo
                                </span>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 dark:bg-slate-950/60">
                            <div id="chatWrap" class="min-h-[360px] max-h-[520px] overflow-auto pr-1 space-y-4">
                                <div id="chat" class="space-y-4"></div>

                                <div id="emptyState" class="hidden">
                                    <div class="rounded-2xl border border-dashed border-blue-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/60 p-6 text-center">
                                        <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M8 10h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M8 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-slate-100">Empieza una búsqueda</h3>
                                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                            Escribe tu consulta para buscar lugares dentro de VibeBloom.
                                        </p>
                                    </div>
                                </div>

                                <div id="typing" class="hidden mt-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center font-bold text-blue-700 dark:text-blue-400 shrink-0">
                                            VB
                                        </div>
                                        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl px-4 py-3 shadow-sm inline-block w-fit max-w-[80%]">
                                            <div class="flex items-center gap-2 text-gray-500 dark:text-slate-400 text-sm">
                                                <span class="inline-block w-2 h-2 rounded-full bg-gray-300 dark:bg-slate-600 animate-pulse"></span>
                                                <span class="inline-block w-2 h-2 rounded-full bg-gray-300 dark:bg-slate-600 animate-pulse" style="animation-delay:120ms"></span>
                                                <span class="inline-block w-2 h-2 rounded-full bg-gray-300 dark:bg-slate-600 animate-pulse" style="animation-delay:240ms"></span>
                                                <span class="ml-2">Vibe está escribiendo…</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800">
                            <div class="flex gap-2 items-center">
                                <div class="flex-1 relative">
                                    <input id="q"
                                           placeholder="Escribe lo que buscas…"
                                           class="{{ $fieldBase }} pr-11"/>

                                    <button id="btnSend"
                                            class="absolute right-1.5 top-1/2 -translate-y-1/2 {{ $buttonPrimary }} !px-3 inline-flex items-center justify-center"
                                            type="button"
                                            title="Enviar"
                                            aria-label="Enviar">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4.5 12h13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M14.5 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>

                                <button id="btnMic"
                                        class="{{ $buttonGhost }} inline-flex items-center justify-center shrink-0"
                                        type="button"
                                        title="Grabar"
                                        aria-label="Grabar">
                                    <span id="micIcon" class="inline-flex">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 14a3 3 0 0 0 3-3V7a3 3 0 1 0-6 0v4a3 3 0 0 0 3 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19 11a7 7 0 0 1-14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M12 18v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M8 21h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>

                            <div class="mt-2 flex items-center justify-between gap-3 flex-wrap">
                                <p id="estado" class="{{ $hintXs }}"></p>
                                <p class="{{ $hintXs }}">Enter para enviar</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                            <div class="flex items-end justify-between gap-3 flex-wrap">
                                <div>
                                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-slate-100">Resultados</h2>
                                    <p class="text-sm text-gray-600 dark:text-slate-400">
                                        Lugares encontrados según la búsqueda.
                                    </p>
                                </div>

                                <button id="btnClear"
                                        class="{{ $buttonGhost }}"
                                        type="button">
                                    Limpiar todo
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div id="resultsEmpty" class="rounded-2xl border border-dashed border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-950/40 p-8 text-center">
                                <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M10 18a8 8 0 1 1 5.293-14.293A8 8 0 0 1 10 18Zm0 0 8 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-slate-100">Aún no hay resultados</h3>
                                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                    Cuando se haga una búsqueda, aquí aparecerán las recomendaciones.
                                </p>
                            </div>

                            <div id="resultsSection" class="hidden">
                                <div id="cards" class="grid grid-cols-1 sm:grid-cols-2 gap-6"></div>

                                <div id="moreWrap" class="hidden mt-6 flex justify-center">
                                    <button id="btnMore" class="{{ $buttonGhost }}" type="button">Cargar más</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    </div>

    <script>
        const $ = (id) => document.getElementById(id);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const ENDPOINT = @json(url('/ai/voz/recomendar'));
        const USER_NAME = @json($userName);

        const estado = $('estado');
        const cards = $('cards');
        const moreWrap = $('moreWrap');
        const chat = $('chat');
        const chatWrap = $('chatWrap');
        const typing = $('typing');
        const resultsSection = $('resultsSection');
        const resultsEmpty = $('resultsEmpty');
        const emptyState = $('emptyState');

        const LS_CHAT = 'vb_ai_chat_v7_dark';
        const LS_RESULTS = 'vb_ai_results_v7_dark';

        function buildGreeting(name) {
            const n = (name && String(name).trim()) ? String(name).trim() : null;
            const hello = n ? `Hola, **${n}**` : `Hola`;

            return [
                `${hello}. Soy **Vibe**, tu asistente virtual.`,
                `Escribe o dicta tu búsqueda para encontrar lugares dentro de VibeBloom.`
            ].join('\n');
        }

        const GREETING = buildGreeting(USER_NAME);

        const pickResults = (d) => d?.resultados || d?.places || d?.results || d?.recommendations || [];
        const pickPrefs = (d) => d?.preferencias_extraidas || d?.prefs || d?.preferences || null;

        const money = (v) => {
            const n = Number(v);
            if (!Number.isFinite(n)) return '';
            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n);
        };

        function escapeHtml(s) {
            return String(s ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderBold(text) {
            const safe = escapeHtml(text);
            return safe.replace(/\*\*(.+?)\*\*/g, '<span class="font-semibold text-gray-900 dark:text-slate-100">$1</span>');
        }

        function toggleEmptyChat() {
            const hasMessages = chat.children.length > 0;
            emptyState.classList.toggle('hidden', hasMessages);
        }

        function showTyping(on) {
            typing.classList.toggle('hidden', !on);
            requestAnimationFrame(() => chatWrap.scrollTo({ top: chatWrap.scrollHeight, behavior: 'smooth' }));
        }

        function showResults() {
            resultsSection.classList.remove('hidden');
            resultsEmpty.classList.add('hidden');
        }

        function hideResults() {
            resultsSection.classList.add('hidden');
            resultsEmpty.classList.remove('hidden');
            moreWrap.classList.add('hidden');
            cards.innerHTML = '';
        }

        function loadChat() {
            try {
                const raw = localStorage.getItem(LS_CHAT);
                const arr = raw ? JSON.parse(raw) : [];
                return Array.isArray(arr) ? arr : [];
            } catch {
                return [];
            }
        }

        function saveChat(msgs) {
            localStorage.setItem(LS_CHAT, JSON.stringify(msgs));
        }

        function bubbleUser(text) {
            return `
                <div class="flex justify-end">
                    <div class="bg-blue-600 text-white rounded-2xl px-4 py-3 shadow-sm inline-block w-fit max-w-[85%]">
                        <p class="text-sm whitespace-pre-wrap leading-relaxed">${escapeHtml(text)}</p>
                    </div>
                </div>
            `;
        }

        function getPlaceIcon(type) {
            const t = String(type || '').toUpperCase().trim();
            const base = 'w-5 h-5 text-blue-700 dark:text-blue-400 shrink-0';

            const icons = {
                RESTAURANTE: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M7 3v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10 3v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M7 7h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M14 3v7a3 3 0 0 0 3 3v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                `,
                CAFETERIA: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 9h9v5a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M13 10h1.5a2.5 2.5 0 0 1 0 5H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8 6s-.8.8.1 1.7C8.9 8.5 8.8 9 8.8 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                `,
                BAR: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M8 4h8l-1 6a3 3 0 0 1-3 2h0a3 3 0 0 1-3-2L8 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M12 12v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10 20h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                `,
                ANTRO: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9 3v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M15 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M7 7h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10 10h4l-1 10h-2l-1-10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                `,
                PARQUE: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 20v-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M12 4 6.5 13h11L12 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M8.5 13h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                `,
                PLAZA: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 9h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M7 9V6.8A1.8 1.8 0 0 1 8.8 5h6.4A1.8 1.8 0 0 1 17 6.8V9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M6 9v9h12V9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                `,
                MIRADOR: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 15l5-5 4 4 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                `,
                MUSEO: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 9h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M6 9v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10 9v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M14 9v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M18 9v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M12 4 4 8h16l-8-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                `,
                OTRO: `
                    <svg class="${base}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 8v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="12" cy="16" r="1" fill="currentColor"/>
                    </svg>
                `,
            };

            return icons[t] || icons.OTRO;
        }

        function bubbleVB(text) {
            return `
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center font-bold text-blue-700 dark:text-blue-400 shrink-0">VB</div>
                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl px-4 py-3 shadow-sm inline-block w-fit max-w-[85%]">
                        <p class="text-sm text-gray-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed">${renderBold(text)}</p>
                    </div>
                </div>
            `;
        }

        function renderChat(msgs) {
            chat.innerHTML = '';
            msgs.forEach(m => {
                chat.insertAdjacentHTML('beforeend', m.role === 'user' ? bubbleUser(m.content) : bubbleVB(m.content));
            });
            toggleEmptyChat();
            requestAnimationFrame(() => chatWrap.scrollTo({ top: chatWrap.scrollHeight, behavior: 'smooth' }));
        }

        function addMessage(role, content) {
            const msgs = loadChat();
            msgs.push({ role, content, ts: Date.now() });
            saveChat(msgs);
            renderChat(msgs);
        }

        async function ensureGreeting(force = false) {
            const msgs = loadChat();
            if (msgs.length && !force) {
                renderChat(msgs);
                return;
            }

            renderChat([]);
            showTyping(true);
            await new Promise(r => setTimeout(r, 650));
            showTyping(false);

            chat.insertAdjacentHTML('beforeend', bubbleVB(GREETING));
            saveChat([{ role: 'assistant', content: GREETING, ts: Date.now() }]);
            toggleEmptyChat();

            requestAnimationFrame(() => chatWrap.scrollTo({ top: chatWrap.scrollHeight, behavior: 'smooth' }));
        }

        function escapeRegExp(s) {
            return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function cleanAssistantText(raw, userQuery) {
            let t = String(raw || '').trim();
            if (!t) return '';

            t = t.replace(/^a\s*continuaci[oó]n\s+se\s+muestran?\s+resultados?\s+de\s*:\s*/i, '');
            t = t.replace(/^a\s*continuaci[oó]n\s+te\s+muestro\s+opciones\s+de\s*:\s*/i, '');
            t = t.replace(/^listo\s*/i, '');

            const q = String(userQuery || '').trim();
            if (q && t.toLowerCase().includes(q.toLowerCase())) {
                t = t.replace(new RegExp(escapeRegExp(q), 'i'), '').trim();
                t = t.replace(/^[:\-–—]\s*/, '').trim();
            }

            t = t.replace(/\n{3,}/g, '\n\n').trim();
            return t;
        }

        const TYPE_LABEL = {
            'RESTAURANTE': 'restaurante',
            'CAFETERIA': 'cafetería',
            'BAR': 'bar',
            'ANTRO': 'antro',
            'PARQUE': 'parque',
            'PLAZA': 'plaza',
            'MIRADOR': 'mirador',
            'MUSEO': 'museo',
            'OTRO': 'otro',
        };

        function typeLabel(t) {
            if (!t) return null;
            const u = String(t).toUpperCase().trim();
            return TYPE_LABEL[u] || null;
        }

        function niceIntroFromPrefs(prefs) {
            const p = (prefs && typeof prefs === 'object') ? prefs : {};
            const parts = [];

            const type = typeLabel(p.type);
            if (type) parts.push(type);

            const city = p.city ? String(p.city).trim() : null;
            if (city) parts.push(city);

            const zone = p.zone ? String(p.zone).trim() : null;
            if (zone) parts.push(zone);

            const rmin = p.rating_min != null ? Number(p.rating_min) : null;
            if (Number.isFinite(rmin)) parts.push(`mín ${rmin}★`);

            const rmax = p.rating_max != null ? Number(p.rating_max) : null;
            if (Number.isFinite(rmax) && !Number.isFinite(rmin)) parts.push(`máx ${rmax}★`);

            if (!parts.length) return 'Listo. Aquí van algunas opciones:';
            return `Listo. Te dejo opciones de: ${parts.join(' · ')}.`;
        }

        function followUp(prefs) {
            const p = (prefs && typeof prefs === 'object') ? prefs : {};
            const type = typeLabel(p.type);
            const city = p.city ? String(p.city).trim() : null;

            if (!type && !city) return "¿En qué **ciudad** estás y qué **tipo** quieres?";
            if (!type) return `¿Qué **tipo** buscas en ${city}: cafetería, restaurante, bar, antro, parque…?`;
            if (!city) return "¿En qué **ciudad** lo quieres?";

            if (!p.price && !p.max_price && !p.min_price) {
                return "¿Qué presupuesto traes: **bajo, medio o alto**? (o dime “máximo $300”)";
            }

            if (!p.zone) {
                return `¿Alguna zona en ${city}? (Centro, Juriquilla, Zibatá, etc.) o te da igual.`;
            }

            return "¿Quieres que te muestre más opciones o me quedo con las mejores?";
        }

        function buildAssistantText(data, userQuery) {
            const prefs = pickPrefs(data);
            const results = pickResults(data);

            const intro = niceIntroFromPrefs(prefs);
            const backendClean = cleanAssistantText(data?.assistant_reply, userQuery);

            if (results.length) {
                if (backendClean) return `${intro}\n\n${backendClean}`;
                return intro;
            }

            if (backendClean) return backendClean;
            return `Todavía no lo tengo perfecto.\n${followUp(prefs)}`;
        }

        let allResults = [];
        let shown = 0;
        const PAGE = 12;

        function clamp(n, min, max) {
            n = Number(n);
            if (!Number.isFinite(n)) return min;
            return Math.max(min, Math.min(max, n));
        }

        function starsHTML(rating) {
            const r = clamp(rating, 0, 5);
            let html = '';
            for (let i = 1; i <= 5; i++) {
                const cls = i <= r ? 'text-yellow-500' : 'text-gray-300 dark:text-slate-600';
                html += `
                    <svg class="w-5 h-5 ${cls}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                `;
            }
            return html;
        }

        function urlFoto(p) {
            if (p?.photo_url) return String(p.photo_url);
            if (p?.photo) {
                const limpio = String(p.photo).replaceAll('\\','/').replace(/^\/+/, '').replace(/^storage\//,'');
                return '/storage/' + limpio;
            }
            return '/images/default.jpg';
        }

        function prettyTypeLabel(tipo) {
            const t = String(tipo || '').toUpperCase().trim();
            const map = {
                RESTAURANTE: 'Restaurante',
                CAFETERIA: 'Cafetería',
                BAR: 'Bar',
                ANTRO: 'Antro',
                PARQUE: 'Parque',
                PLAZA: 'Plaza',
                MIRADOR: 'Mirador',
                MUSEO: 'Museo',
                OTRO: 'Otro',
            };
            return map[t] || 'Otro';
        }

        function cardHTML(p) {
            const id = p.id ?? p.place_id ?? null;
            const nombre = p.name ?? 'Lugar';
            const ciudad = p.city ?? '';
            const tipo = p.type ?? 'OTRO';
            const tipoLabel = prettyTypeLabel(tipo);
            const rating = clamp(p.rating ?? 0, 0, 5);
            const hasPrice = p.price !== null && p.price !== undefined && p.price !== '';
            const priceText = hasPrice ? money(p.price) : null;
            const foto = urlFoto(p);
            const url = p.url ?? (id ? `/places/${id}` : '#');

            return `
                <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow hover:shadow-lg transition overflow-hidden border border-gray-100 dark:border-slate-800">
                    <a href="${url}" class="block">
                        <img src="${foto}" class="h-48 w-full object-cover"
                             alt="Foto de ${escapeHtml(nombre)}"
                             onerror="this.onerror=null; this.src='/images/default.jpg';">
                    </a>

                    <div class="p-5 space-y-3">
                        <div>
                            <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-slate-100">${escapeHtml(nombre)}</h2>
                            <p class="text-gray-600 dark:text-slate-400 text-sm mt-1">${escapeHtml(ciudad || 'Sin ciudad')}</p>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 text-xs font-semibold border border-blue-100 dark:border-blue-500/20">
                                ${getPlaceIcon(tipo)}
                                ${escapeHtml(tipoLabel)}
                            </span>

                            <div class="flex items-center gap-1" title="${rating}/5">
                                ${starsHTML(rating)}
                                <span class="text-xs text-gray-500 dark:text-slate-400 ml-1">${rating}/5</span>
                            </div>
                        </div>

                        <div class="pt-1">
                            <p class="text-gray-500 dark:text-slate-400 text-xs">Precio aprox. por persona</p>
                            ${hasPrice
                                ? `<p class="text-gray-900 dark:text-slate-100 font-bold text-lg">${priceText}</p>`
                                : `<p class="text-gray-500 dark:text-slate-400 text-sm">Sin precio estimado</p>`
                            }
                        </div>

                        <div class="pt-1 flex items-center justify-end">
                            <a href="${url}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-800 dark:text-blue-300 text-sm font-semibold shadow-sm transition border border-blue-100 dark:border-slate-700">
                                Ver detalle
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2H14.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderFirst(results) {
            allResults = Array.isArray(results) ? results : [];
            shown = 0;
            localStorage.setItem(LS_RESULTS, JSON.stringify(allResults));

            if (!allResults.length) {
                hideResults();
                return;
            }

            showResults();
            cards.innerHTML = '';
            renderMore();
        }

        function renderMore() {
            const next = allResults.slice(shown, shown + PAGE);
            next.forEach(p => cards.insertAdjacentHTML('beforeend', cardHTML(p)));
            shown += next.length;

            if (shown < allResults.length) {
                moreWrap.classList.remove('hidden');
                $('btnMore').textContent = `Cargar más (${shown}/${allResults.length})`;
            } else {
                moreWrap.classList.add('hidden');
            }
        }

        function restoreResults() {
            try {
                const raw = localStorage.getItem(LS_RESULTS);
                const arr = raw ? JSON.parse(raw) : [];

                if (Array.isArray(arr) && arr.length) {
                    renderFirst(arr);
                } else {
                    hideResults();
                }
            } catch {
                hideResults();
            }
        }

        function clearChatOnly() {
            $('q').value = '';
            estado.textContent = '';
            localStorage.removeItem(LS_CHAT);
            chat.innerHTML = '';
            toggleEmptyChat();
            ensureGreeting(true);
        }

        function clearAll() {
            $('q').value = '';
            estado.textContent = '';
            localStorage.removeItem(LS_CHAT);
            localStorage.removeItem(LS_RESULTS);
            chat.innerHTML = '';
            hideResults();
            allResults = [];
            shown = 0;
            toggleEmptyChat();
            ensureGreeting(true);
        }

        $('btnClear').onclick = clearAll;
        $('btnClearChatTop').onclick = clearChatOnly;
        $('btnMore').onclick = renderMore;

        async function postForm(form) {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: form
            });

            const raw = await res.text();
            if (!res.ok) throw new Error(`HTTP ${res.status} -> ${raw.slice(0, 200)}`);

            const ct = res.headers.get('content-type') || '';
            if (!ct.includes('application/json')) throw new Error(`No JSON -> ${raw.slice(0, 200)}`);

            return JSON.parse(raw);
        }

        async function sendText(query) {
            try {
                estado.textContent = 'Buscando...';
                addMessage('user', query);
                showTyping(true);

                const form = new FormData();
                form.append('text', query);

                const data = await postForm(form);
                const assistantText = buildAssistantText(data, query);

                if (assistantText) addMessage('assistant', assistantText);

                renderFirst(pickResults(data));
                estado.textContent = pickResults(data).length ? 'Listo' : 'No hubo resultados.';
            } catch (err) {
                console.error(err);
                estado.textContent = `Error: ${err.message}`;
                addMessage('assistant', `Tuve un problema. ${err.message}`);
            } finally {
                showTyping(false);
            }
        }

        $('btnSend').onclick = () => {
            const q = $('q').value.trim();
            if (!q) return;
            $('q').value = '';
            sendText(q);
        };

        $('q').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('btnSend').click();
            }
        });

        let recording = false, mediaRecorder = null, chunks = [], stream = null;

        const micBtn = $('btnMic');
        const micIcon = $('micIcon');

        const MIC_SVG = `
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 14a3 3 0 0 0 3-3V7a3 3 0 1 0-6 0v4a3 3 0 0 0 3 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19 11a7 7 0 0 1-14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 18v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M8 21h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        `;

        const STOP_SVG = `
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="7" y="7" width="10" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
            </svg>
        `;

        function setMicState(on) {
            if (on) {
                micBtn.classList.remove('bg-blue-50','dark:bg-slate-800','hover:bg-blue-100','dark:hover:bg-slate-700','text-blue-800','dark:text-blue-300','border-blue-100','dark:border-slate-700','focus:ring-blue-200','dark:focus:ring-blue-500/30');
                micBtn.classList.add('bg-red-50','dark:bg-red-500/10','hover:bg-red-100','dark:hover:bg-red-500/20','text-red-700','dark:text-red-300','border','border-red-200','dark:border-red-500/20');
                micBtn.setAttribute('title', 'Detener');
                micBtn.setAttribute('aria-label', 'Detener');
                micIcon.innerHTML = STOP_SVG;
            } else {
                micBtn.classList.remove('bg-red-50','dark:bg-red-500/10','hover:bg-red-100','dark:hover:bg-red-500/20','text-red-700','dark:text-red-300','border-red-200','dark:border-red-500/20');
                micBtn.classList.add('bg-blue-50','dark:bg-slate-800','hover:bg-blue-100','dark:hover:bg-slate-700','text-blue-800','dark:text-blue-300','border-blue-100','dark:border-slate-700');
                micBtn.setAttribute('title', 'Grabar');
                micBtn.setAttribute('aria-label', 'Grabar');
                micIcon.innerHTML = MIC_SVG;
            }
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
        }

        micIcon.innerHTML = MIC_SVG;

        micBtn.onclick = async () => {
            if (recording && mediaRecorder) {
                mediaRecorder.stop();
                return;
            }

            try {
                chunks = [];
                estado.textContent = 'Pidiendo micrófono...';

                stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = (e) => chunks.push(e.data);

                mediaRecorder.onstart = () => {
                    recording = true;
                    setMicState(true);
                    estado.textContent = 'Grabando...';
                };

                mediaRecorder.onstop = async () => {
                    try {
                        recording = false;
                        setMicState(false);
                        estado.textContent = 'Enviando audio...';
                        showTyping(true);

                        const blob = new Blob(chunks, { type: 'audio/webm' });
                        const form = new FormData();
                        form.append('audio', blob, 'voz.webm');

                        const data = await postForm(form);

                        const userMsg = data?.transcripcion || 'Mensaje de voz';
                        addMessage('user', userMsg);

                        const assistantText = buildAssistantText(data, userMsg);
                        if (assistantText) addMessage('assistant', assistantText);

                        renderFirst(pickResults(data));
                        estado.textContent = pickResults(data).length ? 'Listo' : 'No hubo resultados.';
                    } catch (err) {
                        console.error(err);
                        estado.textContent = `Error: ${err.message}`;
                        addMessage('assistant', `Tuve un error con el audio. ${err.message}`);
                    } finally {
                        showTyping(false);
                        stopStream();
                    }
                };

                mediaRecorder.start();
            } catch (e) {
                estado.textContent = 'No se pudo acceder al micrófono.';
                setMicState(false);
                stopStream();
            }
        };

        (async function init() {
            await ensureGreeting();
            restoreResults();
            toggleEmptyChat();
        })();
    </script>
</x-app-layout>