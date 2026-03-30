<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear o recuperar usuario administrador
        $usuario = User::firstOrCreate(
            ['email' => '122041665@upq.edu.mx'],
            [
                'name' => 'Administrador UPQ',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Crear o recuperar rol admin
        $rolAdmin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Asignar rol al usuario (solo si no lo tiene)
        if (! $usuario->hasRole('admin')) {
            $usuario->assignRole($rolAdmin);
        }
    }
}
