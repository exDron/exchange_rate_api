<?php

namespace App\Tests\Controller;

use App\Entity\ExchangeRate;
use App\Service\ExchangeRateService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ExchangeRateApiControllerTest extends WebTestCase
{
    private function makeRate(string $symbol, string $price): ExchangeRate
    {
        return new ExchangeRate($symbol, $price);
    }

    public function testLast24hSuccess(): void
    {
        $client = static::createClient();

        $mock = $this->createMock(ExchangeRateService::class);
        $mock->expects($this->once())
            ->method('getLast24hRates')
            ->with('EURBTC')
            ->willReturn([
                $this->makeRate('EURBTC', '123.45000000'),
                $this->makeRate('EURBTC', '130.00000000'),
            ]);

        static::getContainer()->set(ExchangeRateService::class, $mock);

        $client->request('GET', '/api/rates/last-24h?pair=BTC/EUR');

        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('datasets', $data);
        self::assertCount(1, $data['datasets']);
        $ds = $data['datasets'][0];
        self::assertSame('BTC/EUR (last 24h)', $ds['label']);
        self::assertArrayHasKey('data', $ds);
        self::assertCount(2, $ds['data']);
        self::assertArrayHasKey('x', $ds['data'][0]);
        self::assertArrayHasKey('y', $ds['data'][0]);
        self::assertIsNumeric($ds['data'][0]['y']);
    }

    public function testLast24hInvalidPairReturns400(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/rates/last-24h?pair=BTCBTC'); // missing '/'

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error', $data);
        self::assertStringContainsString('Invalid pair', $data['error']);
    }

    public function testSelectedDayMissingDateReturns400(): void
    {
        $client = static::createClient();

        // use supported pair to pass pair validation and trigger date validation
        $client->request('GET', '/api/rates/day?pair=BTC/EUR');

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error', $data);
        self::assertSame('Missing date. Expected format YYYY-MM-DD or ISO date', $data['error']);
    }

    public function testSelectedDaySuccess(): void
    {
        $client = static::createClient();

        $mock = $this->createMock(ExchangeRateService::class);
        $mock->expects($this->once())
            ->method('getSelectedDayRates')
            ->with(
                'EURETH',
                $this->callback(fn($d) => $d instanceof \DateTimeImmutable && $d->format('Y-m-d') === '2024-01-15')
            )
            ->willReturn([
                $this->makeRate('EURETH', '2000.00000000'),
            ]);

        static::getContainer()->set(ExchangeRateService::class, $mock);

        $client->request('GET', '/api/rates/day?pair=eth/eur&date=2024-01-15');

        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('datasets', $data);
        self::assertCount(1, $data['datasets']);
        $ds = $data['datasets'][0];
        self::assertSame('ETH/EUR (2024-01-15)', $ds['label']);
        self::assertCount(1, $ds['data']);
        self::assertArrayHasKey('x', $ds['data'][0]);
        self::assertArrayHasKey('y', $ds['data'][0]);
        self::assertIsNumeric($ds['data'][0]['y']);
    }
}
