<?php

namespace App\Filament\Staff\Pages\CheckInPageConcerns;

use Filament\Infolists;
use Filament\Infolists\Infolist;

trait HandlesVisitorInfolist
{
    public array $visitorData = [];

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
}
