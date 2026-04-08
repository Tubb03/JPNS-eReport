<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'ianchw03@gmail.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('jpnsreport123'),
                'role' => 'admin',
            ]
        );
    }
}
