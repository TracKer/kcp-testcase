<?php

namespace App\Dto;

class TransactionDto
{
    private string $bin;
    private float $amount;
    private string $currency;

    public function getBin(): string
    {
        return $this->bin;
    }

    public function setBin(string $bin): TransactionDto
    {
        $this->bin = $bin;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): TransactionDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): TransactionDto
    {
        $this->currency = $currency;
        return $this;
    }
}
