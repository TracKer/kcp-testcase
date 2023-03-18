<?php

namespace App\Helper;

class CountryChecker
{
    private const EUROPEAN_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES',
        'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    public function isEuropeanCountry($countryCode): bool
    {
        return in_array($countryCode, self::EUROPEAN_COUNTRIES);
    }
}
