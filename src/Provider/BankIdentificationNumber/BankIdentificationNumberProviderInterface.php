<?php

namespace App\Provider\BankIdentificationNumber;

interface BankIdentificationNumberProviderInterface
{
    public function getCountry(string $bin): string;
}
