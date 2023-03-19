<?php

namespace App\Provider\Rate;

interface RateProviderInterface
{
    public function getRate(string $currency): float;
}
