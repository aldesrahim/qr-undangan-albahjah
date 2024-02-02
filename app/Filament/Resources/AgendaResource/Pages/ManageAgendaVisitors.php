<?php

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Filament\Helpers\SafeDeleteAction;
use App\Filament\Resources\AgendaResource;
use App\Models\Invitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ManageAgendaVisitors extends ManageRelatedRecords
{
    protected static string $resource = AgendaResource::class;

    protected static string $relationship = 'visitors';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Visitors');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->translateLabel(),
                Forms\Components\Textarea::make('address')
                    ->translateLabel()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->label)
                    ->multiple()
                    ->translateLabel()
                    ->required()
                    ->preload(),
                Forms\Components\Fieldset::make(__('Invitation'))
                    ->relationship('invitation')
                    ->schema([
                        Forms\Components\Placeholder::make('scan_url')
                            ->content(fn ($record) => $record->scan_url)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('companion')
                            ->translateLabel()
                            ->numeric()
                            ->required()
                            ->minValue(fn (?Invitation $record) => -1 + $record?->checkIns()?->count() ?? 1)
                            ->columns(1),
                        Forms\Components\Hidden::make('code')->default(Invitation::generateCode())
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('genderCategories.name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('colorCategories.name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invitation.companion')
                    ->label(__('Companion'))
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_ins_count')
                    ->label(__('Check in'))
                    ->alignCenter()
                    ->counts('checkIns')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
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
            ->filters($this->getTableCategoryFilters())
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->action(SafeDeleteAction::setUp(['checkIns'])),
            ]);
    }

    public function getTableCategoryFilters(): array
    {
        $relationships = [
            'genderCategories',
            'colorCategories',
        ];

        return collect($relationships)
            ->map(
                fn ($relationship) => Tables\Filters\SelectFilter::make(Str::snake($relationship))
                    ->translateLabel()
                    ->relationship($relationship, 'name')
                    ->multiple()
                    ->preload()
            )
            ->toArray();
    }
}
