<x-action-section>
    <x-slot name="title">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shadow-sm dark:bg-blue-500/10 dark:border-blue-500/20">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-blue-700 dark:text-blue-300" aria-hidden="true">
                    <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v7A2.5 2.5 0 0 1 17.5 16h-11A2.5 2.5 0 0 1 4 13.5v-7Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 20h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>

            <div>
                <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-slate-100">
                    {{ __('Sesiones del Navegador') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    {{ __('Administra y cierra sesión en tus dispositivos y navegadores activos.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <x-slot name="description">
        <span class="text-sm text-gray-600 dark:text-slate-400">
            {{ __('Revisa la actividad de acceso y protege tu cuenta cerrando otras sesiones activas.') }}
        </span>
    </x-slot>

    <x-slot name="content">
        @php
            $card = "bg-white dark:bg-slate-900 shadow rounded-2xl p-6 border border-gray-100 dark:border-slate-800";
            $text = "text-sm text-gray-700 dark:text-slate-300";
            $muted = "text-sm text-gray-600 dark:text-slate-400";
            $muted2 = "text-xs text-gray-500 dark:text-slate-500";

            $btnPrimary = "px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                           active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";

            $btnGhost = "px-5 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-800 font-semibold rounded-xl shadow-sm transition
                         active:scale-[0.99] border border-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-200
                         dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-300 dark:border-blue-500/20 dark:focus:ring-blue-500/30";

            $input = "w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                      text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                      focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-blue-300 dark:focus:border-blue-500 transition";

            $pillOk = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-50 text-green-800 text-xs font-semibold border border-green-100
                       dark:bg-green-500/10 dark:text-green-300 dark:border-green-500/20";

            $row = "flex items-center justify-between gap-4 p-4 rounded-2xl border border-gray-100 dark:border-slate-800
                    bg-white dark:bg-slate-950/40 hover:border-blue-100 dark:hover:border-blue-500/20 hover:shadow-sm transition";
        @endphp

        <div class="{{ $card }}">
            <div class="max-w-2xl {{ $muted }} leading-6">
                {{ __('Si lo consideras necesario, puedes cerrar sesión en todos los demás navegadores y dispositivos donde tu cuenta esté activa. Algunas de tus sesiones recientes se muestran a continuación; sin embargo, esta lista puede no estar completa. Si crees que tu cuenta ha sido comprometida, también deberías actualizar tu contraseña.') }}
            </div>

            @if (count($this->sessions) > 0)
                <div class="mt-6 space-y-3">
                    @foreach ($this->sessions as $session)
                        <div class="{{ $row }}">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="shrink-0">
                                    @if ($session->agent->isDesktop())
                                        <div class="h-11 w-11 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center dark:bg-blue-500/10 dark:border-blue-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6 text-blue-700 dark:text-blue-300">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-11 w-11 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center dark:bg-blue-500/10 dark:border-blue-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6 text-blue-700 dark:text-blue-300">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-slate-100 truncate">
                                        {{ $session->agent->platform() ? $session->agent->platform() : __('Desconocido') }}
                                        <span class="text-gray-300 dark:text-slate-600 font-normal">•</span>
                                        {{ $session->agent->browser() ? $session->agent->browser() : __('Desconocido') }}
                                    </div>

                                    <div class="{{ $muted2 }} mt-1 flex flex-wrap items-center gap-2">
                                        <span class="font-medium text-gray-600 dark:text-slate-400">{{ $session->ip_address }}</span>

                                        @if ($session->is_current_device)
                                            <span class="{{ $pillOk }}">{{ __('Este dispositivo') }}</span>
                                        @else
                                            <span class="text-gray-400 dark:text-slate-600">·</span>
                                            <span>{{ __('Última actividad') }} {{ $session->last_active }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 flex items-center gap-3 flex-wrap">
                <x-button wire:click="confirmLogout" wire:loading.attr="disabled" class="{{ $btnPrimary }}">
                    {{ __('Cerrar sesiones en otros dispositivos') }}
                </x-button>

                <x-action-message class="text-sm text-green-600 dark:text-green-400 font-semibold" on="loggedOut">
                    {{ __('Listo.') }}
                </x-action-message>
            </div>
        </div>

        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center dark:bg-blue-500/10 dark:border-blue-500/20">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-blue-700 dark:text-blue-300" aria-hidden="true">
                            <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v7A2.5 2.5 0 0 1 17.5 16h-11A2.5 2.5 0 0 1 4 13.5v-7Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M9 20h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-slate-100">
                            {{ __('Cerrar sesiones en otros dispositivos') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400">
                            {{ __('Confirma tu contraseña para continuar.') }}
                        </p>
                    </div>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-4">
                    <div class="text-sm text-gray-600 dark:text-slate-400 leading-6">
                        {{ __('Por favor, ingresa tu contraseña para confirmar que deseas cerrar sesión en todos tus demás navegadores y dispositivos.') }}
                    </div>

                    <div
                        x-data="{}"
                        x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)"
                    >
                        <x-input
                            type="password"
                            class="{{ $input }}"
                            autocomplete="current-password"
                            placeholder="{{ __('Contraseña') }}"
                            x-ref="password"
                            wire:model="password"
                            wire:keydown.enter="logoutOtherBrowserSessions"
                        />

                        <x-input-error for="password" class="mt-2" />
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3 w-full">
                    <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled" class="{{ $btnGhost }}">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-button wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled" class="{{ $btnPrimary }}">
                        {{ __('Cerrar sesiones') }}
                    </x-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>