<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\Invitation;
use App\Models\Visitor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class VisitorSeeder extends Seeder
{
    /** @var array<Collection> */
    public array $categories;

    public function __construct()
    {
        $this->categories = [
            CategoryType::COLOR->value => Category::where('type', CategoryType::GENDER)->pluck('id'),
            CategoryType::GENDER->value => Category::where('type', CategoryType::COLOR)->pluck('id'),
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agenda = Agenda::first();
        $visitors = Visitor::factory()
            ->count(50)
            ->create([
                'agenda_id' => $agenda->id,
            ]);

        foreach ($visitors as $visitor) {
            $visitor->categories()->attach($this->getCategories());

            Invitation::factory()->create([
                'visitor_id' => $visitor,
            ]);
        }
    }

    public function getCategories(): array
    {
        $gender = $this->categories[CategoryType::GENDER->value]->random();
        $color = $this->categories[CategoryType::COLOR->value]->random();

        return [$gender, $color];
    }
}
