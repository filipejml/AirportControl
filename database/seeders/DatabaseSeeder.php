<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário Admin (tipo = 0)
        User::create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'tipo' => 0, // 0 = admin
        ]);

        // Criar usuário Comum (tipo = 1)
        User::create([
            'name' => 'Usuário Comum',
            'username' => 'usuario',
            'email' => 'usuario@example.com',
            'password' => Hash::make('123456'),
            'tipo' => 1, // 1 = usuário comum
        ]);
    }
}