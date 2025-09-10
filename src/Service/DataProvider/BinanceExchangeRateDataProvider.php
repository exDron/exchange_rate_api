<?php

namespace App\Service\DataProvider;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinanceExchangeRateDataProvider implements ExchangeRateDataProviderInterface
{
    private const string API_URL = 'https://data-api.binance.vision/api/v3/ticker/price';

    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    /**
     * @param string[] $symbols
     *
     * @return array<int, array{symbol: string, price: string}>
     */
    public function getRates(array $symbols): array
    {
        try {
            $response = $this->client->request(
                'GET',
                self::API_URL,
                [
                    'query' => [
                        'symbols' => json_encode($symbols),
                    ],
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Failed to fetch Binance rates', 0, $e);
        }

        $content = $response->toArray();

        return $content;
    }
}
