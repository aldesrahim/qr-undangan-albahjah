<?php

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Enums\UserRole;
use App\Filament\Helpers\SafeDeleteAction;
use App\Filament\Resources\AgendaResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ManageAgendaGates extends ManageRelatedRecords
{
    protected static string $resource = AgendaResource::class;

    protected static string $relationship = 'gates';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Gates');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('users')
                    ->label('Staff')
                    ->relationship('users', 'name', fn ($query) => $query->role(UserRole::STAFF))
                    ->searchable(['name', 'email'])
                    ->multiple()
                    ->preload()
                    ->required(),
                Forms\Components\Fieldset::make('Categories')
                    ->translateLabel()
                    ->schema($this->getCategoriesForm()),
            ]);
    }

    public function getCategoriesForm(): array
    {
        $relations = [
            'genderCategories',
            'colorCategories',
        ];

        return collect($relations)
            ->map(
                fn ($relation) => Forms\Components\Select::make(Str::snake($relation))
                    ->translateLabel()
                    ->relationship($relation, 'name')
                    ->required()
            )
            ->toArray();
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('colorCategories.name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make()->action(SafeDeleteAction::setUp('checkIns')),
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
