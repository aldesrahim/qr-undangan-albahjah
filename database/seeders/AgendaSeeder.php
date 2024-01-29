<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agenda::create([
            'name' => 'Merajut Cinta Menuju Bulan Mulia 1445 H',
            'started_at' => \carbon('2024-02-20 06:30:00'),
            'finished_at' => null,
        ]);
    }
}
