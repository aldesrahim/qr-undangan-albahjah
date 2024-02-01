<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Helpers\SafeDeleteAction;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->action(SafeDeleteAction::setUp(['gates', 'checkIns'])),
        ];
    }
}
