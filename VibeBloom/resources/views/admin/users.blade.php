<x-app-layout>
    @php
        $currentUser = $currentUser ?? auth()->user();
        $container = "max-w-7xl mx-auto px-6 py-6 pb-32";
        $btnGhost = "inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/80 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-100 font-semibold rounded-xl shadow-sm transition active:scale-[0.99] border border-gray-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30";
    @endphp

    <div class="min-h-screen bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#eef2ff_100%)] dark:bg-[linear-gradient(180deg,#020617_0%,#0f172a_48%,#111827_100%)] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_20%_10%,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.10),transparent_32%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(59,130,246,0.16),transparent_34%),radial-gradient(circle_at_82%_0%,rgba(14,165,233,0.12),transparent_32%)]"></div>

        <div class="{{ $container }} relative space-y-6">
            <section class="rounded-[30px] border border-white/80 dark:border-slate-800 bg-white/88 dark:bg-slate-900/88 backdrop-blur shadow-[0_22px_70px_rgba(15,23,42,0.10)] p-5 sm:p-6 lg:p-7">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300">
                            <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                            Usuarios y roles
                        </div>
                        <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-slate-100 tracking-tight">Gestión de usuarios</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">Administra usuarios y jerarquías dentro de la plataforma.</p>
                    </div>

                    <a href="{{ route('admin.index') }}" class="{{ $btnGhost }}">Volver al panel</a>
                </div>
            </section>

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 dark:border-green-500/30 bg-green-50 dark:bg-green-500/10 px-4 py-3 text-sm font-semibold text-green-700 dark:text-green-300 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-700 dark:text-red-300 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="rounded-2xl border border-blue-200 dark:border-blue-500/30 bg-blue-50 dark:bg-blue-500/10 px-4 py-3 text-sm font-semibold text-blue-700 dark:text-blue-300 shadow-sm">
                    {{ session('info') }}
                </div>
            @endif

            <section class="bg-white/92 dark:bg-slate-900/92 backdrop-blur shadow-sm rounded-[28px] overflow-hidden border border-gray-100 dark:border-slate-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/90 dark:bg-slate-800/70 border-b border-gray-100 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-slate-300">ID</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-slate-300">Nombre</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-slate-300">Correo</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-slate-300">Rol</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-slate-300">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($usuarios as $usuario)
                                @php
                                    $userRole = $usuario->roles->pluck('name')->first() ?? ($usuario->role ?? 'Sin rol');
                                @endphp

                                <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-800/60 transition">
                                    <td class="px-4 py-4 text-gray-600 dark:text-slate-400">{{ $usuario->id }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-900 dark:text-slate-100">{{ $usuario->name }}</td>
                                    <td class="px-4 py-4 text-gray-600 dark:text-slate-400">{{ $usuario->email }}</td>
                                    <td class="px-4 py-4">
                                        @if($userRole === 'admin')
                                            <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-100 dark:bg-red-500/10 dark:text-red-300 dark:border-red-500/20">
                                                Admin
                                            </span>
                                        @elseif($userRole === 'moderator')
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-300 dark:border-blue-500/20">
                                                Moderator
                                            </span>
                                        @elseif($userRole === 'user')
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-gray-700 border border-gray-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                                                User
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20">
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
                                                        class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                                                        Hacer admin
                                                    </button>
                                                </form>
                                            @endif

                                            @if($currentUser->id === 3 && $usuario->id !== 3 && $userRole === 'admin')
                                                <form action="{{ route('admin.removeAdmin', $usuario) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm transition">
                                                        Quitar admin
                                                    </button>
                                                </form>
                                            @endif

                                            @if($usuario->id === 3)
                                                <span class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs font-semibold border border-gray-200 dark:border-slate-700">
                                                    Administrador principal
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-slate-400">
                                        No hay usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-4 border-t border-gray-100 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80">
                    {{ $usuarios->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
