<?php

namespace App\Filament\Staff\Pages\CheckInPageConcerns;

use App\Models\Agenda;
use App\Models\Visitor;
use Filament\Forms;
use Filament\Forms\Form;

trait HandlesManualSearchForm
{
    public array $manualSearchData = [];

    public function manualSearchForm(Form $form): Form
    {
        return $form
            ->statePath('manualSearchData')
            ->model(Agenda::findOrFail($this->getLastAgendaId()))
            ->schema([
                Forms\Components\Select::make('agenda_id')
                    ->label(__('Agenda'))
                    ->required()
                    ->selectablePlaceholder(false)
                    ->options(Agenda::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('visitor_id')
                    ->label(__('Visitor'))
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn (string $search, Forms\Get $get) => Visitor::query()
                            ->with(['invitation'])
                            ->where('agenda_id', $get('agenda_id'))
                            ->invited()
                            ->search($search)
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (Visitor $visitor) => [
                                $visitor->getKey() => static::buildVisitorOption($visitor),
                            ])
                            ->toArray()
                    )
                    ->getOptionLabelUsing(function ($value) {
                        $visitor = Visitor::query()
                            ->with(['invitation'])
                            ->find($value);

                        return $visitor ? static::buildVisitorOption($visitor) : '-';
                    })
                    ->allowHtml()
                    ->helperText('Anda bisa mencari menggunakan: nama, alamat, nomor telepon atau kode undangan'),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('submit')
                        ->extraAttributes(['type' => 'submit'])
                        ->label('Proses Check in'),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public function buildVisitorOption(Visitor $visitor): string
    {
        $code = $visitor->invitation?->code ?? '-';
        $companion = $visitor->invitation?->companion ?? '-';

        return <<<HTML
<div class="rounded-md relative">
    <div class="flex flex-col justify-center pl-3 py-2">
        <p class="text-sm font-bold pb-1">{$visitor->name}</p>
        <div class="flex flex-col items-start">
            <p class="text-xs leading-5">Nomor Telepon: {$visitor->phone_number}</p>
            <p class="text-xs leading-5">Alamat: {$visitor->address}</p>
            <p class="text-xs leading-5">Kode: $code</p>
            <p class="text-xs leading-5">Pendamping: $companion</p>
        </div>
    </div>
</div>
HTML;
    }

    public function manualSearch(): void
    {
        $data = $this->manualSearchForm->getState();

        $visitor = Visitor::query()
            ->where('agenda_id', $data['agenda_id'])
            ->invited()
            ->findOrFail($data['visitor_id']);

        $this->scan($visitor->invitation->scan_url);
        $this->dispatch('close-modal', id: 'manual-search');
    }
}
