<?php

namespace App\Tests\Provider;

use App\Exception\ApiRequestFailureException;
use App\Exception\IncorrectApiStatusCode;
use App\Exception\UnsupportedDataStructureException;
use App\Provider\BankIdentificationNumber\BankIdentificationNumberApiProvider;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class BankIdentificationNumberApiProviderTest extends TestCase
{
    private BankIdentificationNumberApiProvider $binApiProvider;

    public function setUp(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"country": {"alpha2": "US"}}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new BankIdentificationNumberApiProvider($handlerStack);
    }

    public function testGetCountry(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"country": {"alpha2": "US"}}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new BankIdentificationNumberApiProvider($handlerStack);
        $this->assertEquals('US', $provider->getCountry('123456'));
    }

    public function testGetCountryReturnsCachedValue(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"country": {"alpha2": "US"}}'),
            new Response(404),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new BankIdentificationNumberApiProvider($handlerStack);
        $this->assertEquals('US', $provider->getCountry('123456'));
        $this->assertEquals('US', $provider->getCountry('123456'));
    }

    public function testGetCountryThrowsIncorrectApiStatusCode(): void
    {
        $mockHandler = new MockHandler([
            new Response(201, [], '{"country": {"alpha2": "US"}}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new BankIdentificationNumberApiProvider($handlerStack);
        $this->expectException(IncorrectApiStatusCode::class);
        $this->assertEquals('US', $provider->getCountry('123456'));
    }

    public function testGetCountryThrowsUnsupportedDataStructureException(): void
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"foo": "bar"}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new BankIdentificationNumberApiProvider($handlerStack);
        $this->expectException(UnsupportedDataStructureException::class);
        $this->assertEquals('US', $provider->getCountry('123456'));
    }

    public function testGetCountryThrowsApiRequestFailureException(): void
    {
        $mockHandler = new MockHandler([
            new Response(404),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new BankIdentificationNumberApiProvider($handlerStack);
        $this->expectException(ApiRequestFailureException::class);
        $this->assertEquals('US', $provider->getCountry('123456'));
    }
}
