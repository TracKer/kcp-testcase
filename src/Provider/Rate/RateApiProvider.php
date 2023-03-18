<?php

namespace App\Provider\Rate;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class RateApiProvider implements RateProviderInterface
{
    private ?array $rates = null;

    private ClientInterface $client;

    public function __construct()
    {
        $parameters = [];
        $parameters['base_uri'] = 'https://api.apilayer.com/exchangerates_data/';
        $parameters['headers']['apikey'] = getenv('RATE_API_KEY');

        $this->client = new Client($parameters);
    }

    private function updateRates(): void
    {
        $response = $this->client->get('latest');
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Incorrect status code');
        }

        $data = $response->getBody()->getContents();
        $data = json_decode(trim($data), true, flags: JSON_THROW_ON_ERROR);

        $this->rates = $data['rates'];
    }

    public function getRate(string $currency): float
    {
        if ($this->rates === null) {
            $this->updateRates();
        }

        return $this->rates[$currency];
    }
}