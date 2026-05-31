<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('ADMIN_EMAIL', ''));
        $password = (string) env('ADMIN_PASSWORD', '');

        if ($email === '' || $password === '' || str_starts_with($password, 'CHANGE_ME')) {
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Vertoni Admin'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );
    }
}
