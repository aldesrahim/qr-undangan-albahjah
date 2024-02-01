<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategoryType: string implements HasLabel
{
    case GENDER = 'gender';

    case COLOR = 'color';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GENDER => __('Gender'),
            self::COLOR => __('Color'),
        };
    }
}
