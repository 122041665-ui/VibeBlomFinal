<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles si no existen
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user  = Role::firstOrCreate(['name' => 'user']);

        // Buscar al usuario 2
        $usuario = User::find(2);

        // Asignar rol admin si el usuario existe
        if ($usuario) {
            $usuario->assignRole($admin);
        }
    }
}
