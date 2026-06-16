<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate: if record exists, update it. If not, create it.
        // This way re-running seeders doesn't create duplicate admins.
        User::updateOrCreate(
            ['email' => 'admin@taddabur.com'],
            [
                'name'       => 'Admin',
                'password'   => Hash::make('admin123'),
                'plan'       => 'premium',
                'is_admin'   => true,
            ]
        );

        $this->command->info('✅ Admin seeded: admin@taddabur.com / admin123');
        $this->command->warn('⚠️  Change the admin password before going live!');
    }
}
