<?php

namespace App\Filament\Helpers;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;

class FormSchemaBuilder
{
    public static function withTimestamps(\Closure|array $formSchema): Grid
    {
        $main = Section::make()
            ->columns(2)
            ->columnSpan(2);

        if ($formSchema instanceof \Closure) {
            $formSchema($main);
        } else {
            $main->schema($formSchema);
        }

        return Grid::make(3)
            ->columnSpanFull()
            ->schema([
                $main,
                Section::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (Model $record) => \carbon($record?->created_at)->diffForHumans()),
                        Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (Model $record) => \carbon($record?->updated_at)->diffForHumans()),
                    ])
                    ->hiddenOn('create')
                    ->columnSpan(1)
            ]);
    }
}
