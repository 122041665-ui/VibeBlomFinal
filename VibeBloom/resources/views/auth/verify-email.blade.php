<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm leading-6 text-gray-600 dark:text-slate-400">
            {{ __('Antes de continuar, por favor verifica tu dirección de correo haciendo clic en el enlace que te acabamos de enviar. Si no recibiste el correo, con gusto te enviaremos otro.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-semibold text-sm text-green-700 dark:text-green-300">
                {{ __('Se ha enviado un nuevo enlace de verificación al correo electrónico que registraste en la configuración de tu perfil.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit">
                        {{ __('Reenviar correo de verificación') }}
                    </x-button>
                </div>
            </form>

            <div>
                <a
                    href="{{ route('profile.show') }}"
                    class="underline text-sm font-semibold text-blue-700 hover:text-blue-800 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    {{ __('Editar perfil') }}
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit"
                            class="underline text-sm font-semibold text-gray-600 hover:text-blue-700 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-200 ms-2">
                        {{ __('Cerrar sesión') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
