<?php

namespace App\Provider\BankIdentificationNumber;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class BankIdentificationNumberApiProvider implements BankIdentificationNumberProviderInterface

{
    private array $cache = [];

    private ClientInterface $client;

    public function __construct()
    {
        $parameters = [];
        $parameters['base_uri'] = 'https://lookup.binlist.net/';

        $this->client = new Client($parameters);
    }

    public function getCountry(string $bin): string
    {
        if (isset($this->cache[$bin])) {
            return $this->cache[$bin];
        }

        $response = $this->client->get($bin);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Incorrect status code');
        }

        $data = $response->getBody()->getContents();
        $data = json_decode(trim($data), true, flags: JSON_THROW_ON_ERROR);
        $data = $data['country']['alpha2'];

        $this->cache[$bin] = $data;
        return $data;
    }
}