<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the administrator account from the environment (ADMIN_EMAIL /
     * ADMIN_PASSWORD), so no credentials live in the repository.
     *
     * Safe to run on every deploy: an existing administrator is left as it is,
     * so a password changed later from the Profile page is never overwritten.
     */
    public function run(): void
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (User::where('role', 'admin')->exists()) {
            $this->command?->info('Administrator account already exists; left unchanged.');

            return;
        }

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD not set; administrator account not created.');

            return;
        }

        User::create([
            'name' => 'SELFCURE PKS Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->command?->info("Administrator account created for {$email}.");
    }
}
