<?php

namespace App\Filament\Staff\Pages;

use App\Models\Agenda;
use App\Models\Invitation;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class CheckInPage extends Page implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;
    use CheckInPageConcerns\HandlesCheckInForm;
    use CheckInPageConcerns\HandlesManualSearchForm;
    use CheckInPageConcerns\HandlesVisitorInfolist;
    use CheckInPageConcerns\HandlesScan;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.staff.pages.check-in';

    public function mount()
    {
        $this->checkInData = [
            'check_in_person' => 1,
            'gate_id' => $this->getLastGateId(),
        ];

        $this->manualSearchData = [
            'agenda_id' => $this->getLastAgendaId(),
            'visitor_id' => null,
        ];
    }

    #[Computed]
    public function staffGates()
    {
        return auth()->user()->gates()->get();
    }

    public function getInvitation(string $code, string $agendaId): ?Invitation
    {
        return Invitation::query()
            ->with([
                'agenda.gates',
                'visitor.categories',
            ])
            ->withCount('checkIns as visitor_checked_in')
            ->whereHas('agenda', fn ($query) => $query->where('agendas.id', $agendaId))
            ->where('code', $code)
            ->first();
    }

    public function getLastAgendaId(): ?string
    {
        $storedId = session()->get('last-agenda-id');

        if (blank($storedId)) {
            return Agenda::value('id');
        }

        $registeredIds = Agenda::pluck('id')->toArray();

        if (!in_array($storedId, $registeredIds)) {
            session()->forget('last-agenda-id');

            return Agenda::value('id');
        }

        return $storedId;
    }

    public function getLastGateId(): ?string
    {
        $storedId = session()->get('last-gate-id');

        if (blank($storedId)) {
            return null;
        }

        $registeredIds = $this->staffGates->pluck('id')->toArray();

        if (!in_array($storedId, $registeredIds)) {
            session()->forget('last-gate-id');

            return null;
        }

        return $storedId;
    }

    protected function getForms(): array
    {
        return [
            'checkInForm',
            'manualSearchForm',
        ];
    }
}
