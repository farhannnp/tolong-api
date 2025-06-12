<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 
use App\Models\User; 

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Password default: 'password'
                'role' => 'admin', // Ini yang paling penting: set role jadi 'admin'
                'department' => 'IT', // Sesuaikan dengan departemen default
                'batch' => '2020',    // Sesuaikan dengan angkatan default
                // Tambahkan kolom lain jika ada yang required di model User Anda
            ]);

            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }

}