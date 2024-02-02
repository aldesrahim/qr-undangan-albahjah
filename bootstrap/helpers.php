<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

if (!function_exists('carbon')) {
    function carbon($time = null, $tz = null): Carbon
    {
        return new Carbon($time, $tz);
    }
}

if (!function_exists('normalize_phone_number')) {
    function normalize_phone_number($phoneNumber): string
    {
        $phoneNumber = str($phoneNumber)->replaceMatches('~\D~', '');
        $phoneNumber = match (true) {
            $phoneNumber->startsWith('6262') => $phoneNumber->replaceFirst('6262', '0'),
            $phoneNumber->startsWith('62') => $phoneNumber->replaceFirst('62', '0'),
            !$phoneNumber->startsWith('0') => $phoneNumber->prepend('0'),
            default => $phoneNumber,
        };

        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $phoneNumberUtil->parse($phoneNumber->toString(), 'ID');
            $phoneNumber = $phoneNumber->getCountryCode() . $phoneNumber->getNationalNumber();
        } catch (\libphonenumber\NumberParseException $e) {
            Log::error('Error normalize phone number', [
                'phone_number' => $phoneNumber,
            ]);
        }

        return $phoneNumber;
    }
}
