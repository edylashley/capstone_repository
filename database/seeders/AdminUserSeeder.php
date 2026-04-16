<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Repository Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Models\User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'adviser@example.com'],
            [
                'name' => 'Faculty Adviser',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Models\User::ROLE_ADVISER,
                'is_active' => true,
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Sample Student',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Models\User::ROLE_STUDENT,
                'is_active' => true,
            ]
        );
    }
}
