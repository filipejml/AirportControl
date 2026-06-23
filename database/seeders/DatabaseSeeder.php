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
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => Hash::make('123456'),
                'tipo' => 0,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['username' => 'usuario'],
            [
                'name' => 'Usuário Comum',
                'email' => 'usuario@example.com',
                'password' => Hash::make('123456'),
                'tipo' => 1,
                'email_verified_at' => now(),
            ]
        );
    }
}
