<?php

namespace App\Provider\BankIdentificationNumber;

use App\Exception\ApiRequestFailureException;
use App\Exception\IncorrectApiStatusCode;
use App\Exception\UnsupportedDataStructureException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;

class BankIdentificationNumberApiProvider implements BankIdentificationNumberProviderInterface

{
    private array $cache = [];

    private ClientInterface $client;

    public function __construct(HandlerStack $handlerStack)
    {
        $parameters = [];
        $parameters['handler'] = $handlerStack;
        $parameters['base_uri'] = 'https://lookup.binlist.net/';

        $this->client = new Client($parameters);
    }

    public function getCountry(string $bin): string
    {
        if (isset($this->cache[$bin])) {
            return $this->cache[$bin];
        }

        try {
            $response = $this->client->get($bin);
            if ($response->getStatusCode() !== 200) {
                throw new IncorrectApiStatusCode('Incorrect status code');
            }

            $data = $response->getBody()->getContents();
            $data = json_decode(trim($data), true, flags: JSON_THROW_ON_ERROR);

            if (!isset($data['country']['alpha2'])) {
                throw new \OutOfRangeException();
            }

            $data = $data['country']['alpha2'];
        } catch (IncorrectApiStatusCode $e) {
            throw $e;
        } catch (\OutOfRangeException|\JsonException $e) {
            throw new UnsupportedDataStructureException('Unsupported data structure of API response', previous: $e);
        } catch (GuzzleException $e) {
            throw new ApiRequestFailureException('API request failed', previous: $e);
        }

        $this->cache[$bin] = $data;
        return $data;
    }
}
