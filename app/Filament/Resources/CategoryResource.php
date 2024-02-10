<?php

namespace App\Filament\Resources;

use App\Enums\CategoryType;
use App\Filament\Helpers\FormSchemaBuilder;
use App\Filament\Helpers\SafeDeleteAction;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSchemaBuilder::withTimestamps([
                    Forms\Components\Select::make('type')
                        ->translateLabel()
                        ->required()
                        ->live()
                        ->options(CategoryType::class)
                        ->afterStateUpdated(function (Forms\Set $set) {
                            $set('name', null);
                        }),
                    Forms\Components\TextInput::make('name')
                        ->translateLabel()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule
                            ->where('type', $get('type'))
                        )
                        ->required()
                        ->maxLength(255),
                    Forms\Components\ColorPicker::make('color')
                        ->translateLabel()
                        ->required(fn (Forms\Get $get) => CategoryType::tryFrom($get('type')) === CategoryType::COLOR)
                        ->visible(fn (Forms\Get $get) => CategoryType::tryFrom($get('type')) === CategoryType::COLOR),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->translateLabel()
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->action(SafeDeleteAction::setUp(['visitors.checkIns', 'gates.checkIns'])),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
