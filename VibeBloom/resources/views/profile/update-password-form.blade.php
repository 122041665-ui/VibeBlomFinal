<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Actualizar contraseña') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Asegúrate de que tu cuenta utiliza una contraseña larga y segura.') }}
    </x-slot>

    <x-slot name="form">

        <!-- Contraseña actual -->
        <div class="col-span-6 sm:col-span-4" x-data="{ visible: false }">
            <x-label for="current_password" value="{{ __('Contraseña actual') }}" />
            <div class="relative mt-1">
                <x-input id="current_password" x-bind:type="visible ? 'text' : 'password'"
                         class="block w-full pr-12" wire:model="state.current_password"
                         autocomplete="current-password" />
                <button type="button" x-on:click="visible = !visible"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-500 hover:text-blue-600"
                        x-bind:aria-label="visible ? 'Ocultar contraseña actual' : 'Mostrar contraseña actual'">
                    <span x-text="visible ? 'Ocultar' : 'Ver'" class="text-xs font-bold"></span>
                </button>
            </div>
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <!-- Nueva contraseña -->
        <div class="col-span-6 sm:col-span-4" x-data="{ visible: false }">
            <x-label for="password" value="{{ __('Nueva contraseña') }}" />
            <div class="relative mt-1">
                <x-input id="password" x-bind:type="visible ? 'text' : 'password'"
                         class="block w-full pr-12" wire:model="state.password"
                         autocomplete="new-password" />
                <button type="button" x-on:click="visible = !visible"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-500 hover:text-blue-600"
                        x-bind:aria-label="visible ? 'Ocultar nueva contraseña' : 'Mostrar nueva contraseña'">
                    <span x-text="visible ? 'Ocultar' : 'Ver'" class="text-xs font-bold"></span>
                </button>
            </div>
            <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres, una mayúscula, una minúscula y un número.</p>
            <x-input-error for="password" class="mt-2" />
        </div>

        <!-- Confirmar nueva contraseña -->
        <div class="col-span-6 sm:col-span-4" x-data="{ visible: false }">
            <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
            <div class="relative mt-1">
                <x-input id="password_confirmation" x-bind:type="visible ? 'text' : 'password'"
                         class="block w-full pr-12" wire:model="state.password_confirmation"
                         autocomplete="new-password" />
                <button type="button" x-on:click="visible = !visible"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-500 hover:text-blue-600"
                        x-bind:aria-label="visible ? 'Ocultar confirmación' : 'Mostrar confirmación'">
                    <span x-text="visible ? 'Ocultar' : 'Ver'" class="text-xs font-bold"></span>
                </button>
            </div>
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>

    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button>
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
