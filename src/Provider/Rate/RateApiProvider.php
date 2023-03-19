<?php

namespace App\Provider\Rate;

use App\Exception\ApiRequestFailureException;
use App\Exception\IncorrectApiStatusCode;
use App\Exception\UnknownCurrencyException;
use App\Exception\UnsupportedDataStructureException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;

class RateApiProvider implements RateProviderInterface
{
    private ?array $rates = null;

    private ClientInterface $client;

    public function __construct(HandlerStack $handlerStack)
    {
        $parameters = [];
        $parameters['handler'] = $handlerStack;
        $parameters['base_uri'] = 'https://api.apilayer.com/exchangerates_data/';
        $parameters['headers']['apikey'] = getenv('RATE_API_KEY');

        $this->client = new Client($parameters);
    }

    /**
     * @return void
     * @throws ApiRequestFailureException
     * @throws IncorrectApiStatusCode
     * @throws UnsupportedDataStructureException
     */
    private function updateRates(): void
    {
        try {
            $response = $this->client->get('latest');
            if ($response->getStatusCode() !== 200) {
                throw new IncorrectApiStatusCode('Incorrect status code');
            }

            $data = $response->getBody()->getContents();
            $data = json_decode(trim($data), true, flags: JSON_THROW_ON_ERROR);

            if (!isset($data['rates'])) {
                throw new \OutOfRangeException();
            }

            $this->rates = $data['rates'];
        } catch (IncorrectApiStatusCode $e) {
            throw $e;
        } catch (\OutOfRangeException|\JsonException $e) {
            throw new UnsupportedDataStructureException('Unsupported data structure of API response', previous: $e);
        } catch (GuzzleException $e) {
            throw new ApiRequestFailureException('API request failed', previous: $e);
        }
    }

    /**
     * @param string $currency
     * @return float
     * @throws ApiRequestFailureException
     * @throws IncorrectApiStatusCode
     * @throws UnknownCurrencyException
     * @throws UnsupportedDataStructureException
     */
    public function getRate(string $currency): float
    {
        if ($this->rates === null) {
            $this->updateRates();
        }

        if (!isset($this->rates[$currency])) {
            throw new UnknownCurrencyException('Unknown currency');
        }

        return $this->rates[$currency];
    }
}
