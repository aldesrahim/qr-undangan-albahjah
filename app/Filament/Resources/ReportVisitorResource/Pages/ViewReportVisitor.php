<?php

namespace App\Filament\Resources\ReportVisitorResource\Pages;

use App\Filament\Resources\ReportVisitorResource;
use App\Models\Visitor;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewReportVisitor extends ViewRecord
{
    protected static string $resource = ReportVisitorResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        /** @var Visitor $record */
        $record = parent::resolveRecord($key);

        return $record
            ->loadMissing([
                'genderCategories',
                'colorCategories',
                'invitation.oldestCheckIn',
                'invitation.latestCheckIn',
                'agenda.gates',
                'checkIns' => fn ($query) => $query->orderByDesc('checked_in_at'),
                'checkIns.gate',
                'checkIns.user',
            ])
            ->loadCount('checkIns');
    }
}
