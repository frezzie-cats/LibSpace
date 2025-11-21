<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create initial staff account
        User::create([
            'name' => 'Library Staff',
            'email' => 'staff@libspace.test',
            'password' => Hash::make('password123'), // change to a secure password later
            'role' => 'staff',
        ]);

        // Optional: Create a sample student account
        User::create([
            'name' => 'Student 1',
            'email' => 'student@libspace.test',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);
    }
}
