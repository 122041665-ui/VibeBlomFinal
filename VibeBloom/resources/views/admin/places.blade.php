<x-app-layout>
    <div class="px-6 py-4 bg-white border-b">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de lugares</h1>
        <p class="text-sm text-gray-500 mt-1">Administra los lugares publicados en la plataforma.</p>
    </div>

    <div class="px-6 py-6 space-y-6">
        <a href="{{ route('admin.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow">
            ← Volver al panel
        </a>

        <div class="bg-white shadow rounded-2xl border border-gray-100 p-6">
            <p class="text-gray-600">
                Aquí se mostrará la administración de lugares.
            </p>
        </div>
    </div>
</x-app-layout>