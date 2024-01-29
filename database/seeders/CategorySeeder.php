<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Banat (perempuan)', 'type' => CategoryType::GENDER],
            ['name' => 'Banin (laki-laki)', 'type' => CategoryType::GENDER],
            ['name' => 'Gold', 'type' => CategoryType::COLOR],
            ['name' => 'Pink', 'type' => CategoryType::COLOR],
            ['name' => 'Hijau', 'type' => CategoryType::COLOR],
            ['name' => 'Biru', 'type' => CategoryType::COLOR],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
