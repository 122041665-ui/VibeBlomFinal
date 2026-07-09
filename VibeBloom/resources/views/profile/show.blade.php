<x-app-layout>
    <x-slot name="header">
        @php
            $headerContainer = "max-w-7xl mx-auto px-6";
            $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-800 text-xs font-semibold border border-blue-100
                     dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20";
        @endphp

        <div class="{{ $headerContainer }}">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="h-11 w-11 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shadow-sm dark:bg-blue-500/10 dark:border-blue-500/20">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-blue-700 dark:text-blue-300">
                            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M20 21a8 8 0 0 0-16 0" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-slate-100 truncate">
                            {{ __('Perfil') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-slate-400">
                            Configuración personal, seguridad y control de sesiones.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <span class="{{ $pill }}">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-blue-700 dark:text-blue-300">
                            <path d="M12 3 20 7v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4Z" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                        Seguridad
                    </span>

                    <span class="{{ $pill }}">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-blue-700 dark:text-blue-300">
                            <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v7A2.5 2.5 0 0 1 17.5 16h-11A2.5 2.5 0 0 1 4 13.5v-7Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M9 20h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Sesiones
                    </span>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $container = "max-w-7xl mx-auto px-6 py-6 pb-32";
        $card = "bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[28px] border border-gray-100 dark:border-slate-800 overflow-hidden backdrop-blur";
        $section = "p-6 sm:p-7";
    @endphp

    <div class="bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.12),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.08),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)]"></div>

    <div class="{{ $container }} relative">
        <div class="space-y-6">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="{{ $card }}">
                    <div class="{{ $section }}">
                        @livewire('profile.update-profile-information-form')
                    </div>
                </div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="{{ $card }}">
                    <div class="{{ $section }}">
                        @livewire('profile.update-password-form')
                    </div>
                </div>
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="{{ $card }}">
                    <div class="{{ $section }}">
                        @livewire('profile.two-factor-authentication-form')
                    </div>
                </div>
            @endif

            <div class="{{ $card }}">
                <div class="{{ $section }}">
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="{{ $card }}">
                    <div class="{{ $section }}">
                        @livewire('profile.delete-user-form')
                    </div>
                </div>
            @endif
        </div>

        <div class="h-10 sm:h-14"></div>
    </div>
    </div>
</x-app-layout>
