<?php

namespace App\Filament\Staff\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;

class CheckIn extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.staff.pages.check-in';

    #[On('scan-success')]
    public function onScanSuccess($data)
    {
        //        dd($data);
        // $this->dispatch('resume-scan');
    }
}
