<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::macro('toReadableDateTime', function () {
            return $this->isoFormat('dddd, D MMMM Y \J\a\m H:m');
        });

        FilamentAsset::register([
            Js::make('html5-qrcode', 'https://unpkg.com/html5-qrcode')
        ]);
    }
}
