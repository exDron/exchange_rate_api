<?php

namespace App\Service\DataProvider;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinanceExchangeRateDataProvider implements ExchangeRateDataProviderInterface
{
    private const string API_URL = 'https://data-api.binance.vision/api/v3/ticker/price';

    public function __construct(private readonly HttpClientInterface $client,)
    {
    }

    public function getRates(array $symbols = [], array $intervals = []): array
    {
        try {
            $response = $this->client->request(
                'GET',
                self::API_URL,
                [
                    'query' => [
                        'symbols' => json_encode($symbols),
                    ],
                    'headers' => [
                        'intervalLetter' => 'M',
                        'intervalNum' => 5,
                    ],
                ]
            );
        } catch (TransportExceptionInterface $e) {

        }

        $content = $response->toArray();

        return $content;
    }
}
