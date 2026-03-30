<x-app-layout>
    <div class="px-6 py-4 bg-white border-b">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de usuarios</h1>
        <p class="text-sm text-gray-500 mt-1">Administra usuarios y jerarquías dentro de la plataforma.</p>
    </div>

    <div class="px-6 py-6 space-y-6">
        <a href="{{ route('admin.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow">
            ← Volver al panel
        </a>

        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-blue-700">
                {{ session('info') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ID</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Nombre</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Correo</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Rol</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($usuarios as $usuario)
                            @php
                                $userRole = $usuario->roles->pluck('name')->first() ?? ($usuario->role ?? 'Sin rol');
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-700">{{ $usuario->id }}</td>
                                <td class="px-4 py-4 font-medium text-gray-900">{{ $usuario->name }}</td>
                                <td class="px-4 py-4 text-gray-600">{{ $usuario->email }}</td>
                                <td class="px-4 py-4">
                                    @if($userRole === 'admin')
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-100">
                                            Admin
                                        </span>
                                    @elseif($userRole === 'moderator')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                                            Moderator
                                        </span>
                                    @elseif($userRole === 'user')
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 border border-gray-200">
                                            User
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700 border border-yellow-100">
                                            Sin rol
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @if($currentUser->id === 3 && $usuario->id !== 3 && $userRole !== 'admin')
                                            <form action="{{ route('admin.makeAdmin', $usuario) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow">
                                                    Hacer admin
                                                </button>
                                            </form>
                                        @endif

                                        @if($currentUser->id === 3 && $usuario->id !== 3 && $userRole === 'admin')
                                            <form action="{{ route('admin.removeAdmin', $usuario) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow">
                                                    Quitar admin
                                                </button>
                                            </form>
                                        @endif

                                        @if($usuario->id === 3)
                                            <span class="px-3 py-2 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold border border-gray-200">
                                                Administrador principal
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4 border-t border-gray-100 bg-white">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</x-app-layout>