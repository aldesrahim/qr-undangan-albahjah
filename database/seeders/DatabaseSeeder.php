<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(AgendaSeeder::class);
        $this->call(BannerSeeder::class);
        $this->call(GateSeeder::class);
        $this->call(VisitorSeeder::class);
        $this->call(CheckInSeeder::class);
    }
}
