<?php

namespace App\Tests\Provider;

use App\Exception\ApiRequestFailureException;
use App\Exception\IncorrectApiStatusCode;
use App\Exception\UnknownCurrencyException;
use App\Exception\UnsupportedDataStructureException;
use App\Provider\Rate\RateApiProvider;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RateApiProviderTest extends TestCase
{
    public function testGetRateReturnsCorrectRate()
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"rates":{"USD":1.23,"EUR":1.45}}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new RateApiProvider($handlerStack);
        $this->assertEquals(1.23, $provider->getRate('USD'));
    }

    public function testGetRateThrowsUnknownCurrencyException()
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"rates":{"USD":1.23,"EUR":1.45}}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new RateApiProvider($handlerStack);
        $this->expectException(UnknownCurrencyException::class);
        $provider->getRate('JPY');
    }

    public function testGetRateThrowsUnsupportedDataStructureException()
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"foo": "bar"}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new RateApiProvider($handlerStack);
        $this->expectException(UnsupportedDataStructureException::class);
        $provider->getRate('USD');
    }

    public function testGetRateThrowsApiRequestFailureException()
    {
        $mockHandler = new MockHandler([
            new Response(404),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new RateApiProvider($handlerStack);
        $this->expectException(ApiRequestFailureException::class);
        $provider->getRate('USD');
    }

    public function testGetRateThrowsIncorrectApiStatusCodeException()
    {
        $mockHandler = new MockHandler([
            new Response(201, [], '{"rates":{"USD":1.23,"EUR":1.45}}'),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $provider = new RateApiProvider($handlerStack);
        $this->expectException(IncorrectApiStatusCode::class);
        $provider->getRate('USD');
    }
}
