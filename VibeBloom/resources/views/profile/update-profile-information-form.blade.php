<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shadow-sm dark:bg-blue-500/10 dark:border-blue-500/20">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-blue-700 dark:text-blue-300" aria-hidden="true">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M20 21a8 8 0 0 0-16 0" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>

            <div class="min-w-0">
                <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-slate-100 leading-tight">
                    {{ __('Información del Perfil') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                    {{ __('Actualiza la información de tu perfil y tu dirección de correo electrónico.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <x-slot name="description">
        <p class="text-sm leading-6 text-gray-600 dark:text-slate-400">
            {{ __('Mantén actualizados tus datos personales y el correo vinculado a tu cuenta.') }}
        </p>
    </x-slot>

    <x-slot name="form">
        @php
            $label = "font-semibold text-gray-800 dark:text-slate-200";
            $hint = "text-xs text-gray-500 dark:text-slate-400 mt-1";
            $field = "w-full mt-1 border border-gray-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm shadow-sm
                      bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500
                      focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 focus:border-blue-300 dark:focus:border-blue-500 transition";
            $btnPrim = "px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                        active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
            $btnGhost = "px-5 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-800 font-semibold rounded-xl shadow-sm transition
                         active:scale-[0.99] border border-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-200
                         dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-300 dark:border-blue-500/20 dark:focus:ring-blue-500/30";
            $pill = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-800 text-xs font-semibold border border-blue-100
                     dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20";
        @endphp

        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6">
                <div class="rounded-2xl border border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-950/40 p-5 sm:p-6">
                    <input
                        id="photo"
                        type="file"
                        class="hidden"
                        wire:model="photo"
                        x-ref="photo"
                        accept="image/*"
                        x-on:change="
                            photoName = $refs.photo.files[0]?.name || null;
                            const reader = new FileReader();
                            reader.onload = (e) => photoPreview = e.target.result;
                            if ($refs.photo.files[0]) reader.readAsDataURL($refs.photo.files[0]);
                        "
                    />

                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <x-label for="photo" value="{{ __('Foto') }}" class="{{ $label }}" />

                        <span class="{{ $pill }}" x-show="photoName" style="display: none;">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-blue-700 dark:text-blue-300" aria-hidden="true">
                                <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M9 10.5a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5Z" fill="currentColor"/>
                                <path d="M20 15.5l-4.5-4.5L7 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="truncate max-w-[180px]" x-text="photoName"></span>
                        </span>
                    </div>

                    <p class="{{ $hint }}">
                        {{ __('Sube una foto cuadrada para que se vea correctamente en tu perfil.') }}
                    </p>

                    <div class="mt-4" x-show="!photoPreview">
                        <div class="flex items-center gap-4">
                            <img
                                src="{{ $this->user->profile_photo_url }}"
                                alt="{{ $this->user->name }}"
                                class="rounded-2xl h-20 w-20 object-cover border border-gray-200 dark:border-slate-700 shadow-sm"
                            >

                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900 dark:text-slate-100 truncate">
                                    {{ $this->user->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-slate-400 truncate">
                                    {{ $this->user->email }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" x-show="photoPreview" style="display: none;">
                        <div class="flex items-center gap-4">
                            <span
                                class="block rounded-2xl h-20 w-20 bg-cover bg-no-repeat bg-center border border-blue-100 dark:border-blue-500/20 shadow-sm"
                                x-bind:style="'background-image: url(' + photoPreview + ');'"
                            ></span>

                            <div class="text-sm text-gray-700 dark:text-slate-300">
                                {{ __('Vista previa de tu nueva foto.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3 flex-wrap">
                        <button
                            type="button"
                            class="{{ $btnGhost }}"
                            x-on:click.prevent="$refs.photo.click()"
                        >
                            {{ __('Seleccionar nueva foto') }}
                        </button>

                        @if ($this->user->profile_photo_path)
                            <button
                                type="button"
                                class="px-5 py-2.5 bg-white hover:bg-red-50 text-red-700 font-semibold rounded-xl shadow-sm transition
                                       active:scale-[0.99] border border-red-100 focus:outline-none focus:ring-2 focus:ring-red-200
                                       dark:bg-slate-900 dark:hover:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 dark:focus:ring-red-500/20"
                                wire:click="deleteProfilePhoto"
                            >
                                {{ __('Eliminar foto') }}
                            </button>
                        @endif
                    </div>

                    <x-input-error for="photo" class="mt-2" />
                </div>
            </div>
        @endif

        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Nombre') }}" class="{{ $label }}" />
            <x-input
                id="name"
                type="text"
                class="{{ $field }}"
                wire:model="state.name"
                autocomplete="name"
            />
            <x-input-error for="state.name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Correo electrónico') }}" class="{{ $label }}" />
            <x-input
                id="email"
                type="email"
                class="{{ $field }}"
                wire:model="state.email"
                autocomplete="username"
            />
            <x-input-error for="state.email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && !$this->user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl border border-blue-100 dark:border-blue-500/20 bg-blue-50/60 dark:bg-blue-500/10 p-4">
                    <p class="text-sm text-gray-700 dark:text-slate-300">
                        {{ __('Tu correo electrónico no ha sido verificado.') }}
                    </p>

                    <button
                        type="button"
                        class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-800
                               dark:text-blue-300 dark:hover:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-200
                               dark:focus:ring-blue-500/30 rounded-lg"
                        wire:click.prevent="sendEmailVerification"
                    >
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                            <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M6.5 7.5 12 12l5.5-4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Reenviar correo de verificación') }}
                    </button>

                    @if ($verificationLinkSent)
                        <p class="mt-2 text-sm font-semibold text-green-700 dark:text-green-400">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-3">
            <x-action-message class="text-sm text-green-700 dark:text-green-400 font-semibold" on="saved">
                {{ __('Guardado.') }}
            </x-action-message>

            <x-button wire:loading.attr="disabled" class="{{ $btnPrim }}">
                {{ __('Guardar') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>