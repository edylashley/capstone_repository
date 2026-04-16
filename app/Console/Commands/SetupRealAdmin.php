<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupRealAdmin extends Command
{
    protected $signature = 'system:setup-admin';
    protected $description = 'Create a real administrator account for the repository';

    public function handle()
    {
        $name = $this->ask('Enter the Admin Name');
        $email = $this->ask('Enter the Admin Email');
        $password = $this->secret('Enter the Admin Password');
        $confirm = $this->secret('Confirm Password');

        if ($password !== $confirm) {
            $this->error('Passwords do not match!');
            return;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info("Admin account '{$user->email}' created successfully!");
    }
}
