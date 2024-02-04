<?php

namespace App\Filament\Resources\ReportVisitorResource\Widgets;

use App\Enums\VisitorCheckInStatus;
use App\Filament\Resources\ReportVisitorResource\Pages\ListReportVisitors;
use App\Models\Invitation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class VisitorStats extends BaseWidget
{
    public array $widgetFilterData = [];

    protected function getTablePage(): string
    {
        return ListReportVisitors::class;
    }

    protected function getStats(): array
    {
        $agendaId = $this->widgetFilterData['agenda_id'] ?? null;
        $checkInStatus = $this->widgetFilterData['check_in_status'] ?? '';

        $baseQuery = Invitation::query()
            ->whereHas('agenda', fn ($query) => $query->where('agendas.id', $agendaId))
            ->when(
                VisitorCheckInStatus::tryFrom($checkInStatus),
                fn ($query, $value) => $query
                ->checkInStatus($value)
            );

        $totalPerson = $baseQuery->sum('person') ?? 0;
        $totalCheckedIn = $baseQuery->withCount('checkIns')->value('check_ins_count') ?? 0;
        $totalNotCheckedIn = $totalPerson - $totalCheckedIn;

        return [
            Stat::make('Tamu', $baseQuery->count()),
            Stat::make('Tamu + Pendamping', $totalPerson),
            Stat::make('Tamu + Pendamping Hadir', $totalCheckedIn),
            Stat::make('Tamu + Pendamping Tidak Hadir', $totalNotCheckedIn),
        ];
    }

    #[On('filter-submitted')]
    public function onFilterSubmitted($data)
    {
        $this->widgetFilterData = $data;

        $this->dispatch('$refresh');
        $this->dispatch('filter-received');
    }
}
