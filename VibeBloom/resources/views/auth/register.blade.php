<x-guest-layout>
    @php
        $container  = "max-w-7xl mx-auto px-6 py-10";
        $card       = "bg-white shadow rounded-2xl p-6 sm:p-8 max-w-md mx-auto border border-gray-100";

        $title      = "text-2xl font-extrabold text-gray-900";
        $sub        = "text-sm text-gray-600 mt-1";

        $label      = "font-semibold text-gray-800";
        $hint       = "text-xs text-gray-500 mt-1";

        $field      = "w-full mt-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gray-300 transition bg-white";

        $fieldWithIcon = "w-full mt-1 border border-gray-300 rounded-xl pl-4 pr-12 py-2.5 text-sm shadow-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gray-300 transition bg-white";

        $btnPrimary = "w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200";

        $btnGhost   = "px-5 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-xl shadow-sm transition
                       active:scale-[0.99] border border-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-200";

        $link       = "text-sm font-semibold text-blue-700 hover:text-blue-800 underline underline-offset-4";
    @endphp

    <div class="{{ $container }}">
        <div class="{{ $card }}">
            <div class="flex items-center justify-center mb-4">
                <a href="{{ route('home') }}" class="inline-flex items-center">
                    <img src="{{ asset('images/vibebloom.png') }}"
                         alt="VibeBloom"
                         class="h-14 w-auto object-contain"
                         onerror="this.style.display='none'">
                    <span class="sr-only">VibeBloom</span>
                </a>
            </div>

            <div class="text-center mb-6">
                <h1 class="{{ $title }}">Crear cuenta</h1>
                <p class="{{ $sub }}">Únete a VibeBloom y guarda tus lugares favoritos.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <div class="font-extrabold mb-1">Revisa lo siguiente:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="{{ $label }}">Nombre</label>
                    <input id="name"
                           class="{{ $field }}"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           autocomplete="name"
                           placeholder="Tu nombre completo" />
                    <p class="{{ $hint }}">Escribe el nombre que usarás dentro de la plataforma.</p>
                </div>

                <div>
                    <label for="email" class="{{ $label }}">Correo electrónico</label>
                    <input id="email"
                           class="{{ $field }}"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="username"
                           placeholder="tucorreo@ejemplo.com" />
                    <p class="{{ $hint }}">Usa un correo válido para acceder después.</p>
                </div>

                <div>
                    <label for="password" class="{{ $label }}">Contraseña</label>
                    <div class="relative">
                        <input id="password"
                               class="{{ $fieldWithIcon }}"
                               type="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               placeholder="••••••••" />

                        <button type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-blue-600 transition focus:outline-none"
                                aria-label="Mostrar contraseña">
                            <svg id="iconEyePassword" class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"
                                      stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M12 15a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z"
                                      stroke="currentColor" stroke-width="1.8"/>
                            </svg>

                            <svg id="iconEyeOffPassword" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M10.6 10.7a3 3 0 0 0 4.1 4.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M9.9 5.1A11.4 11.4 0 0 1 12 5c6.5 0 10 7 10 7a16.9 16.9 0 0 1-3.4 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.2 6.3C3.7 8 2 12 2 12s3.5 7 10 7c1.8 0 3.4-.4 4.8-1.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <p class="{{ $hint }}">Mínimo 8 caracteres, con al menos una mayúscula.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="{{ $label }}">Confirmar contraseña</label>
                    <div class="relative">
                        <input id="password_confirmation"
                               class="{{ $fieldWithIcon }}"
                               type="password"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="••••••••" />

                        <button type="button"
                                id="togglePasswordConfirmation"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-blue-600 transition focus:outline-none"
                                aria-label="Mostrar confirmación de contraseña">
                            <svg id="iconEyeConfirmation" class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"
                                      stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M12 15a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z"
                                      stroke="currentColor" stroke-width="1.8"/>
                            </svg>

                            <svg id="iconEyeOffConfirmation" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M10.6 10.7a3 3 0 0 0 4.1 4.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M9.9 5.1A11.4 11.4 0 0 1 12 5c6.5 0 10 7 10 7a16.9 16.9 0 0 1-3.4 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.2 6.3C3.7 8 2 12 2 12s3.5 7 10 7c1.8 0 3.4-.4 4.8-1.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <p class="{{ $hint }}">Debe coincidir exactamente con la contraseña anterior.</p>
                </div>

                <div class="pt-2 space-y-3">
                    <button type="submit" class="{{ $btnPrimary }}">
                        Crear cuenta
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="{{ $link }}">
                            ¿Ya tienes cuenta? Inicia sesión
                        </a>
                    </div>
                </div>

                <div class="pt-2 text-center">
                    <a href="{{ route('home') }}"
                       class="text-xs text-gray-500 hover:text-gray-700 underline underline-offset-4">
                        Seguir explorando sin cuenta
                    </a>
                </div>
            </form>
        </div>

        <div class="h-28 sm:h-32 lg:h-40"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function setupToggle(inputId, buttonId, eyeId, eyeOffId, showLabel, hideLabel) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);
                const eye = document.getElementById(eyeId);
                const eyeOff = document.getElementById(eyeOffId);

                if (!input || !button || !eye || !eyeOff) return;

                button.addEventListener('click', () => {
                    const isPassword = input.type === 'password';

                    input.type = isPassword ? 'text' : 'password';
                    eye.classList.toggle('hidden', isPassword);
                    eyeOff.classList.toggle('hidden', !isPassword);
                    button.setAttribute('aria-label', isPassword ? hideLabel : showLabel);
                });
            }

            setupToggle(
                'password',
                'togglePassword',
                'iconEyePassword',
                'iconEyeOffPassword',
                'Mostrar contraseña',
                'Ocultar contraseña'
            );

            setupToggle(
                'password_confirmation',
                'togglePasswordConfirmation',
                'iconEyeConfirmation',
                'iconEyeOffConfirmation',
                'Mostrar confirmación de contraseña',
                'Ocultar confirmación de contraseña'
            );
        });
    </script>
</x-guest-layout>