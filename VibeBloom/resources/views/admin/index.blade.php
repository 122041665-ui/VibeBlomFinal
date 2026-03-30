<x-app-layout>

    @php
        $currentUser = auth()->user();
    @endphp

    <!-- Título -->
    <div class="px-6 py-4 bg-white border-b">
        <h1 class="text-2xl font-bold">Panel de Administración</h1>
    </div>

    <div class="px-6 py-6">

        <!-- Botón regresar -->
        <a href="{{ route('dashboard') }}"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow mb-4 inline-block">
            ← Volver al dashboard
        </a>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Usuarios -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Usuarios</h2>
                <p class="text-gray-600 mb-4">Gestiona los usuarios registrados en la plataforma.</p>

                <a href="{{ route('admin.users') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg shadow">
                    Ver usuarios
                </a>
            </div>

            <!-- Lugares -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Lugares</h2>
                <p class="text-gray-600 mb-4">Administra los lugares publicados por los usuarios.</p>

                <a href="{{ route('admin.places') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg shadow">
                    Ver lugares
                </a>
            </div>

            <!-- Roles y permisos -->
            @if($currentUser->hasRole('admin'))
            <div class="bg-white shadow rounded-lg p-6 border border-blue-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Roles y permisos</h2>
                <p class="text-gray-600 mb-4">Asigna o revoca el rol de administrador.</p>

                <a href="{{ route('admin.users') }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                    Gestionar roles
                </a>
            </div>
            @else
            <div class="bg-white shadow rounded-lg p-6 opacity-60">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Roles y permisos</h2>
                <p class="text-gray-500 mb-4">Solo disponible para administradores.</p>

                <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg">
                    Sin acceso
                </span>
            </div>
            @endif

        </div>
    </div>

</x-app-layout>