<?php

namespace App\Provider\Transaction;

use App\Dto\TransactionDto;

class TransactionFileProvider implements TransactionProviderInterface
{
    private string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getList(): iterable
    {
        $file = fopen($this->fileName, "r");
        if (!is_resource($file)) {
            return;
        }

        while (($line = fgets($file)) !== false) {
            $dataItem = json_decode(trim($line), true, flags: JSON_THROW_ON_ERROR);

            $item = new TransactionDto();
            $item->setBin($dataItem['bin']);
            $item->setAmount(floatval($dataItem['amount']));
            $item->setCurrency($dataItem['currency']);

            yield $item;
        }

        fclose($file);
    }
}
