<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => Hash::make('secret'),
        ]);

        $admin->assignRole(UserRole::ADMIN);

        collect(range(1, 3))
            ->each(function ($i) {
                $staff = User::factory()->create([
                    'name' => "Staff $i",
                    'email' => "staff.$i@mail.com",
                    'password' => Hash::make('secret'),
                ]);

                $staff->assignRole(UserRole::STAFF);
            });
    }
}
