<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportVisitorResource\Pages;
use App\Filament\Resources\ReportVisitorResource\Widgets;
use App\Models\Visitor;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportVisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Report';

    protected static ?string $label = 'Tamu Undangan';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('agenda.name')
                            ->translateLabel(),
                        Infolists\Components\TextEntry::make('agenda.started_at')
                            ->label(__('Started at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('agenda.finished_at')
                            ->label(__('Finished at'))
                            ->dateTime(),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Tamu Undangan')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->translateLabel(),
                        Infolists\Components\TextEntry::make('address')->translateLabel(),
                        Infolists\Components\TextEntry::make('phone_number')->translateLabel(),
                        Infolists\Components\TextEntry::make('genderCategories.name')->translateLabel(),
                        Infolists\Components\TextEntry::make('colorCategories.name')->translateLabel(),
                        Infolists\Components\TextEntry::make('invitation.companion')
                            ->label(__('Companion')),
                        Infolists\Components\TextEntry::make('check_ins_count')
                            ->label(__('Check in')),
                    ])
                    ->columns(3),
                Infolists\Components\RepeatableEntry::make('checkIns')
                    ->label(__('Check in'))
                    ->schema([
                        Infolists\Components\TextEntry::make('checked_in_at')
                            ->translateLabel()
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('gate.name')
                            ->translateLabel(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('Staff')),
                    ])
                    ->columns(3)
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled($record->checkIns)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->translateLabel()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('genderCategories.name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('colorCategories.name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invitation.oldestCheckIn.checked_in_at_label')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('invitation.latestCheckIn.checked_in_at_label')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invitation.companion')
                    ->label(__('Companion'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_ins_count')
                    ->label(__('Check in'))
                    ->alignCenter()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->emptyStateDescription('Silakan perluas pencarian untuk menampilkan data');
    }

    public static function getTableQuery(): Builder
    {
        return Visitor::query()
            ->with([
                'genderCategories',
                'colorCategories',
                'invitation',
                'invitation.oldestCheckIn',
                'invitation.latestCheckIn',
            ])
            ->withCount('checkIns');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportVisitors::route('/'),
            'view' => Pages\ViewReportVisitor::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\FilterForm::class,
            Widgets\VisitorStats::class,
        ];
    }
}
