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

    public function getRates(array $symbols = []): array
    {
        /* @var ExchangeRateRepository $exchangeRatesRepository */
        $exchangeRatesRepository = $this->entityManager->getRepository(ExchangeRate::class);
        $rates = $exchangeRatesRepository->getLast24hRates($symbols);

        return $rates;
    }

    public function saveRates(array $symbols = []): void
    {
        $rates = $this->exchangeRateDataProvider->getRates($symbols);
        foreach ($rates as $rate) {
            $exchangeRate = new ExchangeRate($rate['symbol'], $rate['price']);
            $this->entityManager->persist($exchangeRate);
        }

        $this->entityManager->flush();
    }
}
