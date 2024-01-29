<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Enums\UserRole;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\Gate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class GateSeeder extends Seeder
{
    /** @var array<Collection> */
    public array $categories;

    public Collection $staffs;

    public function __construct()
    {
        $this->categories = [
            CategoryType::COLOR->value => Category::where('type', CategoryType::GENDER)->pluck('id'),
            CategoryType::GENDER->value => Category::where('type', CategoryType::COLOR)->pluck('id'),
        ];

        $this->staffs = User::role(UserRole::STAFF)->pluck('id');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agenda = Agenda::first();
        $gates = [
            ['agenda_id' => $agenda->id, 'name' => 'Gerbang 1'],
            ['agenda_id' => $agenda->id, 'name' => 'Gerbang 2'],
            ['agenda_id' => $agenda->id, 'name' => 'Gerbang 3'],
            ['agenda_id' => $agenda->id, 'name' => 'Gerbang 4'],
        ];

        foreach ($gates as $gate) {
            $gate = Gate::create($gate);
            $gate->categories()->attach($this->getCategories());
            $gate->users()->attach($this->staffs->random());
        }
    }

    public function getCategories(): array
    {
        $gender = $this->categories[CategoryType::GENDER->value]->random();
        $color = $this->categories[CategoryType::COLOR->value]->random();

        return [$gender, $color];
    }
}
