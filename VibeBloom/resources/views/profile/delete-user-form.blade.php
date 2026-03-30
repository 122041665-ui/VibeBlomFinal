<x-action-section>
    <x-slot name="title">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center shadow-sm">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-red-400">
                    <path d="M10 11v6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M14 11v6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M4 7h16" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M6 7l1 11a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-11" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>

            <div>
                <h3 class="text-lg font-extrabold text-white">
                    {{ __('Eliminar Cuenta') }}
                </h3>
                <p class="text-sm text-slate-400">
                    {{ __('Elimina tu cuenta de forma permanente.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <x-slot name="description">
        <p class="text-sm text-slate-400 leading-6">
            {{ __('Esta acción es permanente y eliminará toda la información vinculada a tu cuenta.') }}
        </p>
    </x-slot>

    <x-slot name="content">
        <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-6">
            <div class="flex items-start gap-4">

                <div class="h-10 w-10 rounded-xl bg-slate-900 border border-red-500/20 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-red-400">
                        <path d="M12 8v4" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 16h.01" stroke="currentColor" stroke-width="2"/>
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.72 3h16.92a2 2 0 0 0 1.72-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <p class="text-sm text-slate-300 leading-6">
                        {{ __('Una vez que elimines tu cuenta, todos tus datos y recursos serán eliminados de forma permanente. Antes de continuar, descarga cualquier información que desees conservar.') }}
                    </p>

                    <div class="mt-5">
                        <x-danger-button
                            wire:click="confirmUserDeletion"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition active:scale-[0.98]"
                        >
                            {{ __('Eliminar Cuenta') }}
                        </x-danger-button>
                    </div>
                </div>

            </div>
        </div>

        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                <h3 class="text-lg font-extrabold text-white">
                    {{ __('Confirmar eliminación') }}
                </h3>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-4">
                    <p class="text-sm text-slate-400">
                        {{ __('Ingresa tu contraseña para confirmar que deseas eliminar tu cuenta.') }}
                    </p>

                    <x-input
                        type="password"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition"
                        wire:model="password"
                        placeholder="{{ __('Contraseña') }}"
                    />

                    <x-input-error for="password" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end gap-3 w-full">
                    <x-secondary-button class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-danger-button class="px-5 py-2.5 rounded-xl">
                        {{ __('Eliminar') }}
                    </x-danger-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>