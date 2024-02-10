<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('visitor:fix-phone-number', function () {
    $query = \App\Models\Visitor::query()
        ->where('phone_number', '<>', '')
        ->whereNotNull('phone_number');

    foreach ($query->cursor() as $visitor) {
        $visitor->phone_number = normalize_phone_number($visitor->phone_number);
        $visitor->save();
    }
});

Artisan::command('visitor:fix-addresses', function () {
    \App\Models\Visitor::query()
        ->whereNotNull('address')
        ->each(function (\App\Models\Visitor $visitor) {
            $visitor->update([
                'address' => $visitor->address,
            ]);
        });
});

Artisan::command('categories:generate-colors', function () {
    \App\Models\Category::query()
        ->where('type', \App\Enums\CategoryType::COLOR)
        ->whereNull('color')
        ->each(function (\App\Models\Category $category) {
            $category->update([
                'color' => match (strtolower($category->name)) {
                    'gold' => '#ffd700',
                    'pink' => '#ffc0cb',
                    'hijau' => '#00ff00',
                    'biru' => '#0000ff',
                    default => '#ff0000',
                },
            ]);
        });
});
