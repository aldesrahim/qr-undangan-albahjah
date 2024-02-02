<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\FormSchemaBuilder;
use App\Filament\Helpers\SafeDeleteAction;
use App\Filament\Resources\AgendaResource\Pages;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSchemaBuilder::withTimestamps([
                    Forms\Components\TextInput::make('name')
                        ->translateLabel()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('started_at')
                        ->translateLabel()
                        ->required(),
                    Forms\Components\DateTimePicker::make('finished_at')
                        ->translateLabel()
                        ->helperText('Kosongkan jika waktu tidak menentu'),
                    Forms\Components\Textarea::make('short_description')
                        ->translateLabel()
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->translateLabel()
                        ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finished_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->action(
                    SafeDeleteAction::setUp(['visitors.checkIns', 'gates.checkIns'])
                ),
            ]);
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
            'banners' => Pages\ManageAgendaBanners::route('/{record}/banners'),
            'gates' => Pages\ManageAgendaGates::route('/{record}/gates'),
            'visitors' => Pages\ManageAgendaVisitors::route('/{record}/visitors'),
        ];
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditAgenda::class,
            Pages\ManageAgendaBanners::class,
            Pages\ManageAgendaGates::class,
            Pages\ManageAgendaVisitors::class,
        ]);
    }
}
