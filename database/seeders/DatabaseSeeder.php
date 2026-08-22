<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        Admin::query()->firstOrCreate(
            ['email' => 'admin@tadika-amal.local'],
            [
                'name' => 'Guru Besar / Pengurus',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'cikgu@tadika-amal.local'],
            [
                'name' => 'Cikgu Aisyah (Guru Kelas)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call(TadikaAmalKspkSeeder::class);
    }
}

