<x-app-layout>
    @php
        $container = "max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-40 sm:pb-44";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[28px] border border-gray-100 dark:border-slate-800 backdrop-blur";
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

        $openAiConfigured = (bool) ($openAiConfigured ?? false);
        $quickPrompts = [
            'Cafeterías tranquilas en Querétaro para trabajar',
            'Restaurantes bonitos con presupuesto medio',
            'Bares para ir de noche con buen ambiente',
            'Parques o miradores para un plan tranquilo',
        ];
    @endphp

    @once
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endonce

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }} relative">
            <section class="mb-6 overflow-hidden rounded-[30px] border border-white/80 dark:border-slate-800 bg-white/88 dark:bg-slate-900/88 shadow-[0_22px_70px_rgba(15,23,42,0.10)] backdrop-blur">
                <div class="relative p-5 sm:p-7 lg:p-8">
                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(37,99,235,0.08),transparent_38%),radial-gradient(circle_at_88%_16%,rgba(14,165,233,0.14),transparent_26%)] dark:bg-[linear-gradient(135deg,rgba(59,130,246,0.12),transparent_38%),radial-gradient(circle_at_88%_16%,rgba(14,165,233,0.12),transparent_26%)]"></div>

                    <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div class="max-w-3xl">
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                                <span class="inline-block h-2 w-2 rounded-full {{ $openAiConfigured ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $openAiConfigured ? 'Vibe IA' : 'Configura OPENAI_API_KEY' }}
                            </div>

                            <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-950 dark:text-slate-100 leading-tight">
                                Vibe IA para encontrar <span class="text-blue-600 dark:text-blue-400">lugares</span>
                            </h1>

                            <p class="{{ $hint }} mt-4 max-w-2xl leading-8">
                                Habla o escribe lo que traes en mente. Vibe interpreta intención, ciudad, presupuesto y tipo de plan para recomendar lugares dentro de VibeBloom.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button id="btnClearChatTop" type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-slate-200 shadow-sm transition hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-300">
                                Limpiar chat
                            </button>

                            <a href="{{ route('dashboard') }}" class="{{ $buttonGhost }} inline-flex items-center justify-center gap-2">
                                Volver a lugares
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_390px] gap-6 items-start">
                <main class="{{ $card }} overflow-hidden">
                    <div class="border-b border-gray-100 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-5 sm:px-6 py-4">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-extrabold text-gray-950 dark:text-slate-100">Conversación</h2>
                                <p class="{{ $hintXs }} mt-1">Pide algo como “cafetería tranquila en Querétaro, máximo $250”.</p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2">
                                <input id="cityFilter" class="{{ $fieldBase }} sm:w-48" placeholder="Ciudad opcional">
                                <select id="limitSelect" class="{{ $fieldBase }} sm:w-44">
                                    <option value="">Resultados auto</option>
                                    <option value="3">Top 3</option>
                                    <option value="6">Top 6</option>
                                    <option value="10">Top 10</option>
                                    <option value="20">Top 20</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 bg-slate-50/80 dark:bg-slate-950/50">
                        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
                            @foreach ($quickPrompts as $prompt)
                                <button type="button" data-prompt="{{ $prompt }}" class="quick-prompt shrink-0 rounded-full border border-blue-100 dark:border-blue-500/20 bg-white/90 dark:bg-slate-900/80 px-4 py-2 text-sm font-semibold text-blue-700 dark:text-blue-300 shadow-sm transition hover:bg-blue-50 dark:hover:bg-blue-500/10">
                                    {{ $prompt }}
                                </button>
                            @endforeach
                        </div>

                        <div id="chatWrap" class="min-h-[430px] max-h-[58vh] overflow-auto pr-1 space-y-4">
                            <div id="chat" class="space-y-4"></div>

                            <div id="emptyState" class="hidden">
                                <div class="rounded-[24px] border border-dashed border-blue-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/60 p-8 text-center">
                                    <div class="mx-auto mb-4 w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M6 4h12a2 2 0 0 1 2 2v12l-4-2-4 2-4-2-4 2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-extrabold text-gray-950 dark:text-slate-100">Empieza con una intención</h3>
                                    <p class="{{ $hint }} mt-2">Puedes escribir, dictar por micrófono o usar una sugerencia rápida.</p>
                                </div>
                            </div>

                            <div id="typing" class="hidden mt-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-sm shrink-0">VB</div>
                                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl px-4 py-3 shadow-sm inline-block w-fit max-w-[80%]">
                                        <div class="flex items-center gap-2 text-gray-500 dark:text-slate-400 text-sm">
                                            <span class="inline-block w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                                            <span class="inline-block w-2 h-2 rounded-full bg-blue-400 animate-pulse" style="animation-delay:120ms"></span>
                                            <span class="inline-block w-2 h-2 rounded-full bg-blue-400 animate-pulse" style="animation-delay:240ms"></span>
                                            <span class="ml-2">Vibe está pensando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-4">
                        <div class="flex gap-2 items-center">
                            <div class="flex-1 relative">
                                <input id="q" placeholder="Describe el plan que quieres..." class="{{ $fieldBase }} pr-12">

                                <button id="btnSend" class="absolute right-1.5 top-1/2 -translate-y-1/2 {{ $buttonPrimary }} !px-3 inline-flex items-center justify-center" type="button" title="Enviar" aria-label="Enviar">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4.5 12h13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M14.5 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>

                            <button id="btnMic" class="{{ $buttonGhost }} inline-flex items-center justify-center shrink-0" type="button" title="Grabar" aria-label="Grabar">
                                <span id="micIcon" class="inline-flex"></span>
                            </button>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-3 flex-wrap">
                            <p id="estado" class="{{ $hintXs }}"></p>
                            <div class="flex items-center gap-2">
                                <button id="btnCopyLast" type="button" class="text-xs font-semibold text-blue-700 dark:text-blue-300 hover:underline">Copiar última búsqueda</button>
                                <span class="text-xs text-gray-300 dark:text-slate-700">/</span>
                                <p class="{{ $hintXs }}">Enter para enviar</p>
                            </div>
                        </div>
                    </div>
                </main>

                <aside class="{{ $card }} overflow-hidden xl:sticky xl:top-6">
                    <div class="border-b border-gray-100 dark:border-slate-800 px-5 sm:px-6 py-4 bg-white/80 dark:bg-slate-900/80">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-extrabold text-gray-950 dark:text-slate-100">Recomendaciones</h2>
                                <p class="{{ $hintXs }} mt-1">Resultados generados por intención.</p>
                            </div>

                            <button id="btnClear" class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-slate-200 shadow-sm transition hover:border-blue-200 hover:text-blue-700 dark:hover:border-blue-500/30 dark:hover:text-blue-300" type="button">
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div id="resultsEmpty" class="rounded-[24px] border border-dashed border-gray-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-950/40 p-7 text-center">
                            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 flex items-center justify-center">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10 18a8 8 0 1 1 5.293-14.293A8 8 0 0 1 10 18Zm0 0 8 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-950 dark:text-slate-100">Sin resultados todavía</h3>
                            <p class="{{ $hint }} mt-2">Cuando hagas una búsqueda, Vibe colocará aquí las mejores opciones.</p>
                        </div>

                        <div id="resultsSection" class="hidden">
                            <div id="insightsPanel" class="mb-4 hidden rounded-[22px] border border-blue-100 dark:border-blue-500/20 bg-blue-50/70 dark:bg-blue-500/10 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-blue-700 dark:text-blue-300">Lectura de Vibe</p>
                                <div id="insightsChips" class="mt-3 flex flex-wrap gap-2"></div>
                            </div>

                            <div id="cards" class="grid grid-cols-1 gap-4"></div>

                            <div id="moreWrap" class="hidden mt-6 flex justify-center">
                                <button id="btnMore" class="{{ $buttonGhost }}" type="button">Cargar más</button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        const $ = (id) => document.getElementById(id);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const ENDPOINT = @json(url('/ai/voz/recomendar'));
        const USER_NAME = @json($userName);
        const OPENAI_CONFIGURED = @json($openAiConfigured);

        const estado = $('estado');
        const cards = $('cards');
        const moreWrap = $('moreWrap');
        const chat = $('chat');
        const chatWrap = $('chatWrap');
        const typing = $('typing');
        const resultsSection = $('resultsSection');
        const resultsEmpty = $('resultsEmpty');
        const emptyState = $('emptyState');
        const cityFilter = $('cityFilter');
        const limitSelect = $('limitSelect');
        const insightsPanel = $('insightsPanel');
        const insightsChips = $('insightsChips');

        const LS_CHAT = 'vb_ai_chat_v8_modern';
        const LS_RESULTS = 'vb_ai_results_v8_modern';
        const LS_LAST_QUERY = 'vb_ai_last_query_v8';
        const LS_INSIGHTS = 'vb_ai_insights_v8';

        function buildGreeting(name) {
            const n = (name && String(name).trim()) ? String(name).trim() : null;
            const hello = n ? `Hola, **${n}**` : `Hola`;

            return [
                `${hello}. Soy **Vibe**, tu asistente virtual.`,
                `Puedo recomendar lugares por **tipo**, **ciudad**, **presupuesto** y estilo de plan.`
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
            insightsPanel?.classList.add('hidden');
            cards.innerHTML = '';
        }

        function renderInsights(data = null) {
            const filters = data?.filtros_aplicados || data?.filters || [];
            if (!Array.isArray(filters) || !filters.length) {
                insightsPanel?.classList.add('hidden');
                if (insightsChips) insightsChips.innerHTML = '';
                localStorage.removeItem(LS_INSIGHTS);
                return;
            }

            insightsPanel?.classList.remove('hidden');
            insightsChips.innerHTML = filters.map(filter => `
                <span class="inline-flex items-center rounded-full border border-blue-100 dark:border-blue-500/20 bg-white/85 dark:bg-slate-900/70 px-3 py-1.5 text-xs font-bold text-blue-800 dark:text-blue-300">
                    ${escapeHtml(filter)}
                </span>
            `).join('');
            localStorage.setItem(LS_INSIGHTS, JSON.stringify(filters));
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
                const countText = results.length === 1
                    ? 'Encontré **1 lugar** que cumple mejor con tu búsqueda.'
                    : `Encontré **${results.length} lugares** que cumplen mejor con tu búsqueda.`;

                if (backendClean) return `${countText}\n\n${intro}`;
                return `${countText}\n\n${intro}`;
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
            return '/images/vibebloom.png';
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
            const address = p.address ?? '';
            const description = p.description ?? '';
            const tipo = p.type ?? 'OTRO';
            const tipoLabel = prettyTypeLabel(tipo);
            const rating = clamp(p.rating ?? 0, 0, 5);
            const hasPrice = p.price !== null && p.price !== undefined && p.price !== '';
            const priceText = hasPrice ? money(p.price) : null;
            const foto = urlFoto(p);
            const url = p.url ?? (id ? `/places/${id}` : '#');
            const mapUrl = p.map_url || '/places/map';
            const reasons = Array.isArray(p.match_reasons) ? p.match_reasons.slice(0, 3) : [];
            const shortDescription = String(description || address || '').trim();

            return `
                <article class="group overflow-hidden rounded-[22px] border border-gray-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 shadow-sm transition hover:-translate-y-0.5 hover:shadow-[0_18px_45px_rgba(15,23,42,0.12)]">
                    <div class="grid grid-cols-[112px_minmax(0,1fr)]">
                        <a href="${url}" class="relative block h-full min-h-[138px] overflow-hidden bg-slate-100 dark:bg-slate-800">
                            <img src="${foto}" class="h-full w-full object-cover transition group-hover:scale-[1.04]"
                                 alt="Foto de ${escapeHtml(nombre)}"
                                 onerror="this.onerror=null; this.src='/images/vibebloom.png';">
                        </a>

                        <div class="min-w-0 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-base font-extrabold leading-tight text-gray-950 dark:text-slate-100">${escapeHtml(nombre)}</h2>
                                    <p class="mt-1 truncate text-sm text-gray-600 dark:text-slate-400">${escapeHtml(ciudad || 'Sin ciudad')}</p>
                                </div>

                                <span class="shrink-0 rounded-full bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 text-xs font-bold text-blue-700 dark:text-blue-300">
                                    ${rating}/5
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-100 dark:border-blue-500/20 bg-blue-50/80 dark:bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-800 dark:text-blue-300">
                                    ${getPlaceIcon(tipo)}
                                    ${escapeHtml(tipoLabel)}
                                </span>

                                <span class="rounded-full border border-gray-200 dark:border-slate-700 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:text-slate-300">
                                    ${hasPrice ? priceText : 'Sin precio'}
                                </span>
                            </div>

                            ${shortDescription ? `
                                <p class="mt-3 line-clamp-2 text-xs leading-5 text-gray-500 dark:text-slate-400">
                                    ${escapeHtml(shortDescription)}
                                </p>
                            ` : ''}

                            ${reasons.length ? `
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    ${reasons.map(reason => `
                                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-1 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                            ${escapeHtml(reason)}
                                        </span>
                                    `).join('')}
                                </div>
                            ` : ''}

                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-0.5" title="${rating}/5">
                                    ${starsHTML(rating).replaceAll('w-5 h-5', 'w-3.5 h-3.5')}
                                </div>

                                <div class="flex items-center gap-3">
                                    <a href="${mapUrl}" class="text-sm font-bold text-gray-500 dark:text-slate-400 hover:text-blue-700 dark:hover:text-blue-300">
                                        Mapa
                                    </a>

                                    <a href="${url}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-700 dark:text-blue-300">
                                        Detalle
                                        <svg class="w-4 h-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
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
                const insightsRaw = localStorage.getItem(LS_INSIGHTS);
                const insights = insightsRaw ? JSON.parse(insightsRaw) : [];

                if (Array.isArray(arr) && arr.length) {
                    renderInsights({ filtros_aplicados: insights });
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
            localStorage.removeItem(LS_INSIGHTS);
            chat.innerHTML = '';
            hideResults();
            allResults = [];
            shown = 0;
            toggleEmptyChat();
            ensureGreeting(true);
        }

        function appendSearchMeta(form) {
            const city = cityFilter?.value?.trim();
            const limit = limitSelect?.value;

            if (city) form.append('city', city);
            if (limit) form.append('limit', limit);
        }

        function rememberQuery(query) {
            if (!query) return;
            localStorage.setItem(LS_LAST_QUERY, query);
        }

        $('btnClear').onclick = clearAll;
        $('btnClearChatTop').onclick = clearChatOnly;
        $('btnMore').onclick = renderMore;

        $('btnCopyLast')?.addEventListener('click', async () => {
            const last = localStorage.getItem(LS_LAST_QUERY) || '';
            if (!last) {
                estado.textContent = 'Aún no hay una búsqueda para copiar.';
                return;
            }

            try {
                await navigator.clipboard.writeText(last);
                estado.textContent = 'Última búsqueda copiada.';
            } catch {
                $('q').value = last;
                estado.textContent = 'No pude copiarla, la dejé en el campo.';
            }
        });

        document.querySelectorAll('[data-prompt]').forEach(button => {
            button.addEventListener('click', () => {
                const prompt = button.dataset.prompt || '';
                $('q').value = prompt;
                $('q').focus();
            });
        });

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
                rememberQuery(query);
                showTyping(true);

                const form = new FormData();
                form.append('text', query);
                appendSearchMeta(form);

                const data = await postForm(form);
                const assistantText = buildAssistantText(data, query);

                if (assistantText) addMessage('assistant', assistantText);

                renderInsights(data);
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
                        appendSearchMeta(form);

                        const data = await postForm(form);

                        const userMsg = data?.transcripcion || 'Mensaje de voz';
                        addMessage('user', userMsg);

                        const assistantText = buildAssistantText(data, userMsg);
                        if (assistantText) addMessage('assistant', assistantText);

                        renderInsights(data);
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
            if (!OPENAI_CONFIGURED) {
                estado.textContent = 'Falta configurar OPENAI_API_KEY en .env.';
            }

            await ensureGreeting();
            restoreResults();
            toggleEmptyChat();
        })();
    </script>
</x-app-layout>
