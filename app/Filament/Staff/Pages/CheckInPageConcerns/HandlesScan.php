<?php

namespace App\Filament\Staff\Pages\CheckInPageConcerns;

use App\Models\Agenda;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

trait HandlesScan
{
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

        $visitor->loadMissing([
            'genderCategories',
            'colorCategories',
        ]);

        $this->visitorData = [
            'invitation_id' => $invitation->id,
            'agenda_name' => $agenda->name,
            'agenda_time' => $agenda->time_label,
            'agenda_gates' => $agenda->gates->pluck('name')->join(', '),
            'visitor_name' => $visitor->name,
            'visitor_address' => $visitor->address,
            'visitor_phone_number' => $visitor->phone_number,
            'visitor_genders' => $visitor->genderCategories->pluck('name')->join(', '),
            'visitor_colors' => $visitor->colorCategories,
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

    public function invalidQrCodeNotification(?string $message = null): void
    {
        Notification::make()
            ->title('Kode QR tidak valid')
            ->body($message)
            ->danger()
            ->send();
    }
}
