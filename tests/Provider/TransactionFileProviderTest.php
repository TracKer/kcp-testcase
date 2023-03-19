<?php

namespace App\Tests\Provider;

use App\Dto\TransactionDto;
use App\Exception\FileAccessException;
use App\Exception\FileDoesNotExistException;
use App\Exception\UnsupportedDataStructureException;
use App\Provider\Transaction\TransactionFileProvider;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class TransactionFileProviderTest extends TestCase
{
    private vfsStreamDirectory $fs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = vfsStream::setup();
    }

    public function testGetListReturnsData(): void
    {
        $data = [
            '{"bin":"123456","amount":"12.34","currency":"USD"}',
            '{"bin":"987654","amount":"56.78","currency":"EUR"}',
        ];
        $filePath = vfsStream::url('root/input.txt');
        file_put_contents($filePath, implode(PHP_EOL, $data));
        $provider = new TransactionFileProvider($filePath);

        $result = iterator_to_array($provider->getList());

        $this->assertCount(2, $result);
        foreach ($result as $key => $item) {
            $dataRow = json_decode($data[$key], true);
            $this->assertInstanceOf(TransactionDto::class, $item);
            $this->assertEquals($dataRow['bin'], $item->getBin());
            $this->assertEquals($dataRow['amount'], $item->getAmount());
            $this->assertEquals($dataRow['currency'], $item->getCurrency());
        }
    }

    public function testGetListThrowsFileDoesNotExistExceptionIfFileNotFound(): void
    {
        $filePath = vfsStream::url('root/input.txt');
        $provider = new TransactionFileProvider($filePath);

        $this->expectException(FileDoesNotExistException::class);
        iterator_to_array($provider->getList());
    }

    public function testGetListThrowsFileAccessExceptionIfUnableToOpenFile(): void
    {
        $filePath = vfsStream::url('root/input.txt');
        vfsStream::newFile('input.txt', 0000)->at($this->fs);
        $provider = new TransactionFileProvider($filePath);

        $this->expectException(FileAccessException::class);
        iterator_to_array($provider->getList());
    }

    public function testGetListThrowsUnsupportedDataStructureExceptionIfUnsupportedDataStructure(): void
    {
        $data = [
            '{"bin":"123456","amount":"12.34","currency":"USD"}',
            'invalid json string',
        ];
        $filePath = vfsStream::url('root/input.txt');
        file_put_contents($filePath, implode(PHP_EOL, $data));
        $provider = new TransactionFileProvider($filePath);

        $this->expectException(UnsupportedDataStructureException::class);
        iterator_to_array($provider->getList());
    }
}
