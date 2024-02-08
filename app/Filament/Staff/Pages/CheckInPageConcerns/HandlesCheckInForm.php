<?php

namespace App\Filament\Staff\Pages\CheckInPageConcerns;

use App\Models\CheckIn;
use App\Models\Gate;
use App\Models\Invitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

trait HandlesCheckInForm
{
    public array $checkInData = [];

    public function checkInForm(Form $form): Form
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

    public function cancel(): void
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

        $data = $this->checkInForm->getState();

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
}
