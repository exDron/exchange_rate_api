<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Service\DataProvider\ExchangeRateDataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class ExchangeRateService
{
    public function __construct(private ExchangeRateDataProviderInterface $exchangeRateDataProvider, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return ExchangeRate[]
     *
     * @throws \DateMalformedStringException
     */
    public function getLast24hRates(string $symbols): array
    {
        /** @var ExchangeRateRepository $exchangeRatesRepository */
        $exchangeRatesRepository = $this->entityManager->getRepository(ExchangeRate::class);
        $rates = $exchangeRatesRepository->getLast24hRates($symbols);

        return $rates;
    }

    /**
     * @return ExchangeRate[]
     *
     * @throws \DateMalformedStringException
     */
    public function getSelectedDayRates(string $symbols, \DateTimeImmutable $day): array
    {
        /** @var ExchangeRateRepository $exchangeRatesRepository */
        $exchangeRatesRepository = $this->entityManager->getRepository(ExchangeRate::class);
        $rates = $exchangeRatesRepository->getSelectedDayRates($symbols, $day);

        return $rates;
    }

    /**
     * @param array<string> $symbols
     */
    public function saveRates(array $symbols): void
    {
        $rates = $this->exchangeRateDataProvider->getRates($symbols);
        foreach ($rates as $rate) {
            $exchangeRate = new ExchangeRate($rate['symbol'], $rate['price']);
            $this->entityManager->persist($exchangeRate);
        }

        $this->entityManager->flush();
    }
}
