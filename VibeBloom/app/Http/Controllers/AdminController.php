<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    private const ROOT_ADMIN_ID = 3;

    public function index()
    {
        return view('admin.index');
    }

    public function users()
    {
        $currentUser = auth()->user();

        $usuarios = User::with('roles')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('admin.users', compact('usuarios', 'currentUser'));
    }

    public function places()
    {
        return view('admin.places');
    }

    public function makeAdmin(User $user): RedirectResponse
    {
        $currentUser = auth()->user();

        if (!$currentUser || !$currentUser->hasRole('admin')) {
            abort(403);
        }

        if ((int)$currentUser->id !== self::ROOT_ADMIN_ID) {
            return back()->with('error', 'Solo el administrador principal puede asignar admins.');
        }

        if ((int)$user->id === self::ROOT_ADMIN_ID) {
            return back()->with('info', 'Este usuario ya es el administrador principal.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('info', 'Este usuario ya es admin.');
        }

        $user->syncRoles(['admin']);
        $user->update(['role' => 'admin']);

        return back()->with('success', 'Rol admin asignado correctamente.');
    }

    public function removeAdmin(User $user): RedirectResponse
    {
        $currentUser = auth()->user();

        if (!$currentUser || !$currentUser->hasRole('admin')) {
            abort(403);
        }

        if ((int)$currentUser->id !== self::ROOT_ADMIN_ID) {
            return back()->with('error', 'Solo el administrador principal puede remover admins.');
        }

        if ((int)$user->id === self::ROOT_ADMIN_ID) {
            return back()->with('error', 'No puedes quitar el admin principal.');
        }

        if (!$user->hasRole('admin')) {
            return back()->with('info', 'El usuario no es admin.');
        }

        $user->syncRoles(['moderator']);
        $user->update(['role' => 'moderator']);

        return back()->with('success', 'Admin removido. Ahora es moderator.');
    }
}