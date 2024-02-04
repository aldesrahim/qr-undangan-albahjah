<?php

namespace App\Filament\Resources\ReportVisitorResource\Widgets;

use App\Enums\VisitorCheckInStatus;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class FilterForm extends Widget implements HasForms
{
    use InteractsWithForms;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.resources.report-visitor-resource.widgets.filter-form';

    public array $data = [
        'agenda_id' => null,
        'check_in_status' => null,
    ];

    public bool $loading = false;

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Select::make('agenda_id')
                    ->label(__('Agenda'))
                    ->options(Agenda::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('check_in_status')
                    ->label('Status check in')
                    ->placeholder('Semua')
                    ->options(VisitorCheckInStatus::class),
            ])
            ->columns(3);
    }

    public function submit()
    {
        $data = $this->form->getState();

        $this->dispatch('filter-submitted', $data);
        $this->loading = true;
    }

    #[On('filter-received')]
    public function onFilterReceived(): void
    {
        $this->loading = false;
    }
}
