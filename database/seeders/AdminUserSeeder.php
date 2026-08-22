<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador Principal',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'), // Cambiar contraseña por una mas segura
            'role' => 'admin',
        ]);
    }
}
