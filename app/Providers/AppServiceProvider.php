<?php

namespace App\Providers;

use Filament\Facades\Filament;
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
        $this->usePaths();
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
            Js::make('html5-qrcode', 'https://unpkg.com/html5-qrcode'),
            Js::make('clipboard-js', 'https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js'),
        ]);

        Filament::registerNavigationGroups([
            'Report',
        ]);

        $this->usePaths();
    }

    public function usePaths(): void
    {
        $publicPath = config('filesystems.public_path');
        $storagePath = config('filesystems.storage_path');

        if ($publicPath) {
            app()->usePublicPath(
                realpath(base_path($publicPath))
            );
        }

        if ($storagePath) {
            app()->useStoragePath(
                realpath(base_path($storagePath))
            );
        }
    }
}
