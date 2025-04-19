<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create HC admin
        User::create([
            'name' => 'HC Admin',
            'email' => 'hc@example.com',
            'password' => Hash::make('daffa123'),
            'role' => 'hc',
            'department_id' => 1, // HC Department
            'employee_id' => 'HC001',
            'position' => 'HC Manager',
        ]);

        // Create IT support
        User::create([
            'name' => 'IT Support',
            'email' => 'it@example.com',
            'password' => Hash::make('daffa123'),
            'role' => 'it',
            'department_id' => 2, // IT Department
            'employee_id' => 'IT001',
            'position' => 'IT Support Specialist',
        ]);

        // Create GA support
        User::create([
            'name' => 'GA Support',
            'email' => 'ga@example.com',
            'password' => Hash::make('daffa123'),
            'role' => 'ga',
            'department_id' => 3, // GA Department
            'employee_id' => 'GA001',
            'position' => 'GA Support Staff',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('daffa123'),
            'role' => 'user',
            'department_id' => 4, // Finance Department
            'employee_id' => 'FIN001',
            'position' => 'Staff',
        ]);
    }
}
