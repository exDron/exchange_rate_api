<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Service\DataProvider\ExchangeRateDataProviderInterface;
use App\Service\ExchangeRateService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ExchangeRateServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $em;
    /** @var ExchangeRateDataProviderInterface&MockObject */
    private ExchangeRateDataProviderInterface $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->provider = $this->createMock(ExchangeRateDataProviderInterface::class);
    }

    public function testGetLast24hRatesReturnsRepositoryData(): void
    {
        $symbols = 'BTCUSDT';

        $repo = $this->createMock(ExchangeRateRepository::class);
        $expected = [new ExchangeRate('BTCUSDT', '50000.00000000')];

        $this->em
            ->expects(self::once())
            ->method('getRepository')
            ->with(ExchangeRate::class)
            ->willReturn($repo);

        $repo
            ->expects(self::once())
            ->method('getLast24hRates')
            ->with($symbols)
            ->willReturn($expected);

        $service = new ExchangeRateService($this->provider, $this->em);
        $actual = $service->getLast24hRates($symbols);

        self::assertSame($expected, $actual);
    }

    public function testGetSelectedDayRatesReturnsRepositoryDataWithProvidedDate(): void
    {
        $symbols = 'ETHUSDT';
        $day = new \DateTimeImmutable('2024-12-31 00:00:00');

        $repo = $this->createMock(ExchangeRateRepository::class);
        $expected = [new ExchangeRate('ETHUSDT', '3000.12345678')];

        $this->em
            ->expects(self::once())
            ->method('getRepository')
            ->with(ExchangeRate::class)
            ->willReturn($repo);

        $repo
            ->expects(self::once())
            ->method('getSelectedDayRates')
            ->with($symbols, $day)
            ->willReturn($expected);

        $service = new ExchangeRateService($this->provider, $this->em);
        $actual = $service->getSelectedDayRates($symbols, $day);

        self::assertSame($expected, $actual);
    }

    public function testSaveRatesPersistsEachProvidedRateAndFlushesOnce(): void
    {
        $symbols = ['BTCUSDT', 'ETHUSDT'];
        $fetched = [
            ['symbol' => 'BTCUSDT', 'price' => '50000.00000000'],
            ['symbol' => 'ETHUSDT', 'price' => '3000.12345678'],
        ];

        $this->provider
            ->expects(self::once())
            ->method('getRates')
            ->with($symbols)
            ->willReturn($fetched);

        // We want to ensure persist() gets called with ExchangeRate entities built from fetched rates
        $this->em
            ->expects(self::exactly(count($fetched)))
            ->method('persist')
            ->with(self::callback(function ($entity) use ($fetched) {
                if (!$entity instanceof ExchangeRate) {
                    return false;
                }
                return array_any($fetched, fn($rate) => $entity->getSymbol() === $rate['symbol'] && $entity->getPrice() === $rate['price']);
            }));

        $this->em
            ->expects(self::once())
            ->method('flush');

        $service = new ExchangeRateService($this->provider, $this->em);
        $service->saveRates($symbols);

        $this->addToAssertionCount(1); // If we reached here, expectations were satisfied
    }
}
