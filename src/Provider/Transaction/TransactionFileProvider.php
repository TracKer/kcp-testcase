<?php

namespace App\Provider\Transaction;

use App\Dto\TransactionDto;
use App\Exception\FileAccessException;
use App\Exception\FileDoesNotExistException;
use App\Exception\UnsupportedDataStructureException;

class TransactionFileProvider implements TransactionProviderInterface
{
    private string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getList(): iterable
    {
        if (!file_exists($this->fileName)) {
            throw new FileDoesNotExistException('Transaction file not found');
        }

        $file = fopen($this->fileName, "r");
        if (!is_resource($file)) {
            throw new FileAccessException('Unable to open transaction file for reading');
        }

        try {
            while (($line = fgets($file)) !== false) {
                $dataItem = json_decode(trim($line), true, flags: JSON_THROW_ON_ERROR);

                $item = new TransactionDto();
                $item->setBin($dataItem['bin']);
                $item->setAmount(floatval($dataItem['amount']));
                $item->setCurrency($dataItem['currency']);

                yield $item;
            }
        } catch (\OutOfRangeException|\JsonException $e) {
            throw new UnsupportedDataStructureException('Unsupported data structure of transaction file', previous: $e);
        }

        fclose($file);
    }
}
