<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VisitorCheckInStatus: string implements HasLabel
{
    case NOT_CHECKED_IN = 'NOT_CHECKED_IN';

    case PARTIALLY_CHECKED_IN = 'PARTIALLY_CHECKED_IN';

    public function getLabel(): ?string
    {
        return match($this) {
            self::NOT_CHECKED_IN => __('Not checked in'),
            self::PARTIALLY_CHECKED_IN => __('Partially checked in'),
        };
    }
}
