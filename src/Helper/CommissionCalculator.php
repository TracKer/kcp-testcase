<?php

namespace App\Helper;

use App\Exception\PropertyNotSetException;

class CommissionCalculator
{
    private const EUROPEAN_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES',
        'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    private ?string $countryCode = null;
    private ?string $currency = null;
    private ?float $rate = null;
    private ?float $amount = null;

    /**
     * @return float
     * @throws PropertyNotSetException
     */
    public function calculateCommission(): float
    {
        if ($this->countryCode === null) {
            throw new PropertyNotSetException('Country code is not set');
        }

        if ($this->currency === null) {
            throw new PropertyNotSetException('Currency is not set');
        }

        if ($this->rate === null) {
            throw new PropertyNotSetException('Rate is not set');
        }

        if ($this->amount === null) {
            throw new PropertyNotSetException('Amount is not set');
        }

        $value = $this->amount;

        if (($this->currency != 'EUR') || ($this->rate > 0)) {
            $value /= $this->rate;
        }

        $multiplier = $this->isEuropeanCountry($this->countryCode) ? 0.01 : 0.02;

        $commission = $value * $multiplier;

        // Apply ceiling
        $commission = ceil($commission * 100) / 100;

        return $commission;
    }

    public function setCountryCode(string $countryCode): CommissionCalculator
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function setCurrency(string $currency): CommissionCalculator
    {
        $this->currency = $currency;
        return $this;
    }

    public function setRate(float $rate): CommissionCalculator
    {
        $this->rate = $rate;
        return $this;
    }

    public function setAmount(float $amount): CommissionCalculator
    {
        $this->amount = $amount;
        return $this;
    }

    private static function isEuropeanCountry($countryCode): bool
    {
        return in_array($countryCode, self::EUROPEAN_COUNTRIES);
    }
}
