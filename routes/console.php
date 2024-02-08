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
