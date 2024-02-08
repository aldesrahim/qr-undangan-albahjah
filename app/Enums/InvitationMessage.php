<?php

namespace App\Enums;

enum InvitationMessage: string
{
    case NAME = 'NAME';
    case PHONE_NUMBER = 'PHONE_NUMBER';
    case ADDRESS = 'ADDRESS';
    case COMPANION = 'COMPANION';
    case URL = 'URL';
    case CODE = 'CODE';

    public function placeholder(): string
    {
        return "#{$this->value}#";
    }

    public function description(): string
    {
        return match ($this) {
            self::NAME => 'Nama tamu',
            self::PHONE_NUMBER => 'Nomor telepon tamu',
            self::ADDRESS => 'Alamat tamu',
            self::COMPANION => 'Pendamping tamu',
            self::URL => 'Link undangan',
            self::CODE => 'Kode undangan',
        };
    }

    public static function apply(string $template, array $data): string
    {
        $availableKeys = array_column(InvitationMessage::cases(), 'value');
        $keys = array_keys($data);

        $search = [];
        $replace = [];

        foreach ($keys as $key) {
            if (!in_array($key, $availableKeys)) {
                continue;
            }

            $search[] = self::from($key)->placeholder();
            $replace[] = $data[$key];
        }

        return str_replace($search, $replace, $template);
    }
}
