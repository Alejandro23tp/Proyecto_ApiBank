<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        try {
            User::create([
                'usr_usuario' => 'Loida',
                'usr_correo' => 'euparrales06@gmail.com',
                'password' => Hash::make('Loida06'),
            ]);
            
            $this->command->info('Usuario administrador creado exitosamente.');
        } catch (\Exception $e) {
            $this->command->error('Error al crear usuario: ' . $e->getMessage());
        }
    }
}
