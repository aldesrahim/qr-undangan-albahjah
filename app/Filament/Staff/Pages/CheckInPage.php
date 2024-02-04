<?php

namespace App\Filament\Staff\Pages;

use App\Models\Agenda;
use App\Models\CheckIn;
use App\Models\Gate;
use App\Models\Invitation;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class CheckInPage extends Page implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.staff.pages.check-in';

    public array $visitorData = [];

    public array $checkInData = [
        'check_in_person' => null,
        'gate_id' => null,
    ];

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

    public function visitorInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->visitorData)
            ->schema([
                Infolists\Components\Fieldset::make('Agenda')
                    ->schema([
                        Infolists\Components\TextEntry::make('agenda_name')
                            ->label('Nama'),
                        Infolists\Components\TextEntry::make('agenda_time')
                            ->label('Waktu'),
                        Infolists\Components\TextEntry::make('agenda_gates')
                            ->label('Daftar Gerbang'),
                    ]),
                Infolists\Components\Fieldset::make('Tamu')
                    ->schema([
                        Infolists\Components\TextEntry::make('visitor_name')
                            ->label('Nama'),
                        Infolists\Components\TextEntry::make('visitor_address')
                            ->label('Alamat'),
                        Infolists\Components\TextEntry::make('visitor_phone_number')
                            ->label('Nomor telepon'),
                        Infolists\Components\TextEntry::make('visitor_checked_in')
                            ->label('Jumlah Orang'),
                        Infolists\Components\TextEntry::make('visitor_categories')
                            ->label('Kategori'),
                    ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('checkInData')
            ->schema([
                Forms\Components\TextInput::make('check_in_person')
                    ->label('Jumlah orang')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                Forms\Components\Select::make('gate_id')
                    ->label('Gerbang')
                    ->options($this->staffGates->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('submit')
                        ->extraAttributes(['type' => 'submit'])
                        ->label('Check in'),
                    Forms\Components\Actions\Action::make('cancel')
                        ->extraAttributes(['type' => 'button'])
                        ->label('Batal')
                        ->color('secondary')
                        ->action('cancel')
                ])
                ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function invalidQrCodeNotification(?string $message = null): void
    {
        Notification::make()
            ->title('Kode QR tidak valid')
            ->body($message)
            ->danger()
            ->send();
    }

    public function cancel()
    {
        $this->visitorData = [];
        $this->checkInData = [];

        $this->dispatch('resume-scan');
        $this->dispatch('close-modal', id: 'scan-result');
    }

    public function checkIn(): void
    {
        $invitationId = $this->visitorData['invitation_id'] ?? null;

        if (blank($invitationId)) {
            Notification::make()
                ->title('Scan Kode QR terlebih dahulu')
                ->danger()
                ->send();

            return;
        }

        $invitation = Invitation::query()
            ->withCount('checkIns')
            ->find($invitationId);

        if (blank($invitation)) {
            Notification::make()
                ->title('Undangan tidak valid')
                ->danger()
                ->send();

            return;
        }

        if ($invitation->check_ins_count >= $invitation->person) {
            Notification::make()
                ->title('Tidak dapat melanjutkan proses check in')
                ->body('Tamu dan para pendamping sudah melakukan check in sebelumnya')
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();

        $totalPerson = $invitation->check_ins_count + $data['check_in_person'];
        $remainingPerson = $invitation->person - $invitation->check_ins_count;

        if ($totalPerson > $invitation->person) {
            Notification::make()
                ->title('Tidak dapat melanjutkan proses check in')
                ->body(sprintf('Slot hanya tersisa untuk %s orang lagi', $remainingPerson))
                ->danger()
                ->send();

            return;
        }

        $registeredGates = $invitation->agenda->gates->pluck('id')->toArray();
        $gate = Gate::find($data['gate_id']);

        if (!in_array($data['gate_id'], $registeredGates)) {
            Notification::make()
                ->title('Anda tidak terdaftar di gerbang: ' . $gate?->name)
                ->danger()
                ->send();

            return;
        }

        session()->put('last-gate-id', $data['gate_id']);

        CheckIn::create([
            'invitation_id' => $invitationId,
            'gate_id' => $data['gate_id'],
            'user_id' => auth()->id(),
            'checked_in_at' => now(),
        ]);

        Notification::make()
            ->title('Check in berhasil')
            ->success()
            ->send();

        $this->cancel();
    }

    #[On('scan')]
    public function scan($data): void
    {
        if (!str($data)->isUrl()) {
            $this->invalidQrCodeNotification('[NOT VALID URL]');

            return;
        }

        $parsed = parse_url($data);
        $path = $parsed['path'] ?? null;

        if (blank($path)) {
            $this->invalidQrCodeNotification('[EMPTY_PATH]');

            return;
        }

        $segments = explode('/', $path);
        $segmentCount = count($segments);

        if ($segmentCount < 2) {
            $this->invalidQrCodeNotification('[INVALID SEGMENT]');

            return;
        }

        $lastIndex = $segmentCount - 1;
        $invitationCode = $segments[$lastIndex];
        $agendaId = $segments[$lastIndex - 1];

        $invitation = $this->getInvitation($invitationCode, $agendaId);

        if (blank($invitation)) {
            $this->invalidQrCodeNotification('[INVITATION NOT FOUND]');

            return;
        }

        /** @var Agenda $agenda */
        $agenda = $invitation->agenda;
        $visitor = $invitation->visitor;

        $this->visitorData = [
            'invitation_id' => $invitation->id,
            'agenda_name' => $agenda->name,
            'agenda_time' => $agenda->time_label,
            'agenda_gates' => $agenda->gates->pluck('name')->join(', '),
            'visitor_name' => $visitor->name,
            'visitor_address' => $visitor->address,
            'visitor_phone_number' => $visitor->phone_number,
            'visitor_categories' => $visitor->categories->pluck('label')->join(', '),
            'visitor_checked_in' => sprintf(
                '%s / %s',
                $invitation->visitor_checked_in,
                $invitation->person
            ),
        ];

        $this->checkInData = [
            'check_in_person' => 1,
            'gate_id' => $this->getLastGateId(),
        ];

        $this->dispatch('open-modal', id: 'scan-result');
    }
}
