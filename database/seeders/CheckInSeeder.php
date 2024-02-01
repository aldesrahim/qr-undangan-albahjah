<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\CheckIn;
use App\Models\Gate;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Lottery;

class CheckInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gates = Gate::get();
        $users = User::role(UserRole::STAFF)->get();

        foreach (Invitation::cursor() as $invitation) {
            if (!Lottery::odds(2, 5)->choose()) {
                continue;
            }

            $companion = fake()->numberBetween(1, $invitation->companion);

            for ($i = 0; $i < $companion; $i++) {
                CheckIn::create([
                    'invitation_id' => $invitation->id,
                    'gate_id' => $gates->random()->id,
                    'user_id' => $users->random()->id,
                    'checked_in_at' => now(),
                ]);
            }
        }
    }
}
