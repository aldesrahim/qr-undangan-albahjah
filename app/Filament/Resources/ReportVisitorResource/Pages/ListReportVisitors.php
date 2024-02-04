<?php

namespace App\Filament\Resources\ReportVisitorResource\Pages;

use App\Enums\VisitorCheckInStatus;
use App\Filament\Resources\ReportVisitorResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListReportVisitors extends ListRecords
{
    protected static string $resource = ReportVisitorResource::class;

    public array $widgetFilterData = [];

    protected function getHeaderWidgets(): array
    {
        return [
            ReportVisitorResource\Widgets\FilterForm::class,
            ReportVisitorResource\Widgets\VisitorStats::class,
        ];
    }

    public function table(Table $table): Table
    {
        /** @var Builder $baseQuery */
        $baseQuery = static::$resource::getTableQuery();

        $agendaId = $this->widgetFilterData['agenda_id'] ?? null;
        $checkInStatus = $this->widgetFilterData['check_in_status'] ?? '';

        return parent::table($table)
            ->query(
                $baseQuery
                    ->whereHas('agenda', fn ($query) => $query->where('agendas.id', $agendaId))
                    ->when(
                        VisitorCheckInStatus::tryFrom($checkInStatus),
                        fn ($query, $value) => $query
                        ->whereHas('invitation', fn ($query) => $query->checkInStatus($value))
                    )
            );
    }

    #[On('filter-submitted')]
    public function onFilterSubmitted($data)
    {
        $this->widgetFilterData = $data;

        $this->resetTable();
        $this->dispatch('filter-received');
    }
}
