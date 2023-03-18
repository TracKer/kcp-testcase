<?php

namespace App\Provider\Transaction;

use App\Dto\TransactionDto;

interface TransactionProviderInterface
{
    /**
     * @return iterable|TransactionDto[]
     */
    public function getList(): iterable;
}
